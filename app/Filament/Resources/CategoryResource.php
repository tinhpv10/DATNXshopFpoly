<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Danh mục';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('image')
                    ->image()
                    ->label('Ảnh đại diện')
                    ->required()
                    ->columnSpan(2),
                Select::make('category_id')
                    ->relationship(name: 'categories', titleAttribute: 'name')
                    ->label('Thuộc danh mục'),
                TextInput::make('name')
                    ->label('Danh mục')
                    ->required(),
                TextInput::make('category_slug')
                    ->label('Slug Danh mục')
                    ->required(),
                Select::make('shop_id')
                    ->relationship(name: 'shop', titleAttribute: 'name')
                    ->label('Cửa hàng')
                    ->required(),
                TextInput::make('meta_title')
                    ->label('Tiêu đề SEO')
                    ->maxLength(100)
                    ->required(),
                TagsInput::make('meta_keyword')
                    ->label('Từ khóa SEO')
                    ->required(),
                RichEditor::make('meta_description')
                    ->label('Mô tả SEO')
                    ->required()
                    ->columnSpan(2),
                Toggle::make('status')
                    ->label('Trạng thái')
                    ->inline(false),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('image')
                    ->label('Ảnh đại diện'),
                TextEntry::make('category.name')
                    ->label('Thuộc danh mục'),
                TextEntry::make('name')
                    ->label('Danh mục'),
                TextEntry::make('category_slug')
                    ->label('Slug Danh mục'),
                TextEntry::make('Shop.name')
                    ->label('Cửa hàng'),
                TextEntry::make('meta_title')
                    ->label('Tiêu đề SEO'),
                TextEntry::make('meta_description')
                    ->label('Từ khóa SEO'),
                TextEntry::make('meta_keyword')
                    ->label('Từ khóa SEO'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Ảnh đại diện'),
                TextColumn::make('category.name')
                    ->label('Thuộc danh mục')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Danh mục')
                    ->searchable(),
                TextColumn::make('category_slug')
                    ->label('Slug Danh mục')
                    ->searchable(),
                TextColumn::make('Shop.name')
                    ->label('Cửa hàng')
                    ->searchable(),
                ToggleColumn::make('status')
                    ->label('Trạng thái'),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
