<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VoucherResource\Pages;
use App\Filament\Resources\VoucherResource\RelationManagers;
use App\Models\Voucher;
use App\Models\VoucherType;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Support\RawJs;
class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Voucher';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên voucher')
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('code')
                            ->label('Mã giảm')
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('max_discount_amount')
                            ->label('Giảm tối đa')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->numeric()
                            ->default(0)
                            ->minValue(1)
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('discount')
                            ->label('Giảm giá')
                            ->numeric()
                            ->default(0)
                            ->minValue(1)
                            ->suffix('%')
                            ->required()
                            ->columnSpan(1),

                        DatePicker::make('start_date')
                            ->label('Ngày bắt đầu')
                            ->required()
                            ->minDate(now())
                            ->columnSpan(1),

                        DatePicker::make('expiration')
                            ->label('Ngày hết hạn')
                            ->afterOrEqual('start_date')
                            ->required()
                            ->columnSpan(1),

                        Select::make('shop_id')
                            ->relationship(name: 'Shop', titleAttribute: 'name', modifyQueryUsing: fn(Builder $query) => $query->where('status', true),)
                            ->searchable()
                            ->required()
                            ->label('Mã nhà bán')
                            ->columnSpan(1),

                        Select::make('voucher_type_id')
                            ->relationship(name: 'VoucherType', titleAttribute: 'name', modifyQueryUsing: fn(Builder $query) => $query->where('status', true),)
                            ->searchable()
                            ->required()
                            ->label('Loại  voucher')
                            ->columnSpan(1),

                        TextInput::make('usage_limit')
                            ->label('Số lần dùng')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required()
                            ->columnSpan(1),

                        Textarea::make('description')
                            ->label('Mô tả')
                            ->required()
                            ->columnSpan(2),


                    ])->columns(2),
            ]);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên voucher')
                    ->searchable(),

                TextColumn::make('code')
                    ->label('Mã giảm'),

                TextColumn::make('usage_limit')
                    ->label('Số lần dùng'),

                TextColumn::make('max_discount_amount')
                    ->label('Giảm tối đa')
                    ->money('VND'),

                TextColumn::make('discount')
                    ->label('Giảm giá')
                    ->suffix('%'),

                TextColumn::make('VoucherType.name')
                    ->label('Loại Voucher'),

                TextColumn::make('Shop.name')
                    ->label('Nhà bán'),
            ])
            ->filters([
                Filter::make('name')
                    ->form([
                        DatePicker::make('start_date')
                            ->label('Ngày bắt đầu'),
                        DatePicker::make('expiration')
                            ->label('ngày kết thúc'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'],
                                fn(Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['expiration'],
                                fn(Builder $query, $date): Builder => $query->whereDate('start_date', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('Shop.name')
                    ->label('Nhà bán'),

                TextEntry::make('VoucherType.name')
                    ->label('Loại voucher'),

                TextEntry::make('name')
                    ->label('Tên voucher'),

                TextEntry::make('code')
                    ->label('Mã giảm'),

                TextEntry::make('start_date')
                    ->label('Ngày bắt đầu')
                    ->date('d/m/Y'),

                TextEntry::make('expiration')
                    ->label('Ngày hết hạn')
                    ->date('d/m/Y'),

                TextEntry::make('discount')
                    ->label('Giảm giá')
                    ->suffix('%'),

                TextEntry::make('max_discount_amount')
                    ->label('Giảm tối đa')
                    ->money('VND'),

                TextEntry::make('usage_limit')
                    ->label('Số lần dùng'),

                TextEntry::make('description')
                    ->label('Mô tả')
                    ->columnSpanFull(),

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
            'index' => Pages\ListVouchers::route('/'),
            'create' => Pages\CreateVoucher::route('/create'),
            'edit' => Pages\EditVoucher::route('/{record}/edit'),
        ];
    }
}
