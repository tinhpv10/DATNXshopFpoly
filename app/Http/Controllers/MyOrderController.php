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
        $orders = auth()->user()->orders()->with(['OrderDetail.Product.mainMedia','OrderDetail.Product.productVariation'])->get();
        $CancelledReasons = CancelledStatus::cases();
        // Đếm số đơn trong những trạng thái
        $orderCounts = [
            'all' => $orders->count(),
            'Processing' => auth()->user()->orders()->with(['OrderDetail.Product.mainMedia','OrderDetail.Product.productVariation'])->where('status', 'Đang xử lý')->count(),
            'Shipped' => auth()->user()->orders()->with(['OrderDetail.Product.mainMedia','OrderDetail.Product.productVariation'])->where('status', 'Đã vận chuyển')->count(),
            'waitingDelivery' => auth()->user()->orders()->with(['OrderDetail.Product.mainMedia','OrderDetail.Product.productVariation'])->where('status', 'Chờ giao hàng')->count(),
            'Delivered' => auth()->user()->orders()->with(['OrderDetail.Product.mainMedia','OrderDetail.Product.productVariation'])->where('status', 'Đã giao hàng')->count(),
            'canceled' => auth()->user()->orders()->with(['OrderDetail.Product.mainMedia','OrderDetail.Product.productVariation'])->where('status', 'Đã hủy')->count(),
            'refunded' => auth()->user()->orders()->with(['OrderDetail.Product.mainMedia','OrderDetail.Product.productVariation'])->where('status', 'Hoàn tiền')->count(),
        ];

        // -- Phân loại đơn hàng
        // + Chờ thanh toán
        $orderProcessing =  auth()->user()->orders()->with(['OrderDetail.Product.mainMedia','OrderDetail.Product.productVariation'])->where('status', 'Đang xử lý')->get();
        // + Vận chuyển
        $Shipped =  auth()->user()->orders()->with(['OrderDetail.Product.mainMedia','OrderDetail.Product.productVariation'])->where('status', 'Đã vận chuyển')->get();
        // + Chờ giao hàng
        $waitingDelivery = auth()->user()->orders()->with(['OrderDetail.Product.mainMedia','OrderDetail.Product.productVariation'])->where('status', 'Chờ giao hàng')->get();
        // + Hoàn thành
        $Delivered =  auth()->user()->orders()->with(['OrderDetail.Product.mainMedia','OrderDetail.Product.productVariation'])->where('status', 'Đã giao hàng')->get();
        // Đã huỷ
        $canceled = auth()->user()->orders()->with(['OrderDetail.Product.mainMedia','OrderDetail.Product.productVariation'])->where('status', 'Đã hủy bỏ')->get();
//        dd($canceled);
        // Hoàn tiền

        return view('layouts.my_order', compact('user', 'orders', 'CancelledReasons','orderProcessing','orderCounts','Shipped','Delivered','waitingDelivery','canceled'));
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
