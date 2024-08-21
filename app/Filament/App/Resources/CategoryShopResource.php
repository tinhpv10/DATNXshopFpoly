<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\CategoryShopResource\Pages;
use App\Filament\App\Resources\CategoryShopResource\RelationManagers;
use App\Models\CategoryShop;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoryShopResource extends Resource
{
    protected static ?string $model = CategoryShop::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Danh mục';
    protected static ?string $navigationGroup = 'Sản phẩm';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(CategoryShop::query())
            ->columns([
                TextColumn::make('category_full_name')
                    ->label('Danh mục của bạn')
                    ->searchable()
                    ->formatStateUsing(function ($state, $record) {
                        return $record->category_full_name;
                    }),
//                TextColumn::make('shop.name')
//                    ->label('Tên Shop')
//                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
//                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
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
            'index' => Pages\ListCategoryShops::route('/'),
            'create' => Pages\CreateCategoryShop::route('/create'),
            'view' => Pages\ViewCategoryShop::route('/{record}'),
            'edit' => Pages\EditCategoryShop::route('/{record}/edit'),
        ];
    }
}
