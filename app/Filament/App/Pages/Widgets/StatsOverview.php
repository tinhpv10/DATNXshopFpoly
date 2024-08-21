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

        // Duyệt qua từng trạng thái trong OrderStatus enum
        foreach (OrderStatus::cases() as $status) {
            // Đếm số lượng đơn hàng có status khớp với giá trị enum
            $orderCount = Order::where('status', $status->value)->count();

            // Thêm thẻ thống kê cho từng trạng thái
            $stats[] = Stat::make($status->getLabel(), $orderCount)
                ->description('Số lượng đơn hàng: ' . $status->getLabel())
                ->color($status->getColor());

        }

        return $stats;

    }

    protected function getColumns(): int
    {
        return 4; // Số cột mong muốn trên một hàng
    }
}
