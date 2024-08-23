<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryPostResource\Pages;
use App\Filament\Resources\CategoryPostResource\RelationManagers;
use App\Models\CategoryPost;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoryPostResource extends Resource
{
    protected static ?string $model = CategoryPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $label = 'Danh mục bài viết';
    protected static ?string $navigationGroup = 'Bài viết';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Tên danh mục')
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Tên đã tồn tại.',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên danh mục')
                    ->searchable(),
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
            'index' => Pages\ListCategoryPosts::route('/'),
//            'create' => Pages\CreateCategoryPost::route('/create'),
//            'edit' => Pages\EditCategoryPost::route('/{record}/edit'),
        ];
    }
}
