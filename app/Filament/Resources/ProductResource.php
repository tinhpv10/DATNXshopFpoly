<?php

namespace App\Filament\Resources;

use App\Enums\RatingRole;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Sản phẩm';

    protected static ?string $label = 'Sản phẩm';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('shop_id')
                    ->required()
                    ->relationship(name: 'Shop', titleAttribute: 'name')
                    ->label('Nhà bán'),
                Select::make('supplier_id')
                    ->required()
                    ->searchable()
                    ->relationship(name: 'Supplier', titleAttribute: 'name')
                    ->label('Nhà cung cấp'),
                Select::make('category_id')
                    ->required()
                    ->relationship(name: 'Category' ,titleAttribute: 'name')
                    ->label('Danh mục'),
                Select::make('brand_id')
                    ->required()
                    ->relationship(name: 'Brand', titleAttribute: 'name')
                    ->label('Thương hiệu'),

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
                MarkdownEditor::make('description')->columnSpan('full')
                    ->label('Mô tả'),
                Select::make('rating')
                    ->options(RatingRole::class)
                    ->required()
                    ->label('Đánh giá'),
                TagsInput::make('meta_keyword')
                    ->label('Từ khóa SEO')
                    ->required(),
                TextInput::make('origin')
                    ->label('Nguồn gốc'),
                TextInput::make('meta_title')
                    ->label('Tiêu đề SEO')
                    ->maxLength(100)
                    ->required(),
                MarkdownEditor::make('meta_description')->columnSpan('full')
                    ->label('Mô tả SEO'),

            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('Shop.name')
                    ->label('Nhà bán'),
                TextEntry::make('Supplier.name')
                    ->label('Nhà cung cấp'),
                TextEntry::make('Brand.name')
                    ->label('Thương hiệu'),
                TextEntry::make('Category.name')
                    ->label('Danh mục'),
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
            ->query(Product::query()->where('pause', 1))
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label('Tên sản phẩm'),
                TextColumn::make('Category.name')
                    ->searchable()
                    ->label('Danh mục'),
                TextColumn::make('Brand.name')
                    ->searchable()
                    ->label('Thương hiệu'),
                TextColumn::make('regular_price')
                    ->label('Giá'),
                TextColumn::make('sale_price')
                    ->label('Giá giảm'),
                TextColumn::make('rating')
                    ->label('Đánh giá'),
                TextColumn::make('view_count')
                    ->label('Lượt xem'),
                TextColumn::make('sold_count')
                    ->label('Lượt bán'),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\Action::make('Duyệt')
                    ->label('Duyệt')
                    ->color('success')
                    ->modalSubmitActionLabel('Duyệt')
                    ->form([
                        Section::make('Thông tin cửa hàng')
                            ->schema([
                                Placeholder::make('Tên sản phẩm')
                                    ->content(fn($record): string => $record->name),
                                Placeholder::make('Danh mục')
                                    ->content(fn($record): string => $record->category->parent->name),
                                Placeholder::make('Thương hiệu')
                                    ->content(fn($record): string => $record->Brand->name),
                                Placeholder::make('Shop')
                                    ->content(fn($record): string => $record->Shop->name)
                                    ->columnSpan(2),
                            ])->columns(2),
                    ])
                    ->action(function (array $data, $record): void {
                        $record->pause = 0;
                        $record->save();
                        $shopOwner = $record->shop->user;
                        // gửi thông báo xét duyệt thành công
                        Notification::make()
                            ->title(' Sản phẩm Duyệt thành công')
                            ->icon('heroicon-o-squares-2x2')
                            ->success()
                            ->body('Sản phẩm Đã được xét duyệt : ' . $record->name)
                            ->sendToDatabase($shopOwner);

                    }),

                Tables\Actions\ViewAction::make(),
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make()
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
            RelationManagers\ProductMediaRelationManager::class,
            RelationManagers\ProductStockRelationManager::class,
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
