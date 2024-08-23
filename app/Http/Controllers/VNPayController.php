<?php
namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\UserAddress;
use App\Services\ApService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class VNPayController extends Controller
{
    public function __construct(ApService $apSer)
    {
        $this->middleware('auth');
        $this->apSer = $apSer;
    }

    public function create(Request $request)
    {
        session(['url_prev' => url()->previous()]);

        $vnp_TmnCode = "93QX4E2D";
        $vnp_HashSecret = "ZPKCTSJQALKCIQZQQQQBQHWMLKTWUQGY";
        $vnp_Url = "http://sandbox.vnpayment.vn/paymentv2/vpcpay.html";

        $vnp_Returnurl = route('payment.callback');
        $vnp_TxnRef = uniqid(); // Tạo mã đơn hàng ngẫu nhiên
        $vnp_OrderInfo = "Thanh toán hóa đơn phí dịch vụ";
        $vnp_OrderType = 'billpayment';

        // Lấy tổng tiền và phí vận chuyển từ session
        $totalPayment = session('total_payment', 0);
        $shippingFee = session('shipping_fee', 0);
        $vnp_Amount = ($totalPayment + $shippingFee) * 100; // Cộng phí vận chuyển

        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip(); // Lấy địa chỉ IP của yêu cầu

        $inputData = array(
            "vnp_Version" => "2.0.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if ($request->has('vnp_BankCode') && $request->input('vnp_BankCode') != "") {
            $inputData['vnp_BankCode'] = $request->input('vnp_BankCode');
        }

        ksort($inputData);

        $hashdata = "";
        $query = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . $key . "=" . $value;
            } else {
                $hashdata .= $key . "=" . $value;
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . rtrim($query, '&');

        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= '&vnp_SecureHashType=SHA512&vnp_SecureHash=' . $vnpSecureHash;
        }

        $cartItemIds = Session::get('selectedItems', []);
        // Lấy chi tiết các CartItem từ cơ sở dữ liệu
        $cartItems = CartItem::whereIn('id', $cartItemIds)->get();
        // Tạo đơn hàng mới
        $order = new Order();
        $order->user_id = Auth::id();
        $order->status = OrderStatus::Processing->value;
        $order->total_price = $totalPayment;
        $order->shipping_unit = $shippingFee;
        $order->is_paid = 1;
        $order->shop_id = $cartItems->first()->shop_id ?? null; // Sử dụng shop_id từ giỏ hàng
        $order->payment_method_id = 1;
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

        return redirect($vnp_Url);
    }



    public function paymentCallback(Request $request)
    {
        $url = session('url_prev', '/');

        $ss = Session::get('cost_id');

        if ($request->vnp_ResponseCode == "00") {
            $this->apSer->thanhtoanonline($ss);

            // Xóa các mục đã chọn trong giỏ hàng sau khi thanh toán thành công
            $userId = Auth::id();
            $cartItemIds = Session::get('selectedItems', []);
            if (!empty($cartItemIds)) {
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

                // Xóa session giỏ hàng
                Session::forget('selectedItems');
                Session::forget('total_payment');
                Session::forget('shipping_fee');
            }

            return redirect()->route('checkout.success')->with('success', 'Đơn hàng của bạn đã thanh toán thành công.');
        } else {
            $order = Order::where('id', $ss)->first();
            if ($order) {
                $order->on_hold = '1';
                $order->save();
                return redirect($url)->with('error', 'Chưa thanh toán phí dịch vụ');
            }
        }

        session()->forget('url_prev');
        session()->forget('cost_id');

        return redirect($url)->with('error', 'Lỗi trong quá trình thanh toán phí dịch vụ');
    }


}


