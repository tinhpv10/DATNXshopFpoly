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
        $shopTotals = []; // Mảng để lưu tổng tiền của từng shop
        $shopProductCounts = []; // Mảng để lưu số lượng sản phẩm của từng shop
        $shopShippingFees = []; // Mảng để lưu phí vận chuyển của từng shop

        foreach ($cartData as $itemId) {
            $cartItem = CartItem::find($itemId);
            if ($cartItem) {
                // Lấy thông tin shop_id
                $shopId = $cartItem->shop_id;

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

                // Tính toán giá sản phẩm
                $productPrice = $productStock ? $productStock->retail_price : $product->getPrice();
                $productTotal = $productPrice * $cartItem->quantity;

                // Thêm tổng tiền sản phẩm vào tổng tiền của shop
                if (!isset($shopTotals[$shopId])) {
                    $shopTotals[$shopId] = 0;
                    $shopShippingFees[$shopId] = $this->generateRandomShippingFee(); // Tính phí vận chuyển cho shop
                }
                $shopTotals[$shopId] += $productTotal;

                // Thêm số lượng sản phẩm vào đếm số lượng của shop
                if (!isset($shopProductCounts[$shopId])) {
                    $shopProductCounts[$shopId] = 0;
                }
                $shopProductCounts[$shopId]++; // Đếm số lượng sản phẩm cho shop này

                // Thêm thông tin sản phẩm vào danh sách
                $products[] = [
                    'product' => $product,
                    'productStock' => $productStock,
                    'media' => $cartItem->media,
                    'quantity' => $cartItem->quantity,
                    'price' => $productPrice, // Giá từ biến thể hoặc sản phẩm gốc
                    'variations' => $variations,
                ];
            }
        }

        // Lấy thông tin shop từ danh sách shop_ids
        $shop = Shop::with(['products.productMedia'])
            ->whereIn('id', array_keys($shopTotals)) // Chỉ lấy thông tin các shop có sản phẩm trong giỏ
            ->get();

        // Tính toán tổng tiền hàng và phí vận chuyển
        $totalPayment = array_sum($shopTotals); // Tổng tiền của tất cả các shop
        $totalShippingFee = array_sum($shopShippingFees); // Tổng phí vận chuyển của tất cả các shop

        $paymentmethod = PaymentMethod::all();

        $grandTotal = $totalPayment + $totalShippingFee; // Tổng tiền bao gồm cả phí vận chuyển
        Session::put('totalship',$totalShippingFee);
        // Lưu tổng tiền vào session
        Session::put('grandTotal', $grandTotal);
        return view('layouts.checkout', [
            'shop' => $shop,
            'paymentmethod' => $paymentmethod,
            'address' => $address,
            'products' => $products,
            'totalPayment' => $totalPayment,
            'shippingFee' => $totalShippingFee, // Thay đổi phí vận chuyển trong view
            'shopTotals' => $shopTotals, // Thêm biến này vào view
            'shopProductCounts' => $shopProductCounts, // Thêm biến này vào view để đếm sản phẩm theo shop
            'shopShippingFees' => $shopShippingFees, // Thêm phí vận chuyển của từng shop vào view
        ]);
    }


    private $shippingFee = null;

    private function generateRandomShippingFee()
    {
        if ($this->shippingFee === null) {
            // Đặt phạm vi cho giá trị tối thiểu và tối đa
            $min = 31000;
            $max = 50000;

            // Tạo dãy số chẵn trong khoảng từ $min đến $max
            $evenNumbers = range($min, $max, 2000); // Bước nhảy là 2000 để có số chẵn trong khoảng

            // Chọn một số ngẫu nhiên từ dãy số chẵn
            $this->shippingFee = $evenNumbers[array_rand($evenNumbers)];
        }

        return $this->shippingFee;
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
