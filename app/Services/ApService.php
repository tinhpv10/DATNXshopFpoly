<?php

namespace App\Services;

use App\Models\Order;

class ApService
{
    public function thanhtoanonline($costId)
    {
        $order = Order::where('id', $costId)->first();
        if ($order) {
            $order->is_paid = '1';
            $order->save();
        }
    }
}
