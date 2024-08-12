<?php

namespace App\Filament\App\Resources\ProductResource\RelationManagers;

use App\Models\AppProductMedia;
use App\Models\AppProductStock;
use App\Models\AppProductVariationValue;
use App\Models\ProductAttribute;
use App\Models\ProductVariation;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProductStockRelationManager extends RelationManager
{
    protected static string $relationship = 'productStock';

    protected static ?string $title = 'Quản lí kho';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->schema(function () {
                        $productVariations = ProductVariation::where('product_id', $this->ownerRecord->id)->get();

                        $fieldsets = [];
                        foreach ($productVariations as $index => $variation) {
                            $fieldsets[] = Fieldset::make("Biến thể {$index}")
                                ->schema([
                                    Select::make("product_variation_id[{$index}]")
                                        ->label('Biến thể')
                                        ->filled()
                                        ->reactive()
                                        ->options(fn(Get $get) => ProductVariation::query()
                                            ->where('product_id', $this->ownerRecord->id)
                                            ->pluck('variation_name', 'id'))
                                        ->live(),
                                    Select::make("app_product_variation_value_id[{$index}]")
                                        ->reactive()
                                        ->label('Giá trị biến thể')
                                        ->filled()
                                        ->options(fn(Get $get) => AppProductVariationValue::query()
                                            ->where('product_variation_id', $get("product_variation_id[{$index}]"))
                                            ->pluck('variation_value_name', 'id'))
                                        ->live(),
                                ]);
                        }
                        return $fieldsets;
                    })
                ,
                Select::make('media')
                    ->required()
                    ->label('Ảnh')
                    ->options(fn(Get $get) => AppProductMedia::query()
                        ->where('product_id', $this->ownerRecord->id)
                        ->get()
                        ->mapWithKeys(fn($item) => [$item->media => $item->name_media]))
                    ->live(),
                TextInput::make('sku')
                    ->required()
                    ->label('Mã SKU'),
                TextInput::make('import_price')
                    ->required()
                    ->label('Giá nhập vào'),
                TextInput::make('retail_price')
                    ->required()
                    ->label('Giá bán lẻ'),
                TextInput::make('wholesale_price')
                    ->required()
                    ->label('Giá bán sỉ'),
                TextInput::make('qty_inventory')
                    ->required()
                    ->label('Số lượng tồn'),
            ]);
    }


    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('media')
                    ->square()
                    ->label('Ảnh'),
                TextColumn::make('productAttribute_name')
                    ->getStateUsing(function (Model $record) {
                        $listProductAttribute = $record->productAttribute;
                        $attributeNames = [];

                        foreach ($listProductAttribute as $attr) {
                            $value = $attr->appProductVariationValue->variation_value_name ?? NULL;
                            $label = $attr->appProductVariationValue->productVariation->variation_name;

                            // Concatenate label and value
                            $attributeNames[] = "{$label} : {$value}";
                        }

                        // Return concatenated string
                        return implode(' , ', $attributeNames);
                    })
                    ->label('Thuộc tính')
                    ->searchable(),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('import_price')
                    ->label('Giá nhập vào')
                    ->searchable(),
                TextColumn::make('retail_price')
                    ->label('Giá bán lẻ')
                    ->searchable(),
                TextColumn::make('wholesale_price')
                    ->label('Giá bán sỉ')
                    ->searchable(),
                TextColumn::make('qty_inventory')
                    ->label('Số lượng tồn')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('Thêm vào kho')
                    ->form([
                        Repeater::make('AppProductVariationValue')
                            ->schema([
                                Section::make('')
                                    ->schema(function () {
                                        $productVariations = ProductVariation::where('product_id', $this->ownerRecord->id)->get();

                                        $fieldsets = [];
                                        foreach ($productVariations as $index => $variation) {
                                            $fieldsets[] = Fieldset::make("Biến thể {$index}")
                                                ->schema([
                                                    Select::make("product_variation_id[{$index}]")
                                                        ->label('Biến thể')
                                                        ->filled()
                                                        ->reactive()
                                                        ->options(fn(Get $get) => ProductVariation::query()
                                                            ->where('product_id', $this->ownerRecord->id)
                                                            ->pluck('variation_name', 'id'))
                                                        ->live(),
                                                    Select::make("app_product_variation_value_id[{$index}]")
                                                        ->reactive()
                                                        ->label('Giá trị biến thể')
                                                        ->filled()
                                                        ->options(fn(Get $get) => AppProductVariationValue::query()
                                                            ->where('product_variation_id', $get("product_variation_id[{$index}]"))
                                                            ->pluck('variation_value_name', 'id'))
                                                        ->live(),
                                                ]);
                                        }
                                        return $fieldsets;
                                    })
                                ,
                                Select::make('media')
                                    ->required()
                                    ->label('Ảnh')
                                    ->options(fn(Get $get) => AppProductMedia::query()
                                        ->where('product_id', $this->ownerRecord->id)
                                        ->pluck('name_media', 'id'))
                                    ->live(),
                                TextInput::make('sku')
                                    ->required()
                                    ->label('Mã SKU'),
                                TextInput::make('import_price')
                                    ->required()
                                    ->label('Giá nhập vào'),
                                TextInput::make('retail_price')
                                    ->required()
                                    ->label('Giá bán lẻ'),
                                TextInput::make('wholesale_price')
                                    ->required()
                                    ->label('Giá bán sỉ'),
                                TextInput::make('qty_inventory')
                                    ->required()
                                    ->label('Số lượng tồn'),
                            ]),

                    ])
                    ->action(function ($data) {
                        $productVariations = ProductVariation::where('product_id', $this->ownerRecord->id)->get();

                        $productId = $productVariations->isEmpty() ? null : $productVariations->first()->product_id;

                        if (!$productId) {
                            return;
                        }

                        foreach ($data['AppProductVariationValue'] as $variationData) {
                            $sku = $variationData['sku'];
                            $importPrice = $variationData['import_price'];
                            $retailPrice = $variationData['retail_price'];
                            $wholesalePrice = $variationData['wholesale_price'];
                            $qtyInventory = $variationData['qty_inventory'];
                            $mediaId = $variationData['media'];

                            // Truy xuất giá trị media từ AppProductMedia
                            $productMedia = AppProductMedia::find($mediaId);
                            $mediaPath = $productMedia ? $productMedia->media : null;

                            // Tạo hoặc cập nhật AppProductStock
                            $productStock = AppProductStock::updateOrCreate(
                                ['sku' => $sku],
                                [
                                    'product_id' => $productId,
                                    'import_price' => $importPrice,
                                    'retail_price' => $retailPrice,
                                    'wholesale_price' => $wholesalePrice,
                                    'qty_inventory' => $qtyInventory,
                                    'media' => $mediaPath,
                                ]
                            );

                            $productStockId = $productStock->id;

                            foreach ($variationData as $key => $value) {
                                if (strpos($key, 'product_variation_id') === 0) {
                                    $index = str_replace(['product_variation_id[', ']'], '', $key);
                                    $productVariationId = $value;
                                    $productVariationValueIdKey = "app_product_variation_value_id[{$index}]";
                                    if (isset($variationData[$productVariationValueIdKey])) {
                                        $productVariationValueId = $variationData[$productVariationValueIdKey];
                                        // Tạo hoặc cập nhật ProductAttribute
                                        $productAttribute = ProductAttribute::updateOrCreate(
                                            [
                                                'variation_id' => $productVariationId,
                                                'app_product_variation_value_id' => $productVariationValueId,
                                                'app_product_stock_id' => $productStockId,
                                            ]
                                        );
                                    }
                                }
                            }
                        }
                    })


            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
