<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // Nếu có yêu cầu POST từ form thanh toán
        if ($request->isMethod('post')) {
            return $this->processCheckout($request);

        }

        // Lấy dữ liệu giỏ hàng từ session
        $cartData = Session::get('selectedItems', []);

        $user_id = Auth::id();
        $address = UserAddress::where('user_id', $user_id)
            ->where('is_default', 1)
            ->first();

        // Kiểm tra nếu $cartData không phải là mảng
        if (!is_array($cartData)) {
            return redirect()->back()->with('error', 'Giỏ hàng không hợp lệ.');
        }

        // Tính toán tổng tiền hàng và chi tiết sản phẩm
        $products = [];
        foreach ($cartData as $itemId) {
            $cartItem = CartItem::find($itemId);
            if ($cartItem) {
                $productStock = $cartItem->productstock;

                if ($productStock) {
                    $product = $productStock->product;

                    // Lấy thông tin biến thể sản phẩm
                    $variations = $productStock->productAttribute()
                        ->with(['appProductVariation', 'appProductVariationValue'])
                        ->get()
                        ->map(function ($attribute) {
                            return [
                                'variation_name' => $attribute->appProductVariation->variation_name ?? 'Không có tên biến thể',
                                'variation_value' => $attribute->appProductVariationValue->variation_value_name ?? 'Không có giá trị biến thể',
                            ];
                        });

                    $products[] = [
                        'product' => $product,
                        'productStock' => $productStock,
                        'media' => $cartItem->media,
                        'quantity' => $cartItem->quantity,
                        'price' => $productStock->retail_price,
                        'variations' => $variations,
                    ];
                }
            }
        }

        // Tính toán tổng tiền hàng và phí vận chuyển
        $totalPayment = $this->calculateTotal($products);
        $shippingFee = 30000; // Phí vận chuyển cố định

        return view('layouts.checkout', [
            'address' => $address,
            'products' => $products,
            'totalPayment' => number_format($totalPayment, 0, ',', '.'),
            'shippingFee' => number_format($shippingFee, 0, ',', '.'),
        ]);
    }

    public function processCheckout(Request $request)
    {
        // Lấy dữ liệu giỏ hàng từ session
        $cartData = Session::get('selectedItems', []);

        // Kiểm tra nếu dữ liệu giỏ hàng không phải là mảng hoặc rỗng
        if (!is_array($cartData) || empty($cartData)) {
            return redirect()->back()->with('error', 'Giỏ hàng không hợp lệ.');
        }

        // Lấy thông tin chi tiết của các sản phẩm trong giỏ hàng
        $cartItems = [];
        foreach ($cartData as $itemId) {
            $cartItem = CartItem::find($itemId);
            if ($cartItem) {
                $cartItems[] = [
                    'product_id' => $cartItem->product_id,
                    'app_product_stock_id' => $cartItem->app_product_stock_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'media' => $cartItem->media,
                    'variations' => json_decode($cartItem->variations, true),
                    'shop_id' => $cartItem->shop_id ?? null,
                ];
            }
        }

        // Lưu vào session
        Session::put('selected_items', $cartItems);
        Session::put('total_payment', $this->calculateTotal($cartItems)); // Thêm tổng tiền vào session
        Session::put('shipping_fee', 30000); // Thêm phí vận chuyển vào session

        // Xử lý thanh toán và chuyển hướng đến VNPay
        return redirect()->route('vnpay.payment');
    }




    public function calculateTotal($products)
    {
        $totalPayment = 0;
        foreach ($products as $item) {
            $totalPayment += $item['price'] * $item['quantity'];

        }
        return $totalPayment;
    }

    public function calculateTotalAjax(Request $request)
    {
        $selectedItems = $request->input('selectedItems', []);
        $totalPayment = 0;

        foreach ($selectedItems as $itemId) {
            $cartItem = CartItem::find($itemId);
            if ($cartItem && $cartItem->productstock) {
                $productStock = $cartItem->productstock;
                $totalPayment += $productStock->retail_price * $cartItem->quantity;
            }
        }

        // Nếu không có sản phẩm nào trong giỏ hàng, trả về lỗi
        if ($totalPayment == 0) {
            return response()->json([
                'error' => 'Không có sản phẩm hợp lệ trong giỏ hàng.'
            ], 400);
        }

        // Sử dụng giá trị mặc định nếu không có trong session
        $shippingFee = 34000; // Hoặc bạn có thể lấy từ session nếu cần
        $totalWithShipping = $totalPayment + $shippingFee;

        return response()->json([
            'totalPayment' => number_format($totalPayment, 0, ',', '.'),
            'totalWithShipping' => number_format($totalWithShipping, 0, ',', '.'),
        ]);
    }

    public function completeCheckout(Request $request)
    {
        // Xử lý lưu trữ đơn hàng khi nhận hàng

        // Trả về kết quả
        return response()->json(['success' => true]);
    }



}
