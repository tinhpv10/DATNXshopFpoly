<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VoucherTypeResource\Pages;
use App\Filament\Resources\VoucherTypeResource\RelationManagers;
use App\Models\VoucherType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class VoucherTypeResource extends Resource
{
    protected static ?string $model = VoucherType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Loại voucher';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên loại voucher')
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
