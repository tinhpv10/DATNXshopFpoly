<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\CancelledStatus;

class MyOrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Lấy đơn hàng với thông tin chi tiết
        $orders = $user->orders()->with([
            'OrderDetail.Product.mainMedia',
            'OrderDetail.appProductStock.productAttribute.appProductVariation',
            'OrderDetail.appProductStock.productAttribute.appProductVariationValue'
        ]) ->orderBy('created_at', 'desc')->get();

        // Các trạng thái đơn hàng
        $CancelledReasons = CancelledStatus::cases();

        // Đếm số đơn trong các trạng thái
        $orderCounts = [
            'all' => $orders->count(),
            'Processing' => $user->orders()->where('status', 'Đang xử lý')->count(),
            'Shipped' => $user->orders()->where('status', 'Đã vận chuyển')->count(),
            'waitingDelivery' => $user->orders()->where('status', 'Chờ giao hàng')->count(),
            'Delivered' => $user->orders()->where('status', 'Đã giao hàng')->count(),
            'canceled' => $user->orders()->where('status', 'Đã hủy')->count(),
            'refunded' => $user->orders()->where('status', 'Hoàn tiền')->count(),
        ];

        // Phân loại đơn hàng theo trạng thái
        $orderProcessing = $this->getOrderWithVariations('Đang xử lý');
        $Shipped = $this->getOrderWithVariations('Đã vận chuyển');
        $waitingDelivery = $this->getOrderWithVariations('Thời gian giao hàng');
        $Delivered = $this->getOrderWithVariations('Đã giao hàng');
        $canceled = $this->getOrderWithVariations('Đã hủy bỏ');

        return view('layouts.my_order', compact('user', 'orders', 'CancelledReasons', 'orderProcessing', 'orderCounts', 'Shipped', 'Delivered', 'waitingDelivery', 'canceled'));
    }


    /**
     * Lấy danh sách đơn hàng với các biến thể
     */
    private function getOrderWithVariations($status)
    {
        $orders = auth()->user()->orders()->with([
            'OrderDetail.Product.mainMedia',
            'OrderDetail.appProductStock.productAttribute.appProductVariation',
            'OrderDetail.appProductStock.productAttribute.appProductVariationValue'
        ])
            ->where('status', $status)
            ->get();

        // Duyệt qua các đơn hàng và chi tiết đơn hàng
        foreach ($orders as $order) {
            foreach ($order->OrderDetail as $orderDetail) {
                $productStock = $orderDetail->appProductStock;

                if ($productStock) {
                    $variations = $productStock->productAttribute
                        ->map(function ($attribute) {
                            return [
                                'variation_name' => $attribute->appProductVariation->variation_name ?? 'Không có tên biến thể',
                                'variation_value' => $attribute->appProductVariationValue->variation_value_name ?? 'Không có giá trị biến thể',
                            ];
                        });

                    $orderDetail->variations = $variations;
                }
            }
        }

        return $orders;
    }






    public function updateCancell(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $cancelReason = $request->input('cancelReason');
        $order->status = 'Đã hủy bỏ';
        $order->cancel_reason = $cancelReason;
        // request path user( yêu cầu đường dẫn từ khách hàng)
        $url = $request->path();
        $urlNotId = preg_replace('/\/\d+$/','',$url);
        $order->save();
        if($urlNotId === 'orders/cancel'){
            $order->canceled_by = 'Người Mua';
            $order->save();
        }

        return redirect()->back()->with('success', 'Đơn hàng đã được hủy');
    }
    // layout canceled
    public function showLayoutCanceled($id){
        $user = Auth::user();
        // Đã huỷ
        $orders = auth()->user()->orders()->with(['OrderDetail.Product.mainMedia','OrderDetail.Product.productVariation'])
            ->where('id',$id)
            ->where('status', 'Đã hủy bỏ')
            ->get();
//        dd($orders);
        return view('layouts.cancell_my_order', compact('user','orders'));
    }
}
