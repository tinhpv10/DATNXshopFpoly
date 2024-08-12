<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Mail\OrderDeliveredMail;
use App\Mail\OrderNewMail;
use App\Mail\OrderOnHoldMail;
use App\Mail\OrderProcessingMail;
use App\Mail\OrderShippedMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\UserAddress;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

//use App\Filament\Resources\OrderResource\RelationManagers;


class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Đơn hàng';

    protected static ?string $label = 'Đơn hàng';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Order')
                        ->label('Đơn hàng')
                        ->schema([
                            Forms\Components\TextInput::make('code')
                                ->default('OR-' . random_int(100000, 999999))
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->maxLength(32)
                                ->label('Mã đơn hàng')
                                ->unique(Order::class, 'code', ignoreRecord: true),

                            Select::make('user_address_id')
                                ->relationship(name: 'UserAddress', titleAttribute: 'address_specific')
                                ->default(function () {
                                    $address = UserAddress::where('id', true)->first();
                                    return $address ? $address->id : null;
                                })
                                ->label('Địa chỉ đơn hàng')
                                ->createOptionForm([
                                    Forms\Components\Select::make('user_id')
//                                        ->default(function () {
//                                            return auth()->user()->name ?? '';
//                                        })
                                        ->relationship(name: 'user', titleAttribute: 'name')
                                        ->required(),
                                    Forms\Components\TextInput::make('name')
                                        ->required(),
                                    Forms\Components\TextInput::make('phone')
                                        ->required(),
                                    Forms\Components\TextInput::make('address_specific')
                                        ->required()
                                       ->label('Địa chỉ'),
                                ]),


                            TextInput::make('shipping_unit')
                                ->required()
                                ->label('Đơn vị vận chuyển'),

                            Select::make('user_id')
                                ->required()
                                ->relationship(name: 'User', titleAttribute: 'name')
                                ->label('Người dùng'),

                            Select::make('voucher_id')
                                ->required()
                                ->relationship(name: 'Voucher', titleAttribute: 'name')
                                ->label('Mã giảm giá'),

                            ToggleButtons::make('status')
                                ->columnSpanFull()
                                ->required()
                                ->inline()
                                ->options(OrderStatus::class)
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get, $record) {
                                    if ($state === OrderStatus::Cancelled->value) {
                                        $set('cancel_reason', '');
                                    }

                                    if ($state === OrderStatus::New->value) {
//                                        dd(Mail::to($record->user->email));
                                        // Gửi email khi đơn hàng mới được tạo
                                        Mail::to($record->user->email)->send(new OrderNewMail($record));
                                    }

                                    if ($state === OrderStatus::Processing->value) {
                                        // Gửi email khi đơn hàng đang được xử lý
                                        Mail::to($record->user->email)->send(new OrderProcessingMail($record));
                                    }

                                    if ($state === OrderStatus::Shipped->value) {
                                        // Gửi email khi đơn hàng đã được giao
                                        Mail::to($record->user->email)->send(new OrderShippedMail($record));
                                    }

                                    if ($state === OrderStatus::Delivered->value) {
                                        // Gửi email khi đơn hàng đã được giao thành công
                                        Mail::to($record->user->email)->send(new OrderDeliveredMail($record));
                                    }
                                })
                                ->label('Trạng thái đơn hàng'),
                            Textarea::make('cancel_reason')
                                ->label('Lý do hủy đơn hàng')
                                ->visible(fn ($get) => $get('status') === OrderStatus::Cancelled->value)
                                ->required(fn ($get) => $get('status') === OrderStatus::Cancelled->value),
                            Select::make('payment_method_id')
                                ->required()
                                ->relationship(name: 'PaymentMethod', titleAttribute: 'method_name')
                                ->label('Phương thức thanh toán'),
                            Forms\Components\Toggle::make('is_paid')
                                ->label('Đã thanh toán')
                                ->default(false)
                                ->afterStateUpdated(function ($state, callable $set, callable $get, $record) {
                                    if ($state) {
                                        Mail::to($record->user->email)->send(new OrderOnHoldMail($record));
                                    }
                                }),
                            Forms\Components\Toggle::make('on_hold')
                                ->label('Tạm giữ')
                                ->default(false)
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get, $record) {
                                    if ($state) {
                                        Mail::to($record->user->email)->send(new OrderOnHoldMail($record));
                                    }
                                }),
                            TextInput::make('total_price')
                                ->numeric()
