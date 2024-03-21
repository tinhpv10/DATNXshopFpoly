<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Người dùng';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('avatar')
                    ->columnSpan(2)
                    ->label('Ảnh đại diện'),
                TextInput::make('name')
                    ->required()
                    ->label('Tên tài khoản'),
                TextInput::make('email')
                    ->required()
                    ->regex('/^.+@.+$/i')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Tài khoản đã được đăng kí.',
                    ]),
                TextInput::make('password')
                    ->required()
                    ->label('Mật khẩu')
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->minLength(6)
                    ->maxLength(16)
                    ->password()
                    ->filled()
                    ->unique(ignoreRecord: true)
                    ->autocomplete('new-password'),
                TextInput::make('phone')
                    ->label('SĐT')
                    ->required()
                    ->Length(16)
                    ->regex('/^0\d{9}/'),
                DatePicker::make('birthday')
                    ->label('Ngày sinh'),
                Select::make('gender')
                    ->options([
                        'nam' => 'Nam',
                        'nữ' => 'Nữ',
                        'gay' => 'Giới tính khác',
                    ])
                    ->label('Giới tính'),
                Select::make('user_address_id')
                    ->relationship(name: 'UserAddress', titleAttribute: 'city')
                    ->required()
                    ->label('Địa chỉ'),
                Select::make('shop_id')
                    ->relationship(name: 'shop', titleAttribute: 'name')
                    ->required()
                    ->label('Cửa hàng'),
                TextInput::make('verification_code')
                    ->required()
                    ->label('Mã nhân viên')
                    ->unique()
                    ->regex('/^[a-zA-Z0-9]{6}$/')
                    ->validationMessages([
                        'unique' => 'Mã nhân viên đã được tạo.',
                    ]),
                Select::make('payment_method')
                    ->relationship(name: 'PaymentMethod', titleAttribute: 'method_name')
                    ->required()
                    ->label('Phương thức thanh toán'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name')
                    ->label('Tên tài khoản'),
                TextEntry::make('email')
                    ->label('Email'),
                TextEntry::make('phone')
                    ->label('SĐT'),
                TextEntry::make('birthday')
                    ->label('Ngày sinh'),
                TextEntry::make('gender')
                    ->label('Giới tính'),
                TextEntry::make('user_address_id')
                    ->label('Địa chỉ'),
                TextEntry::make('avatar')
                    ->label('Ảnh đại diện'),
                TextEntry::make('shop_id')
                    ->label('Cửa hàng'),
                TextEntry::make('verification_code')
                    ->label('Mã xác thực'),
                TextEntry::make('payment_method')
                    ->label('Phương thức thanh toán'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->icon('heroicon-m-user-circle')
                    ->label('Tên tài khoản')
                    ->searchable(),
                TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->label('Email'),
                TextColumn::make('phone')
                    ->label('SĐT')
                    ->searchable(),
                TextColumn::make('gender')
                    ->label('Giới tính'),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
