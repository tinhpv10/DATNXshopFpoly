<?php

namespace App\Filament\Resources\VoucherTypeResource\Pages;

use App\Filament\Resources\VoucherTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVoucherType extends EditRecord
{
    protected static string $resource = VoucherTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
