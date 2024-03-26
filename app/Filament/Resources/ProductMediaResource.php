<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductMediaResource\Pages;
use App\Models\ProductMedia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Tables\Columns\IconColumn;


class ProductMediaResource extends Resource
{
    protected static ?string $model = ProductMedia::class;

    protected static ?string $navigationGroup = 'Sản phẩm';

    protected static ?string $label = 'Ảnh sản phẩm';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('media')->columnSpan('full')
                    ->required()
                    ->label('Ảnh')
                    ->image()
                    ->imageEditor(),
                Select::make('product_id')->columnSpan('full')
                    ->required()
                    ->label('Mã sản phẩm')
                    ->relationship(name: 'Product', titleAttribute: 'name'),
                Radio::make('is_main')
                    ->required()
                    ->label('Chọn ảnh chính')
                    ->boolean('Có','Không')
                    ->inline()
                    ->inlineLabel(false)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Product.name')
                    ->label('Mã sản phẩm')
                    ->searchable(),
                ImageColumn::make('media')
                    ->label('Ảnh'),
                IconColumn::make('is_main')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->label('Ảnh chính')

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
            'index' => Pages\ListProductMedia::route('/'),
            'create' => Pages\CreateProductMedia::route('/create'),
            'edit' => Pages\EditProductMedia::route('/{record}/edit'),
        ];
    }
}
