<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShopInfoResource\Pages;
use App\Filament\Resources\ShopInfoResource\RelationManagers;
use App\Models\Shop;
use App\Models\ShopInfo;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShopInfoResource extends Resource
{
    protected static ?string $model = ShopInfo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Thông tin cửa hàng';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('shop_id')
                    ->relationship(name: 'shop', titleAttribute: 'name')
                    ->options(shop::all()->pluck('name', 'id'))
                    ->label('Cửa hàng')
                    ->searchable()
                    ->required(),
                TextInput::make('name_bank')
                    ->label('Tên ngân hàng')
                    ->required(),
                TextInput::make('user_name_bank')
                    ->label('Tên tài khoản')
                    ->required(),
                TextInput::make('number_bank')
                    ->label('Số tài khoản')
                    ->required(),
                TextInput::make('profile_number')
                    ->label('Thông tin')
                    ->columnSpan(2)
                    ->required(),
                FileUpload::make('front_side')
                    ->columnSpan(2)
                    ->required()
                    ->label('Mặt trước chứng minh nhân dân'),
                FileUpload::make('back_side')
                    ->columnSpan(2)
                    ->required()
                    ->label('Mặt sau chứng minh nhân dân'),
                FileUpload::make('portrait_photo')
                    ->columnSpan(2)
                    ->required()
                    ->label('Hình ảnh khuôn mặt'),
                FileUpload::make('national_id')
                    ->columnSpan(2)
                    ->required()
                    ->label('Giấy tùy thân'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('Shop.name')
                    ->label('Tên cửa hàng'),
                TextEntry::make('name_bank')
                    ->label('Tên ngân hàng'),
                TextEntry::make('user_name_bank')
                    ->label('Tên tài khoản'),
                TextEntry::make('number_bank')
                    ->label('Số tài khoản ngân hàng'),
                TextEntry::make('profile_number')
                    ->columnSpan(2)
                    ->label('Thông tin'),
                ImageEntry::make('front_side')
                    ->height(150)
                    ->width(300)
                    ->label('Mặt trước chứng minh nhân dân'),
                ImageEntry::make('back_side')
                    ->height(150)
                    ->width(300)
                    ->label('Mặt sau chứng minh nhân dân'),
                ImageEntry::make('portrait_photo')
                    ->height(150)
                    ->width(300)
                    ->label('Hình ảnh khuôn mặt'),
                ImageEntry::make('national_id')
                    ->height(150)
                    ->width(300)
                    ->label('Giấy tùy thân'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Shop.name')
                    ->searchable()
                    ->label('Tên cửa hàng'),
                TextColumn::make('name_bank')
                    ->label('Tên ngân hàng')
                    ->searchable(),
                TextColumn::make('user_name_bank')
                    ->label('Tên tài khoản')
                    ->searchable(),
                TextColumn::make('number_bank')
                    ->label('Số tài khoản ngân hàng'),
                TextColumn::make('profile_number')
                    ->label('Thông tin'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListShopInfos::route('/'),
            'create' => Pages\CreateShopInfo::route('/create'),
            'edit' => Pages\EditShopInfo::route('/{record}/edit'),
        ];
    }
}
