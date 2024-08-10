<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
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
        $this->middleware('auth');


        session(['url_prev' => url()->previous()]);

        $vnp_TmnCode = "93QX4E2D";
        $vnp_HashSecret = "ZPKCTSJQALKCIQZQQQQBQHWMLKTWUQGY";
        $vnp_Url = "http://sandbox.vnpayment.vn/paymentv2/vpcpay.html";

        $vnp_Returnurl = route('payment.callback');
        $vnp_TxnRef = uniqid(); // Tạo mã đơn hàng ngẫu nhiên
        $vnp_OrderInfo = "Thanh toán hóa đơn phí dịch vụ";
        $vnp_OrderType = 'billpayment';

        $totalPayment = session('total_payment', 0);
        $shippingFee = session('shipping_fee', 0);
        $vnp_Amount = ($totalPayment + $shippingFee) * 100;

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

        // Tạo đơn hàng mới
        $order = new Order();
        $order->user_id = Auth::id();
        $order->status = OrderStatus::Pending;
        $order->total_price = $totalPayment;
        $order->shipping_unit = 'default_value';
        $order->payment_method_id = 1;
        $order->code = strtoupper(Str::random(10));
        $defaultAddress = UserAddress::where('user_id', Auth::id())->where('is_default', 1)->first();
        if (!$defaultAddress) {
            return redirect()->back()->with('success', 'Không tìm thấy địa chỉ mặc định.');
        }
        $order->user_address_id = $defaultAddress->id;
        $cartItems = Session::get('cart_data', []);
        if (!empty($cartItems)) {
            $shopIds = collect($cartItems)->pluck('shop_id')->unique();
            $order->shop_id = $shopIds->first();
        }
        $order->save();

        session(['cost_id' => $order->id]);

        foreach ($cartItems as $cartItem) {
            $orderDetail = new OrderDetail();
            $orderDetail->order_id = $order->id;
            $orderDetail->product_id = $cartItem['product_id'];
            $orderDetail->product_stock_id = $cartItem['product_stock_id'];
            $orderDetail->product_image = $cartItem['media'];
            $orderDetail->product_price = $cartItem['price'];
            $orderDetail->product_quantity = $cartItem['quantity'];
            $orderDetail->shop_id = $cartItem['shop_id'];
            $orderDetail->save();
        }

        // Xóa session cart sau khi đã lưu chi tiết đơn hàng
        Session::forget('cart');
        Session::forget('cart_data');

        return redirect($vnp_Url);
    }

    public function paymentCallback(Request $request)
    {
        $url = session('url_prev', '/');

        $ss = Session::get('cost_id');

        if ($request->vnp_ResponseCode == "00") {
            $this->apSer->thanhtoanonline($ss);
            return redirect($url)->with('success', 'Đã thanh toán phí dịch vụ');
        } else {
            $order = Order::where('id', $ss)->first();
            if ($order) {
                $order->on_hold = '1';
                $order->save();
                return redirect($url)->with('success', 'chưa thanh toán phí dịch vụ');
            }
        }

        session()->forget('url_prev');
        session()->forget('cost_id');

        return redirect($url)->with('success', 'Lỗi trong quá trình thanh toán phí dịch vụ');
    }

}

