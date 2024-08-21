<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $pendingApprovalCount = Product::countPendingApproval();

        return [
            'Chờ duyệt (' . $pendingApprovalCount . ')' => Tab::make()->query(fn($query) => $query->where('pause', 1)),
        ];
    }
}