//                                ->hidden()
                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters(',')
                                ->label('Tổng tiền'),

                        ])->columns(2),
                    Forms\Components\Wizard\Step::make('Product items')
                        ->label('Chi tiết')
                        ->schema([
                            static::getItemsRepeater(),
                        ])
                ])->columnSpan(['lg' => fn(?Order $record) => $record === null ? 3 : 2]),

            ]);

    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('code')
                    ->badge()
                    ->label('Mã đơn hàng'),
                TextEntry::make('ShippingAddress.name')
                    ->label('Địa chỉ người dùng'),
                TextEntry::make('OrderDetail.Product.name')
                    ->label('Sản phẩm'),
                TextEntry::make('total_price')
                    ->label('Tổng tiền'),
                TextEntry::make('total_price')
                    ->label('Tổng tiền'),
                TextEntry::make('shipping_unit')
                    ->label('Đơn vị vận chuyển'),
                TextEntry::make('User.name')
                    ->label('Người dùng'),
                TextEntry::make('Voucher.name')
                    ->label('Mã giảm giá'),
                TextEntry::make('status')
                    ->label('Trạng thái đơn hàng'),
                TextEntry::make('PaymentMethod.method_name')
                    ->label('Trạng thái đơn hàng'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
//                TextColumn::make('ShippingAddress.street')
//                    ->searchable()
//                    ->label('Địa chỉ người dùng'),
                TextColumn::make('total_price')
                    ->searchable()
                    ->money('VND')
                    ->label('Tổng tiền')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('VND'),
                    ]),
                TextColumn::make('shipping_unit')
                    ->label('Đơn vị vận chuyển'),
                TextColumn::make('User.name')
                    ->label('Người dùng'),
                TextColumn::make('Voucher.name')
                    ->label('Giảm giá'),
                TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->label('Trạng thái đơn hàng'),
                IconColumn::make('on_hold')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-clock')
                    ->label('Tạm giữ'),
                IconColumn::make('is_paid')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-clock')
                    ->label('Đã thanh toán'),

            ])
            ->filters([
                SelectFilter::make('shipping_address_id')
                    ->label('Địa chỉ người dùng')
                    ->relationship(name: 'ShippingAddress', titleAttribute: 'name'),
                SelectFilter::make('status')
                    ->options(OrderStatus::class)
                    ->label('Trạng thái đơn hàng'),
                SelectFilter::make('payment_method_id')
                    ->relationship(name: 'PaymentMethod', titleAttribute: 'method_name')
                    ->label('Phương thức thanh toán'),
            ], layout: FiltersLayout::AboveContent)
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

    public static function getItemsRepeater(): Repeater
    {
        return Repeater::make('OrderDetail')
            ->label('Thêm sản phẩm')
            ->relationship()
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Sản phẩm')
                    ->required()
                    ->options(Product::query()->pluck('name', 'id'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('product_price', Product::find($state)?->regular_price ?? 0))
                    ->distinct()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->columnSpan([
                        'md' => 5,
                    ])
                    ->searchable(),

                Forms\Components\TextInput::make('product_quantity')
                    ->label('Số lượng')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->columnSpan([
                        'md' => 2,
                    ])
                    ->required(),

                Forms\Components\TextInput::make('product_price')
                    ->label('Tổng tiền')
                    ->required()
                    ->disabled()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->suffix('vnđ')
                    ->dehydrated()
                    ->numeric()
                    ->required()
                    ->columnSpan([
                        'md' => 3,
                    ]),
                FileUpload::make('product_image')->columnSpanFull()
                    ->image()
                    ->imageEditor()
                    ->required()
                    ->label('Ảnh sản phẩm'),

            ])
            ->extraItemActions([
                Action::make('openProduct')
                    ->tooltip('Open product')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(function (array $arguments, Repeater $component): ?string {
                        $itemData = $component->getRawItemState($arguments['item']);

                        $product = Product::find($itemData['product_id']);

                        if (! $product) {
                            return null;
                        }

                        return ProductResource::getUrl('edit', ['record' => $product]);
                    }, shouldOpenInNewTab: true)
                    ->hidden(fn (array $arguments, Repeater $component): bool => blank($component->getRawItemState($arguments['item'])['product_id'])),
            ])
            ->orderColumn('sort')
            ->defaultItems(1)
            ->hiddenLabel()
            ->columns([
                'md' => 10,
            ])
            ->required();
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
