<?php

namespace App\Filament\Resources\ProductMediaResource\Pages;

use App\Filament\Resources\ProductMediaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductMedia extends EditRecord
{
    protected static string $resource = ProductMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
