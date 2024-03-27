<?php

namespace App\Filament\Resources\ShopInfoResource\Pages;

use App\Filament\Resources\ShopInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShopInfo extends EditRecord
{
    protected static string $resource = ShopInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
