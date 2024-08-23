<?php

namespace App\Http\Controllers;

use App\Models\AppProductStock;
use App\Models\AppProductVariationValue;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function viewCart(Request $request)
    {
        $cartItems = collect(); // Khởi tạo một collection trống

        if (Auth::check()) {
            $userId = Auth::user()->id;
            $cart = Cart::where('user_id', $userId)->first();

            if ($cart) {
                $cartItems = $cart->items;
            }
        } else {
            // Nếu người dùng chưa đăng nhập, lấy giỏ hàng từ Session
            $cartItems = collect(Session::get('cart', []));
        }

        $totalPrice = 0;

        // Kiểm tra nếu có sản phẩm trong giỏ hàng
        if ($cartItems->isNotEmpty()) {
            // Nhóm sản phẩm theo shop
            $groupedItems = $cartItems->groupBy(function ($item) {
                return $item->product->shop_id;
            });

            // Xử lý logic tính giá và chuẩn bị dữ liệu cho view
            foreach ($groupedItems as $shopId => $items) {
                foreach ($items as $cartItem) {
                    $productStock = $cartItem->productstock;

                    if ($productStock) {
                        // Nếu có biến thể, lấy giá từ biến thể
                        $retailPrice = $productStock->retail_price;
                        $cartItem->price = $retailPrice * $cartItem->quantity;
                        $cartItem->productStock = $productStock;

                        // Lấy thông tin sản phẩm và shop
                        $product = Product::find($cartItem->product_id);
                        $cartItem->product = $product;
                        $cartItem->shop = $product->shop;

                        // Lấy thông tin biến thể
                        $variations = $productStock->productAttribute()
                            ->with(['appProductVariation', 'appProductVariationValue'])
                            ->get()
                            ->map(function ($attribute) {
                                return [
                                    'variation_name' => $attribute->appProductVariation->variation_name ?? 'Không có tên biến thể',
                                    'variation_value' => $attribute->appProductVariationValue->variation_value_name ?? 'Không có giá trị biến thể',
                                ];
                            });

                        $cartItem->variations = $variations;
                    } else {
                        // Nếu không có biến thể, lấy giá từ phương thức getPrice() của sản phẩm
                        $product = Product::find($cartItem->product_id);
                        $cartItem->price = $product->getPrice() * $cartItem->quantity;
                        $cartItem->product = $product;
                        $cartItem->shop = $product->shop;

                        // Không có biến thể nên gán biến thể là rỗng
                        $cartItem->variations = collect();
                    }

                    // Cộng dồn tổng giá
                    $totalPrice += $cartItem->price;
                }
            }

            $totalPayment = $totalPrice;

            // Cập nhật session với dữ liệu giỏ hàng
            Session::put('cart_data', $cartItems->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_stock_id' => $item->product_stock_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'media' => $item->media,
                    'variations' => $item->variations,
                    'shop_id' => $item->shop->id ?? null,
                ];
            }));

            // Đặt tổng tiền và phí vận chuyển vào session
            Session::put('total_payment', $totalPayment);

            $orderCode = 'ORD-' . uniqid();

            return view('layouts.cart', compact('groupedItems', 'totalPrice', 'totalPayment', 'orderCode'));
        }

        // Nếu không có sản phẩm trong giỏ hàng
        return view('layouts.cart', ['groupedItems' => collect(), 'totalPrice' => 0, 'totalPayment' => 0]);
    }


    public function saveSelectedItems(Request $request)
    {
        $selectedItems = $request->input('selectedItems', []);

        // Lưu sản phẩm đã chọn vào session
        session(['selectedItems' => $selectedItems]);

        return response()->json(['success' => true]);
    }



    public function addToCart(Request $request)
    {

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variations' => 'nullable|array',
            'product_image' => 'required|string',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        Log::info('Dữ liệu thêm vào giỏ hàng:', $validated);

        $productId = $validated['product_id'];
        $variations = $validated['variations'] ?? [];
        $productImage = $validated['product_image'];
        $quantity = $validated['quantity'] ?? 1;

        // Kiểm tra xem sản phẩm có biến thể mà người dùng chưa chọn
//        if (empty($variations)) {
//
//            return redirect()->route('cart.view')->with('error', 'Bạn cần chọn ít nhất một biến thể để thêm sản phẩm vào giỏ hàng.');
//        }
        if (Auth::check()) {

            $userId = Auth::user()->id;
            $cart = Cart::firstOrCreate(
                ['user_id' => $userId],
                ['token' => bin2hex(random_bytes(32)), 'status' => 'active']
            );

            $this->addToUserCart($cart->id, $productId, $variations, $productImage, $quantity);
        } else {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.');
        }

        return redirect()->route('cart.view')->with('success', 'Sản phẩm đã được thêm vào giỏ hàng.');
    }


    private function addToUserCart($cartId, $productId, $variations, $productImage, $quantity)
    {
        $product = Product::findOrFail($productId);
        $shopId = $product->shop_id;

        // Kiểm tra nếu không có biến thể hoặc biến thể trống
        if (empty($variations)) {

            // Trường hợp không có biến thể
            // Thay vì tìm trong AppProductStock, chúng ta kiểm tra trực tiếp kho sản phẩm
            if ($variations == []) { // Giả sử `stock` là cột lưu trữ số lượng tồn kho
                $cartItem = CartItem::where('product_id', $productId)
                    ->whereNull('app_product_stock_id') // Chỉ áp dụng khi không có biến thể
                    ->where('cart_id', $cartId)
                    ->first();

                if ($cartItem) {
                    // Cập nhật số lượng nếu đã có sản phẩm trong giỏ
                    $cartItem->quantity += $quantity;
                    $cartItem->save();
                } else {
                    // Lấy giá từ sản phẩm (trường hợp không có biến thể)
                    $productPrice = $product->getPrice(); // Hoặc $product->price

                    // Thêm mới sản phẩm vào giỏ
                    CartItem::create([
                        'cart_id' => $cartId,
                        'product_id' => $productId,
                        'price' => $productPrice,
                        'quantity' => $quantity,
                        'app_product_stock_id' => null, // Không có biến thể nên để null
                        'media' => $productImage,
                        'shop_id' => $shopId,
                    ]);
                }

                return true; // Đánh dấu đã thêm sản phẩm thành công
            }
        } else {
            // Trường hợp có biến thể
            $variationValueIds = $this->getVariationValueIds($variations);
            $matchedStockIds = $this->getMatchedStockIds($variationValueIds);

            foreach ($matchedStockIds as $stockId => $count) {
                if ($count == count($variations)) {
                    $productStock = AppProductStock::find($stockId);

                    if ($productStock) {
                        $cartItem = CartItem::where('product_id', $productId)
                            ->where('app_product_stock_id', $productStock->id)
                            ->where('cart_id', $cartId)
                            ->first();

                        if ($cartItem) {
                            // Cập nhật số lượng nếu đã có sản phẩm trong giỏ
                            $cartItem->quantity += $quantity;
                            $cartItem->save();
                        } else {
                            // Thêm mới sản phẩm vào giỏ
                            CartItem::create([
                                'cart_id' => $cartId,
                                'product_id' => $productId,
                                'price' => $productStock->retail_price,
                                'quantity' => $quantity,
                                'app_product_stock_id' => $productStock->id,
                                'media' => $productImage,
                                'shop_id' => $shopId,
                                'variations' => json_encode($variations),
                            ]);
                        }

                        return true; // Đánh dấu đã thêm sản phẩm thành công
                    }
                }
            }
        }

        return false; // Không tìm thấy sản phẩm
    }


    private function getVariationValueIds($variations)
    {
        $variationValueIds = [];

        foreach ($variations as $variation) {
            $value = $variation['value'];
            $variationValue = AppProductVariationValue::where('variation_value_name', $value)->first();

            if ($variationValue) {
                $variationValueIds[] = $variationValue->id;
            }
        }

        return $variationValueIds;
    }


    private function getMatchedStockIds($variationValueIds)
    {
        $matchedStockIds = [];
        foreach ($variationValueIds as $variationValueId) {
            $productAttributes = ProductAttribute::where('app_product_variation_value_id', $variationValueId)->get();
            foreach ($productAttributes as $productAttribute) {
                if (!isset($matchedStockIds[$productAttribute->app_product_stock_id])) {
                    $matchedStockIds[$productAttribute->app_product_stock_id] = 0;
                }
                $matchedStockIds[$productAttribute->app_product_stock_id]++;
            }
        }
        return $matchedStockIds;
    }
    public function getRetailPrice(Request $request)
    {
        $validated = $request->validate([
            'selectedVariations' => 'required|array',
        ]);

        $selectedVariations = $validated['selectedVariations'];

        if (count($selectedVariations) == 0) {
            return response()->json(['error' => 'Cần chọn ít nhất một biến thể.'], 400);
        }

        $matchedStockIds = $this->getMatchedStockIds($selectedVariations);

        foreach ($matchedStockIds as $stockId => $count) {
            if ($count == count($selectedVariations)) {
                $productStock = AppProductStock::find($stockId);

                if ($productStock) {
                    $retailPriceFormatted = number_format($productStock->retail_price, 0, ',', '.');
                    $media = $productStock->media;
                    return response()->json([
                        'retailPriceFormatted' => $retailPriceFormatted,
                        'media' => $media,
                    ]);
                }
            }
        }

        return response()->json(['error' => 'Không tìm thấy giá và hình ảnh cho biến thể sản phẩm này.'], 404);
    }

    public function updateCartItemMedia(CartItem $cartItem)
    {
        $productStock = AppProductStock::find($cartItem->product_stock_id);

        if ($productStock) {
            $cartItem->media = $productStock->media;
            $cartItem->save();
        }
    }

    public function updateCartItemQuantity(Request $request)
    {
        $cartItemId = $request->input('cartItemId');
        $quantity = $request->input('quantity');

        // Tìm cart item theo ID
        $cartItem = CartItem::find($cartItemId);

        if ($cartItem) {
            // Cập nhật số lượng
            $cartItem->quantity = $quantity;

            // Tính toán giá tiền mới
            if ($cartItem->productStock) {
                // Sản phẩm có biến thể
                $productPrice = $cartItem->productStock->retail_price;
            } else {
                // Sản phẩm không có biến thể
                $productPrice = $cartItem->product->getPrice();
            }

            $newPrice = $quantity * $productPrice;

            // Cập nhật giá mới vào thuộc tính price
            $cartItem->price = $newPrice;

            // Lưu cart item với giá mới
            $cartItem->save();

            // Tính toán tổng giá trị giỏ hàng
            $cart = $cartItem->cart;
            $totalPrice = $cart->items->sum(function ($item) {
                if ($item->productStock) {
                    return $item->quantity * $item->productStock->retail_price;
                } else {
                    return $item->quantity * $item->product->getPrice();
                }
            });


            // Trả về JSON response
            return response()->json([
                'success' => true,
                'cartItemId' => $cartItemId,
                'newPrice' => number_format($newPrice, 0, ',', '.'),
                'totalPrice' => number_format($totalPrice, 0, ',', '.')
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm.']);
    }


    public function removeFromCart($cartItemId)
    {
        if (Auth::check()) {
            // Người dùng đã đăng nhập
            $cartItem = CartItem::findOrFail($cartItemId);
            $cartItem->delete();
        } else {
            // Người dùng chưa đăng nhập
            $cart = Session::get('cart', []);
            foreach ($cart as $key => $item) {
                if ($item['product_id'] == $cartItemId) {
                    unset($cart[$key]);
                    break;
                }
            }

            // Cập nhật giỏ hàng trong phiên
            Session::put('cart', array_values($cart)); // array_values để làm lại chỉ số mảng
        }

        return redirect()->route('cart.view')->with('success', 'Sản phẩm đã được xóa khỏi giỏ hàng.');
    }

    // Đếm số sản phẩm trong giỏ hàng
    public function getCartQuantity()
    {
        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)->first();

        $quantity = 0;
        if ($cart) {
            $quantity = CartItem::where('cart_id', $cart->id)->sum('quantity');
        }

        return response()->json(['quantity' => $quantity]);
    }


}
