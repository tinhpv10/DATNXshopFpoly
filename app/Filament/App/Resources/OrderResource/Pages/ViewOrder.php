<?php

namespace App\Filament\App\Resources\OrderResource\Pages;

use App\Filament\App\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

//    protected function getHeaderActions(): array
//    {
//        return [
//            Actions\EditAction::make(),
//        ];
//    }
    protected function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product Name'),
                TextColumn::make('product.price')
                    ->label('Price'),
                TextColumn::make('product.quantity')
                    ->label('Quantity'),
                TextColumn::make('product.total_price')
                    ->label('Total Price'),
                // Add more columns as needed
            ])
            ->query(function () {
                $orderDetails = OrderDetail::where('order_id', $this->record->id)->get();

                return $orderDetails->map(function ($detail) {
                    return [
                        'product.name' => $detail->product->name,
                        'product.price' => $detail->product_price,
                        'product.quantity' => $detail->product_quantity,
                        'product.total_price' => $detail->product_price * $detail->product_quantity,
                        // Add more fields as needed
                    ];
                });
            });
    }
}
