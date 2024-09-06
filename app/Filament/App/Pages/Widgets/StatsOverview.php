<?php

namespace App\Filament\App\Pages\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = [];

        // Giả sử bạn có thể lấy shop_id hiện tại từ phiên hoặc một biến nào đó
        $currentShopId = auth()->user()->shop_id; // Hoặc cách khác để lấy shop_id hiện tại

        // Duyệt qua từng trạng thái trong OrderStatus enum
        foreach (OrderStatus::cases() as $status) {
            // Đếm số lượng đơn hàng theo shop_id hiện tại và status
            $orderCount = Order::where('shop_id', $currentShopId)
                ->where('status', $status->value)
                ->count();

            // Luôn tạo thống kê, ngay cả khi số lượng đơn hàng là 0
            $stats[] = Stat::make($status->getLabel(), $orderCount)
                ->description("Số lượng đơn hàng: " . $status->getLabel())
                ->color($status->getColor());
        }

        return $stats;
    }

    protected function getColumns(): int
    {
        return 4; // Số cột mong muốn trên một hàng
    }
}
