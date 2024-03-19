<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShopResource\Pages;
use App\Filament\Resources\ShopResource\RelationManagers;
use App\Models\Shop;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class ShopResource extends Resource
{
    protected static ?string $model = Shop::class;


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
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
                    ->label('Tên cửa hàng')
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->required(),
                TextInput::make('phone')
                    ->label('Số điện thoại')
                    ->required(),
                TextInput::make('address')
                    ->label('Địa chỉ')
                    ->required(),
                TextInput::make('rating')
                    ->label('Đánh giá')
                    ->required(),
                RichEditor::make('description')
                    ->label('Mô tả')
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
                    ->searchable(),

                ImageColumn::make('avatar')
                    ->label('Avatar ')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Số điện thoại')
                    ->searchable(),

                TextColumn::make('rating')
                    ->label('Đánh giá')
                    ->searchable(),

                TextColumn::make('follower')
                    ->label('Số người theo dõi')
                    ->searchable(),

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
                Tables\Actions\Action::make('Duyệt')
                    ->label('Duyệt')
                    ->color('success')
                    ->modalSubmitActionLabel('Duyệt')
                    ->form([
                        Section::make('Thông tin cửa hàng')
                            ->schema([
                                Placeholder::make('Tên cửa hàng')
                                    ->content(fn($record): string => $record->name),
                                Placeholder::make('Email')
                                    ->content(fn($record): string => $record->email),
                                Placeholder::make('Số điện thoại')
                                    ->content(fn($record): string => $record->phone),
                                Placeholder::make('Địa chỉ')
                                    ->content(fn($record): string => $record->address),
                                Placeholder::make('Đánh giá')
                                    ->content(fn($record): string => $record->rating),
                                Placeholder::make('Mô tả')
                                    ->content(fn($record): string => $record->description)
                                    ->columnSpan(2),
                            ])->columns(2),

                    ])
                    ->action(function (array $data, $record): void {
                        $record->status = 1;
                        $record->save();
                        Notification::make()
                            ->title('Duyệt thành công')
                            ->success()
                            ->send();

                    }),
                Tables\Actions\Action::make('Hủy')
                    ->label('Tạm dừng')
                    ->color('danger')
                    ->action(function (array $data, $record): void {
                        $record->status = 0;
                        $record->save();
                        Notification::make()
                            ->title('Đã tạm dừng')
                            ->success()
                            ->send();

                    }),

                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListShops::route('/'),
            'create' => Pages\CreateShop::route('/create'),
            'edit' => Pages\EditShop::route('/{record}/edit'),
        ];
    }
}
