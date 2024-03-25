<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Nhà cung cấp';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Tên nhà cung cấp')
                    ->columnSpan(2),
                TextInput::make('address')
                    ->required()
                    ->label('Địa chỉ'),
                TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->regex('/^.+@.+$/i')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Tài khoản đã tồn tại.',
                    ]),
                TextInput::make('phone')
                    ->label('Số điện thoại')
                    ->required()
                    ->maxLength(14)
                    ->tel()
                    ->telRegex('/^0\d{9}$/'),
                TextInput::make('website')
                    ->required()
                    ->url()
                    ->filled()
                    ->prefix('https://')
                    ->label('Link liên kết'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên nhà cung cấp')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email nhà cung cấp')
                    ->icon('heroicon-m-envelope'),
                TextColumn::make('phone')
                    ->label('Số điện thoại')
                    ->searchable(),
                TextColumn::make('address')
                    ->label('Địa chỉ')
                    ->searchable(),
                TextColumn::make('website')
                    ->label('Tên website')
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
