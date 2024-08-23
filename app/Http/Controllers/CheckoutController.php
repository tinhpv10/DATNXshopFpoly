<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\PaymentMethod;
use App\Models\Shop;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Enums\OrderStatus;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\OrderDetail;
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
                // Kiểm tra nếu sản phẩm có biến thể (productStock)
                $productStock = $cartItem->productstock;

                if ($productStock) {
                    $product = $productStock->product;

                    // Lấy thông tin biến thể sản phẩm (nếu có)
                    $variations = $productStock->productAttribute()
                        ->with(['appProductVariation', 'appProductVariationValue'])
                        ->get()
                        ->map(function ($attribute) {
                            return [
                                'variation_name' => $attribute->appProductVariation->variation_name ?? 'Không có tên biến thể',
                                'variation_value' => $attribute->appProductVariationValue->variation_value_name ?? 'Không có giá trị biến thể',
                            ];
                        });
                } else {
                    // Nếu không có biến thể, lấy trực tiếp thông tin sản phẩm
                    $product = $cartItem->product;
                    $variations = collect(); // Không có biến thể
                }

                // Thêm thông tin sản phẩm vào danh sách
                $products[] = [
                    'product' => $product,
                    'productStock' => $productStock,
                    'media' => $cartItem->media,
                    'quantity' => $cartItem->quantity,
                    'price' => $productStock ? $productStock->retail_price : $product->getPrice(), // Giá từ biến thể hoặc sản phẩm gốc
                    'variations' => $variations,
                ];

            }
        }



        // Tính toán tổng tiền hàng và phí vận chuyển
        $totalPayment = $this->calculateTotal($products);

        $shippingFee = 30000; // Phí vận chuyển cố định
        $paymentmethod = PaymentMethod::all();





        return view('layouts.checkout', [
            'paymentmethod' => $paymentmethod,
            'address' => $address,
            'products' => $products,
            'totalPayment' => $totalPayment,
            'shippingFee' => $shippingFee,
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
//        dd(Session::put('total_payment', $this->calculateTotal($cartItems)));
        Session::put('shipping_fee', 30000); // Thêm phí vận chuyển vào session

        // Xử lý thanh toán và chuyển hướng đến VNPay
        // Trả về URL VNPay qua JSON response
        return response()->json(['redirect_url' => route('vnpay.payment')]);
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

    public function codCheckout(Request $request)
    {
        // Lấy thông tin từ session
        $totalPayment = session('total_payment', 0);
        $shippingFee = session('shipping_fee', 0);

        // Lấy ID của các CartItem từ session
        $cartItemIds = Session::get('selectedItems', []);

        // Kiểm tra nếu không có ID hoặc không phải là mảng
        if (!is_array($cartItemIds) || empty($cartItemIds)) {
            return redirect()->back()->with('error', 'Dữ liệu giỏ hàng không hợp lệ.');
        }

        // Lấy chi tiết các CartItem từ cơ sở dữ liệu
        $cartItems = CartItem::whereIn('id', $cartItemIds)->get();

        // Tạo đơn hàng mới
        $order = new Order();
        $order->user_id = Auth::id();
        $order->status = OrderStatus::Processing->value; // Đang xử lý
        $order->total_price = $totalPayment;
        $order->shipping_unit = $shippingFee;
        $order->is_paid = 0; // Chưa thanh toán
        $order->shop_id = $cartItems->first()->shop_id ?? null; // Lấy shop_id từ item đầu tiên
        $order->payment_method_id = 2; // Giả sử 2 là ID cho phương thức COD
        $order->code = strtoupper(Str::random(10));

        $defaultAddress = UserAddress::where('user_id', Auth::id())->where('is_default', 1)->first();
        if (!$defaultAddress) {
            return redirect()->back()->with('error', 'Không tìm thấy địa chỉ mặc định.');
        }
        $order->user_address_id = $defaultAddress->id;
        $order->save();

        // Lưu chi tiết đơn hàng
        foreach ($cartItems as $cartItem) {
            $orderDetail = new OrderDetail();
            $orderDetail->order_id = $order->id;
            $orderDetail->product_id = $cartItem->product_id;
            $orderDetail->app_product_stock_id = $cartItem->app_product_stock_id;
            $orderDetail->product_image = $cartItem->media;
            $orderDetail->product_price = $cartItem->price;
            $orderDetail->product_quantity = $cartItem->quantity;
            $orderDetail->shop_id = $cartItem->shop_id;
            $orderDetail->save();
        }

        // Xóa các mục đã chọn trong giỏ hàng
        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)->first();
        if ($cart) {
            CartItem::where('cart_id', $cart->id)
                ->whereIn('id', $cartItemIds)
                ->delete();

            // Nếu giỏ hàng không còn sản phẩm, xóa giỏ hàng
            if (CartItem::where('cart_id', $cart->id)->count() == 0) {
                $cart->delete();
            }
        }

        // Xóa session sau khi đã lưu chi tiết đơn hàng
        Session::forget('selectedItems');
        Session::forget('total_payment');
        Session::forget('shipping_fee');

        // Trả về trang thông báo thành công
        return redirect()->route('checkout.success')->with('success', 'Đơn hàng của bạn đã được ghi nhận. Vui lòng thanh toán khi nhận hàng.');
    }



    public function showSucess(){
        return view('layouts.check-sucess');
    }
}
