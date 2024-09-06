<?php

namespace App\Filament\App\Pages\Widgets;

use App\Models\Shop;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Auth;

class FollowerShopChart extends ChartWidget
{
    protected static ?string $heading = 'Số lượng người theo dõi';

    protected function getData(): array
    {
        $shopId = Auth::user()->shop_id;

        // Truy vấn số lượng followers cho shop của user hiện tại theo từng ngày
        $data = Trend::query(
            Shop::query()
                ->where('id', $shopId)
        )
            ->between(
                start: now()->startOfYear(),
                end: now(),
            )
            ->perDay() // Thay đổi từ perMonth() thành perDay() để thống kê theo từng ngày
            ->sum('follower'); // Giả sử cột "follower" chứa số lượng followers mỗi ngày

        // Lấy dữ liệu cho ngày hiện tại và ngày trước đó
        $currentDayData = $data->last();
        $previousDayData = $data->slice(-2, 1)->first();

        $percentageChange = 0;
        if ($previousDayData && $currentDayData) {
            $previousFollowers = $previousDayData->aggregate;
            $currentFollowers = $currentDayData->aggregate;

            if ($previousFollowers > 0) {
                $percentageChange = (($currentFollowers - $previousFollowers) / $previousFollowers) * 100;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Số lượng người theo dõi',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => Carbon::parse($value->date)->format('Y-m-d')), // Chuyển đổi thành Carbon
            'percentageChange' => number_format($percentageChange, 2) . '%',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
