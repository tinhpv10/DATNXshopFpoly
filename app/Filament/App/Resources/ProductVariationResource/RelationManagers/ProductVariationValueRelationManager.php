<?php

namespace App\Filament\App\Resources\ProductVariationResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductVariationValueRelationManager extends RelationManager
{
    protected static string $relationship = 'appProductVariationValue';
    protected static ?string $label = 'Giá trị biến thể';
    protected static ?string $title = 'Quản biến thể';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('variation_value_name')
                    ->required()
                    ->label('Tên giá trị biến thể'),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('media')
            ->columns([
                TextColumn::make('variation_value_name')
                    ->label('Tên giá trị biến thể')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
