<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductVariationResource\Pages;
use App\Models\ProductVariation;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;


class ProductVariationResource extends Resource
{
    protected static ?string $model = ProductVariation::class;

    protected static ?string $navigationGroup = 'Sản phẩm';
    protected static ?string $label = 'Sản phẩm biến thể';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('variation_name')
                    ->required()
                    ->label('Tên biến thể'),
                Select::make('product_id')
                    ->required()
                    ->label('Mã sản phẩm')
                    ->relationship(name: 'Product', titleAttribute: 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('variation_name')
                    ->label('Tên biến thể')
                    ->searchable(),
                TextColumn::make('Product.name')
                    ->label('Mã sản phẩm')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('product_id')
                    ->label('Mã sản phẩm')
                    ->relationship('Product', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductVariations::route('/'),
            'create' => Pages\CreateProductVariation::route('/create'),
            'edit' => Pages\EditProductVariation::route('/{record}/edit'),
        ];
    }
}
