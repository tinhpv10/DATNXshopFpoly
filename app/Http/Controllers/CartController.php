<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductStock;
use App\Models\ProductVariationValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function viewCart()
    {
        $cartItems = collect();

        if (Auth::check()) {
            $userId = Auth::user()->id;
            $cart = Cart::where('user_id', $userId)->first();

            if ($cart) {
                $cartItems = $cart->items;
            }
        } else {
            $cartItems = Session::get('cart', []);
        }

        $totalPrice = 0;
        $shippingFee = 30000;

        foreach ($cartItems as $cartItem) {
            $productStock = ProductStock::find($cartItem->product_stock_id);

            if ($productStock) {
                $retailPrice = $productStock->retail_price;
                $cartItem->price = $retailPrice * $cartItem->quantity;
                $cartItem->productStock = $productStock;
                $totalPrice += $cartItem->price;

                $product = Product::find($cartItem->product_id);
                $cartItem->product = $product;
                $cartItem->shop = $product->shop;
            } else {
                $cartItem->price = 0;
            }

            $variations = ProductAttribute::select('product_variations.variation_name as variation_name', 'product_variation_values.variation_value_name as variation_value')
                ->join('product_variations', 'product_attributes.variation_id', '=', 'product_variations.id')
                ->join('product_variation_values', 'product_attributes.product_variation_value_id', '=', 'product_variation_values.id')
                ->where('product_attributes.product_stock_id', $cartItem->product_stock_id)
                ->get();

            $cartItem->variations = $variations;
        }

        $totalPayment = $totalPrice + $shippingFee;

        // Lưu thông tin giỏ hàng vào session
        $cartData = $cartItems->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'product_stock_id' => $item->product_stock_id,
                'quantity' => $item->quantity,
                'media' => $item->media,
                'price' => $item->price,
                'variations' => $item->variations,
                'shop_id' => $item->shop->id ?? null, // Đảm bảo rằng shop_id luôn tồn tại
            ];
        });

        Session::put('cart_data', $cartData);
        Session::put('total_payment', $totalPayment);
        Session::put('shipping_fee', $shippingFee);

        $orderCode = 'ORD-' . uniqid();
        return view('layouts.cart', compact('cartItems', 'totalPrice', 'shippingFee', 'totalPayment', 'orderCode'));
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variations' => 'required|array',
            'product_image' => 'required|string',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $productId = $validated['product_id'];
        $variations = $validated['variations'];
        $productImage = $validated['product_image'];
        $quantity = $validated['quantity'] ?? 1;

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
        $variationValueIds = $this->getVariationValueIds($variations);
        $product = Product::findOrFail($productId);
        $shopId = $product->shop_id;

        $matchedStockIds = $this->getMatchedStockIds($variationValueIds);

        foreach ($matchedStockIds as $stockId => $count) {
            if ($count == count($variations)) {
                $productStock = ProductStock::find($stockId);

                if ($productStock) {
                    $cartItem = CartItem::where('product_id', $productId)
                        ->where('product_stock_id', $productStock->id)
                        ->where('cart_id', $cartId)
                        ->first();

                    if ($cartItem) {

                        $cartItem->quantity += $quantity;
                        $this->updateCartItemMedia($cartItem);
                        $cartItem->save();
                    } else {

                        $newCartItem = CartItem::create([
                            'cart_id' => $cartId,
                            'product_id' => $productId,
                            'price' => $productStock->retail_price,
                            'quantity' => $quantity,
                            'product_stock_id' => $productStock->id,
                            'media' => $productImage,
                            'shop_id' => $shopId,
                            'variations' => json_encode($variations),
                        ]);
                    }

                    return redirect()->route('cart.view')->with('success', 'Sản phẩm đã được thêm vào giỏ hàng.');
                }
            }
        }

        return redirect()->back()->with('error', 'Không tìm thấy sản phẩm biến thể.');
    }


    private function getVariationValueIds($variations)
    {
        $variationValueIds = [];

        foreach ($variations as $variation) {
            $value = $variation['value'];
            $variationValue = ProductVariationValue::where('variation_value_name', $value)->first();

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
            $productAttributes = ProductAttribute::where('product_variation_value_id', $variationValueId)->get();
            foreach ($productAttributes as $productAttribute) {
                if (!isset($matchedStockIds[$productAttribute->product_stock_id])) {
                    $matchedStockIds[$productAttribute->product_stock_id] = 0;
                }
                $matchedStockIds[$productAttribute->product_stock_id]++;
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
                $productStock = ProductStock::find($stockId);

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
        $productStock = ProductStock::find($cartItem->product_stock_id);

        if ($productStock) {
            $cartItem->media = $productStock->media;
            $cartItem->save();
        }
    }

    public function updateQuantity(Request $request, $cartItemId)
    {
        $cartItem = CartItem::findOrFail($cartItemId);

        $quantity = $request->input('quantity', 1);
        $cartItem->quantity = $quantity;
        $cartItem->save();

        return redirect()->route('cart.view')->with('success', 'Cập nhật số lượng sản phẩm thành công.');
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

