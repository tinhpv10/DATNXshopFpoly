<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VoucherTypeResource\Pages;
use App\Filament\Resources\VoucherTypeResource\RelationManagers;
use App\Models\VoucherType;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class VoucherTypeResource extends Resource
{
    protected static ?string $model = VoucherType::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-minus';
    protected static ?string $navigationGroup = 'Đơn hàng';
    protected static ?string $label = 'Loại phiếu giảm giá';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên loại phiếu giảm giá')
                            ->required()
                            ->columnSpan(1),

                        Toggle::make('status')
                            ->inline(false)
                            ->label('Trạng thái')
                            ->default(1)
                            ->columnSpan(2),
                    ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên loại'),

                ToggleColumn::make('status')
                    ->label('Trạng thái'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                DeleteAction::make(),
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
            'index' => Pages\ListVoucherTypes::route('/'),
//            'create' => Pages\CreateVoucherType::route('/create'),
//            'edit' => Pages\EditVoucherType::route('/{record}/edit'),
        ];
    }
}
