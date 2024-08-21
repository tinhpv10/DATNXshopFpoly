<?php

namespace App\Filament\Resources\ShopInfoResource\Pages;

use App\Filament\Resources\ShopInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShopInfos extends ListRecords
{
    protected static string $resource = ShopInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
