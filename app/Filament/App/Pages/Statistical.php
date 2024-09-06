<?php

namespace App\Filament\App\Pages;

use App\Filament\App\Pages\Widgets\FollowerShopChart;
use App\Filament\App\Pages\Widgets\StatisticalChart;
use App\Filament\App\Pages\Widgets\StatsOverview;
use Filament\Pages\Page;


class Statistical extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.app.pages.statistical';
    protected static ?string $title = 'Phân tích bán hàng';

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class, // Gọi widget StatsOverview
            StatisticalChart::class,
            FollowerShopChart::class,// Gọi widget StatisticalChart
        ];
    }
}
