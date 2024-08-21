<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ShopResource\Pages;
use App\Filament\App\Resources\ShopResource\RelationManagers;
use App\Models\Shop;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class ShopResource extends Resource
{
    protected static ?string $model = Shop::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationGroup = 'Cửa hàng';
    protected static ?string $label = 'Cửa hàng';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('avatar')
                    ->label('Avatar cửa hàng')
                    ->image()
                    ->imageEditor()
                    ->required()
                    ->columnSpan(2),
                TextInput::make('name')
                    ->maxLength(110)
                    ->label('Tên cửa hàng')
                    ->required()
                    ->validationMessages([
                        'maxLength' => 'Tên shop chỉ giới hạn trong 200 ký tự'
                    ]),
//                TextInput::make('email')
//                    ->label('Email')
//                    ->required(),
                TextInput::make('phone')
                    ->label('Số điện thoại')
                    ->required(),
                TextInput::make('address')
                    ->label('Địa chỉ')
                    ->required(),
//                TextInput::make('follower')
//                    ->label('Người theo dỏi   ')
//                    ->required()
//                    ->numeric()
//                    ->rules('min:0'),
//                TextInput::make('rating')
//                    ->label('Đánh giá')
//                    ->required(),
                RichEditor::make('description')
                    ->label('Mô tả')
                    ->required()
                    ->columnSpan(2),
//                Toggle::make('status')
//                    ->label('Trạng thái')
//                    ->inline(false),


            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name')
                    ->label('Tên cửa hàng'),

                TextEntry::make('email')
                    ->label('Email'),

                TextEntry::make('phone')
                    ->label('Số điện thoại'),

                TextEntry::make('address')
                    ->label('Địa chỉ'),

                TextEntry::make('rating')
                    ->label('Đánh giá'),

                TextEntry::make('follower')
                    ->label('Số người theo dõi'),

                TextEntry::make('description')
                    ->label('Mô tả')
                    ->columnSpanFull(),

            ]);

    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên cửa hàng')
                   ,

                ImageColumn::make('avatar')
                    ->label('Avatar ')
                    ,

                TextColumn::make('email')
                    ->label('Email')
                    ,

                TextColumn::make('phone')
                    ->label('Số điện thoại')
                   ,

                TextColumn::make('rating')
                    ->label('Đánh giá')
                    ,

                TextColumn::make('follower')
                    ->label('Số người theo dõi')
                    ,

                IconColumn::make('status')
                    ->label('Trạng thái')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-clock'),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);



    }


    public static function getRelations(): array
    {
        return [
            RelationManagers\ShopInfoRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShops::route('/'),
//            'create' => Pages\CreateShop::route('/create'),
            'view' => Pages\ViewShop::route('/{record}'),
            'edit' => Pages\EditShop::route('/{record}/edit'),
        ];
    }
}
