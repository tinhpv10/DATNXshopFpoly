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
        $shippingFee = 30000;

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
                        $retailPrice = $productStock->retail_price;
                        $cartItem->price = $retailPrice * $cartItem->quantity;
                        $cartItem->productStock = $productStock;
                        $totalPrice += $cartItem->price;

                        $product = Product::find($cartItem->product_id);
                        $cartItem->product = $product;
                        $cartItem->shop = $product->shop;

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
                        $cartItem->price = 0;
                    }
                }
            }

            $totalPayment = $totalPrice + $shippingFee;

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
            Session::put('shipping_fee', $shippingFee);

            $orderCode = 'ORD-' . uniqid();

            return view('layouts.cart', compact('groupedItems', 'totalPrice', 'shippingFee', 'totalPayment', 'orderCode'));
        }

        // Nếu không có sản phẩm trong giỏ hàng
        return view('layouts.cart', ['groupedItems' => collect(), 'totalPrice' => 0, 'shippingFee' => $shippingFee, 'totalPayment' => 0]);
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
            'variations' => 'required|array',
            'product_image' => 'required|string',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        Log::info('Dữ liệu thêm vào giỏ hàng:', $validated);

        $productId = $validated['product_id'];
        $variations = $validated['variations'];
        $productImage = $validated['product_image'];
        $quantity = $validated['quantity'] ?? 1;

        // Kiểm tra xem sản phẩm có biến thể mà người dùng chưa chọn
        if (empty($variations)) {

            return redirect()->route('cart.view')->with('error', 'Bạn cần chọn ít nhất một biến thể để thêm sản phẩm vào giỏ hàng.');
        }

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
        // Lấy giá trị biến thể
        $variationValueIds = $this->getVariationValueIds($variations);
        $product = Product::findOrFail($productId);
        $shopId = $product->shop_id;

        // Nếu không có biến thể, bỏ qua bước tìm kiếm dựa trên biến thể
        if (empty($variationValueIds)) {
            $productStock = AppProductStock::where('product_id', $productId)->first();

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
        } else {
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
            $cartItem->save();

            // Tính lại giá tiền dựa trên số lượng
            $newPrice = $cartItem->quantity * $cartItem->productStock->retail_price;

            // Lấy tất cả các mục trong giỏ hàng thông qua cart_id
            $cart = $cartItem->cart;
            $totalPrice = $cart->items->sum(function($item) {
                return $item->quantity * $item->productStock->retail_price;
            });

            // Trả về JSON response
            return response()->json([
                'success' => true,
                'newPrice' => number_format($newPrice, 0, ',', '.') . ' đ',
                'totalPrice' => number_format($totalPrice, 0, ',', '.') . ' đ'
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


}
