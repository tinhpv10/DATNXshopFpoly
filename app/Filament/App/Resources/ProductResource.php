<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ProductResource\Pages;
use App\Filament\App\Resources\ProductResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Sản phẩm';

    protected static ?string $label = 'Sản phẩm';
    // Chức năng search ( thanh tìm kiếm )
    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        // Đếm số lượng sản phẩm có cùng shop_id
        $count = static::getModel()::where('shop_id', $user->shop_id)->count();
        return (string) $count; // Trả về số lượng đơn hàng
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('supplier_id')
                    ->required()
                    ->searchable()
                    ->relationship('Supplier','name',function ($query){
                        // lấy ra những nhà cung cấp của chính shop đó
                       $shopId = Auth::user()->shop_id;
                       return $query->where('shop_id',$shopId);
                    })
                    ->label('Nhà cung cấp'),
                Select::make('category_id')
                    ->options(function () {
                        $categories = Category::whereNull('parent_id')->with('children')->get();
                        $options = [];

                        // Hàm đệ quy để lấy danh mục con
                        $addChildrenToOptions = function ($categories, $indentation = '') use (&$options, &$addChildrenToOptions) {
                            foreach ($categories as $category) {
                                // Thêm thụt lề vào danh mục
                                $options[$category->id] = $indentation . $category->name;
                                if ($category->children) {
                                    // Thêm một khoảng thụt lề cho các cấp con
                                    $newIndentation = $indentation . '_ '; // Thêm khoảng trắng để thụt lề
                                    $addChildrenToOptions($category->children, $newIndentation);
                                }
                            }
                        };

                        // Gọi hàm đệ quy với danh mục cấp 1
                        $addChildrenToOptions($categories);

                        return $options;
                    })

                    ->searchable()
                    ->label('Thuộc danh mục'),
                Select::make('brand_id')
                    ->required()
                    // lấy những thương hiệu của shop đó
                    ->relationship('Brand', 'name', function ($query){
                        $shopId = Auth::user()->shop_id;
                        return $query->where('shop_id',$shopId);
                    })
                    ->searchable()
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
                    ->badge()
                    ->separator(',')
                    ->money('VND')
                    ->label('Giá'),
                TextEntry::make('sale_price')
                    ->badge()
                    ->separator(',')
                    ->money('VND')
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
                    ->badge()
                    ->separator(',')
                    ->money('VND')
                    ->label('Giá'),
                TextColumn::make('sale_price')
                    ->badge()
                    ->separator(',')
                    ->money('VND',)
                    ->label('Giá giảm'),
                TextColumn::make('rating')
                    ->label('Đánh giá'),
                TextColumn::make('view_count')
                    ->label('Lượt xem'),
                TextColumn::make('sold_count')
                    ->label('Lượt bán'),
            ])
            ->filters([
                SelectFilter::make('supplier_id')
                    ->label('Nhà cung cấp')
                    ->relationship('Supplier', 'name'),
                SelectFilter::make('Brand_id')
                    ->label('Thương hiệu')
                    ->relationship('Brand', 'name'),
                SelectFilter::make('category_id')
                    ->label('Danh Mục')
                    ->relationship('Category', 'name'),
            ])
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
