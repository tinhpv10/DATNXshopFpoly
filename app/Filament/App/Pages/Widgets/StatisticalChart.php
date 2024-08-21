<?php

namespace App\Filament\App\Pages\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Auth;


class StatisticalChart extends ChartWidget
{
    protected static ?string $heading = 'Doanh thu';


    protected function getData(): array
    {
        $shopId = Auth::user()->shop_id;
        $data = Trend::query(
            Order::query()
                ->where('shop_id', $shopId)
                ->whereNotNull('total_price')
        )
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('total_price');

        $currentMonthData = $data->last();
        $previousMonthData = $data->slice(-2, 1)->first();

        $percentageChange = 0;
        if ($previousMonthData && $currentMonthData) {
            $previousRevenue = $previousMonthData->aggregate;
            $currentRevenue = $currentMonthData->aggregate;

            if ($previousRevenue > 0) {
                $percentageChange = (($currentRevenue - $previousRevenue) / $previousRevenue) * 100;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Doanh thu',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
            'percentageChange' => number_format($percentageChange, 2) . '%',
        ];
    }


    protected function getType(): string
    {
        return 'line';
    }
}
