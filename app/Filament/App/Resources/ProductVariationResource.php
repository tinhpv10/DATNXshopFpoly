<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ProductVariationResource\Pages;
use App\Filament\App\Resources\ProductVariationResource\RelationManagers\ProductVariationValueRelationManager;
use App\Models\AppProductVariation;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProductVariationResource extends Resource
{
    protected static ?string $model = AppProductVariation::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';
    protected static ?string $navigationGroup = 'Sản phẩm';
    protected static ?string $label = 'Biến thể';
    // Chức năng search ( thanh tìm kiếm )
    protected static ?string $recordTitleAttribute = 'variation_name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('product_id')
                    ->required()
                    ->searchable()
                    ->label('Sản phẩm')
                    ->relationship('Product','name',function ($query){
                        // lấy ra những sản phẩm của chính shop đó
                        $shopId = Auth::user()->shop_id;
                        return $query->where('shop_id',$shopId);
                    }),
                TextInput::make('variation_name')
                    ->required()
                    ->label('Tên biến thể'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Product.name')
                    ->label('Sản phẩm')
                    ->searchable(),
                TextColumn::make('variation_name')
                    ->label('Tên biến thể')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('product_id')
                    ->label('Sản phẩm')
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::check()) {
            $shopId = Auth::user()->shop_id;
            return $query->where('shop_id', $shopId);
        }

        return $query;
    }
    public static function getRelations(): array
    {
        return [
            ProductVariationValueRelationManager::class
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
