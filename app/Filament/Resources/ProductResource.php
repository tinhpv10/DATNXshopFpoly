<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Forms\Components\Select;
use Filament\Support\RawJs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;



class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Sản phẩm';

    protected static ?string $label = 'Sản phẩm';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('supplier_id')
                    ->required()
                    ->relationship(name: 'Supplier', titleAttribute: 'name')
                    ->label('Nhà cung cấp'),
                Select::make('category_id')
                    ->required()
                    ->relationship(name: 'Category' ,titleAttribute: 'name')
                    ->label('Danh mục'),
                Select::make('shop_id')
                    ->required()
                    ->relationship(name: 'Shop', titleAttribute: 'name')
                    ->label('Nhà bán'),
                TextInput::make('name')
                    ->required()
                    ->label('Tên sản phẩm'),
                TextInput::make('slug')
                    ->prefix('https://')
                    ->suffix('.com')
                    ->label('Đường dẫn SP'),
                TextInput::make('regular_price')
                    ->required()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->suffix('vnđ')
                    ->label('Giá'),
                TextInput::make('sale_price')
                    ->required()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->suffix('vnđ')
                    ->label('Giá giảm'),
                TextInput::make('sku')
                    ->required()
                    ->label('Mã SKU'),
                Hidden::make('view_count')
                    ->label('Lượt xem'),
                Hidden::make('sold_count')
                    ->label('Lượt bán'),
                RichEditor::make('description')->columnSpan('full')
                    ->label('Mô tả'),
                TextInput::make('rating')
                    ->numeric()
                    ->label('Đánh giá'),
                TextInput::make('meta_keyword')
                    ->label('Từ khóa SEO'),
                TextInput::make('origin')
                    ->label('Nguồn gốc'),
                TextInput::make('meta_title')
                    ->label('Tiêu đề SEO'),
                RichEditor::make('meta_description')->columnSpan('full')
                    ->label('Mô tả SEO'),

            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('Supplier.name')
                    ->label('Nhà cung cấp'),
                TextEntry::make('Category.name')
                    ->label('Danh mục'),
                TextEntry::make('Shop.name')
                    ->label('Nhà bán'),
                TextEntry::make('name')
                    ->label('Tên sản phẩm'),
                TextEntry::make('slug')
                    ->label('Đường dẫn SP'),
                TextEntry::make('regular_price')
                    ->label('Giá'),
                TextEntry::make('sale_price')
                    ->label('Giá giảm'),
                TextEntry::make('sku')
                    ->label('Mã SKU'),
                TextEntry::make('rating')
                    ->label('Đánh giá'),
                TextEntry::make('view_count')
                    ->label('Lượt xem'),
                TextEntry::make('sold_count')
                    ->label('Lượt bán'),
                TextEntry::make('description')
                    ->label('Mô tả'),
                TextEntry::make('origin')
                    ->label('Nguồn gốc'),
                TextEntry::make('meta_title')
                    ->label('Tiêu đề SEO'),
                TextEntry::make('meta_description')
                    ->label('Mô tả SEO'),
                TextEntry::make('meta_keyword')
                    ->label('Từ khóa SEO'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Supplier.name')
                    ->searchable()
                    ->label('Nhà cung cấp'),
                TextColumn::make('Category.name')
                    ->searchable()
                    ->label('Danh mục'),
                TextColumn::make('Shop.name')
                    ->searchable()
                    ->label('Nhà bán'),
                TextColumn::make('name')
                    ->searchable()
                    ->label('Tên sản phẩm'),
                TextColumn::make('slug')
                    ->label('Đường dẫn SP'),
                TextColumn::make('regular_price')
                    ->label('Giá'),
                TextColumn::make('sale_price')
                    ->label('Giá giảm'),
                TextColumn::make('sku')
                    ->label('Mã SKU'),
                TextColumn::make('rating')
                    ->label('Đánh giá'),
                TextColumn::make('view_count')
                    ->label('Lượt xem'),
                TextColumn::make('sold_count')
                    ->label('Lượt bán'),
                TextColumn::make('description')
                    ->label('Mô tả'),
                TextColumn::make('origin')
                    ->label('Nguồn gốc'),
                TextColumn::make('meta_title')
                    ->searchable()
                    ->label('Tiêu đề SEO'),
                TextColumn::make('meta_description')
                    ->label('Mô tả SEO'),
                TextColumn::make('meta_keyword')
                    ->searchable()
                    ->label('Từ khóa SEO'),

            ])
            ->filters([
                SelectFilter::make('supplier_id')
                    ->label('Nhà cung cấp')
                    ->relationship('Supplier', 'name'),
                SelectFilter::make('category_id')
                    ->label('Danh Mục')
                    ->relationship('Category', 'name'),
                SelectFilter::make('shop_id')
                    ->label('Nhà bán')
                    ->relationship('Shop', 'name'),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
