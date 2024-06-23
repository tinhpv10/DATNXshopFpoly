<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\OrderResource\Pages;
use App\Filament\App\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\ProductResource;
use App\Mail\OrderDeliveredMail;
use App\Mail\OrderNewMail;
use App\Mail\OrderOnHoldMail;
use App\Mail\OrderPaidMail;
use App\Mail\OrderProcessingMail;
use App\Mail\OrderShippedMail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\UserAddress;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\IconColumn;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Product;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\RawJs;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Facades\Mail;
use Filament\Infolists\Components\IconEntry;
use Illuminate\Support\Carbon;
use Filament\Infolists\Components\Section;


class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $recordTitleAttribute = 'number';
    protected static ?int $navigationSort = 1;

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
                                ->disabled()
//                                ->default(function () {
//                                    $address = UserAddress::where('id', true)->first();
//                                    return $address ? $address->id : null;
//                                })
                                ->label('Địa chỉ đơn hàng')
                                ->createOptionForm([
                                    Forms\Components\Select::make('user_id')
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
                                ->disabled()
                                ->label('Đơn vị vận chuyển'),

                            Select::make('user_id')
                                ->required()
                                ->disabled()
                                ->relationship(name: 'User', titleAttribute: 'name')
                                ->label('Người dùng'),

                            Select::make('voucher_id')
                                ->required()
                                ->disabled()
                                ->relationship(name: 'Voucher', titleAttribute: 'name')
                                ->label('Mã giảm giá'),

                            Forms\Components\Section::make('Trạng thái')
                                ->description('Gồm có các trạng thái và thanh toán của đơn hàng')
                                ->schema([
                                    ToggleButtons::make('status')
                                        ->columnSpanFull()
                                        ->required()
                                        ->inline()
                                        ->options(OrderStatus::class)
                                        ->reactive()
                                        ->label('Trạng thái đơn hàng')
                                        ->afterStateUpdated(function ($state, callable $set, callable $get, $record) {
                                            if ($state === OrderStatus::Cancelled->value) {
                                                $set('cancel_reason', '');
                                            }

                                            if ($state === OrderStatus::New->value) {
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
                                            $record->status = $state;
                                            $record->save();
                                        }),
                                    ToggleButtons::make('is_paid')
                                        ->columnSpanFull()
                                        ->required()
                                        ->default(false)
                                        ->inline()
                                        ->options(PaymentStatus::class)
                                        ->reactive()
                                        ->label('Trạng thái thanh toán')
                                        ->afterStateUpdated(function ($state, callable $set, callable $get, $record) {
                                            if ($state === PaymentStatus::Paid->value) {
                                                $record->is_paid = true;
                                                $record->on_hold = false;
                                                Mail::to($record->user->email)->send(new OrderPaidMail($record));
                                            } else {
                                                $record->is_paid = false;
                                                $record->on_hold = true;
                                                Mail::to($record->user->email)->send(new OrderOnHoldMail($record));
                                            }
                                            $record->save();
                                        }),
                                    Textarea::make('cancel_reason')
                                        ->label('Lý do hủy đơn hàng')
                                        ->visible(fn($get) => $get('status') === OrderStatus::Cancelled->value)
                                        ->required(fn($get) => $get('status') === OrderStatus::Cancelled->value),
                                ])->columns('1'),

                            Select::make('payment_method_id')
                                ->required()
                                ->disabled()
                                ->relationship(name: 'PaymentMethod', titleAttribute: 'method_name')
                                ->label('Phương thức thanh toán'),

                            TextInput::make('total_price')
                                ->numeric()
                                ->disabled()
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
                Forms\Components\Section::make('Ngày tạo đơn')
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Thời gian tạo đơn')
                            ->content(fn(Order $record): ?string => $record->created_at?->format('Y-m-d H:i:s')),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Thời gian cập nhật')
                            ->content(fn(Order $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn(?Order $record) => $record === null),
            ])->columns(3);


    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Thông tin đơn hàng')
                    ->schema([
                        TextEntry::make('code')
                            ->badge()
                            ->label('Mã đơn hàng'),
                        TextEntry::make('address')
                            ->label('Địa chỉ người dùng'),
                        TextEntry::make('shipping_unit')
                            ->label('Đơn vị vận chuyển'),
                        TextEntry::make('User.name')
                            ->label('Người dùng'),
                        TextEntry::make('Voucher.name')
                            ->label('Mã giảm giá'),
                        IconEntry::make('is_paid')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-badge')
                            ->falseIcon('heroicon-o-x-mark')
                            ->label('Trạng thái thanh toán'),
                        TextEntry::make('status')
                            ->label('Trạng thái đơn hàng'),
                        TextEntry::make('PaymentMethod.method_name')
                            ->label('Phương thức thanh toán'),
                        TextEntry::make('day_paid')
                            ->dateTime('d-m-Y H:i:s')
                            ->label('Ngày giờ thanh toán đơn hàng'),
                        TextEntry::make('created_at')
                            ->dateTime('d-m-Y H:i:s')
                            ->label('Ngày giờ đặt hàng')
                    ])->columns(4),
                Section::make('Sản phẩm trong giỏ hàng')
                    ->schema(function (Model $record) {
                        $OrderDetails = [];

                        // Thêm tiêu đề cột
                        $OrderDetails[] = Section::make('')->schema([
                            TextEntry::make('STT')->label('STT')->columnSpan(1),
                            TextEntry::make('Sản phẩm')->label('Sản phẩm')->columnSpan(1),
                            TextEntry::make('Số lượng')->label('Số lượng')->columnSpan(1),
                            TextEntry::make('Giá')->label('Giá')->columnSpan(1),
                        ])->columns(4);

                        $record->loadMissing('OrderDetail.product');
                        foreach ($record->OrderDetail as $OrderDetail) {
                            $formattedPrice = number_format($OrderDetail->product_price, 1, ',') . ' VND';

                            $OrderDetails[] = Section::make('')->schema([
                                TextEntry::make($OrderDetail->product->id)->columnSpan(1),
                                TextEntry::make($OrderDetail->product->name)->columnSpan(1),
                                TextEntry::make($OrderDetail->product_quantity)->columnSpan(1),
                                TextEntry::make($formattedPrice)->columnSpan(1),
                            ])->columns(4);
                        }
                        $OrderDetails[] = Section::make('')->schema([

                            TextEntry::make('')->columnSpan(1),
                            TextEntry::make('')->columnSpan(1),
                            TextEntry::make('Tổng tiền')->label('Tổng tiền :')->columnSpan(1),
                            TextEntry::make(number_format($record->total_price, 1,',').'VND')->columnSpan(1),
                        ])->columns(4);
                        return $OrderDetails;
                    })->columns(3)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Mã đơn hàng'),
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
                TextColumn::make('total_price')
                    ->searchable()
                    ->money('VND')
                    ->label('Tổng tiền')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('VND'),
                    ]),
                TextColumn::make('created_at')
                    ->label('Đã đặt')
                    ->date('d-m-Y'),
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
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Tạo từ ngày')
                            ->placeholder(fn($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Cho đến ngày')
                            ->placeholder(fn($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Order from ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Order until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
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

    public static function getItemsRepeater(): Repeater
    {
        return Repeater::make('OrderDetail')
            ->label('Thêm sản phẩm')
            ->relationship()
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Sản phẩm')
                    ->disabled()
                    ->required()
                    ->options(Product::query()->pluck('name', 'id'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn($state, Forms\Set $set) => $set('product_price', Product::find($state)?->regular_price ?? 0))
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
                    ->disabled()
                    ->default(1)
                    ->columnSpan([
                        'md' => 2,
                    ])
                    ->required(),

                Forms\Components\TextInput::make('product_price')
                    ->label('Tổng tiền')
                    ->required()
                    ->disabled()
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

                        if (!$product) {
                            return null;
                        }

                        return ProductResource::getUrl('edit', ['record' => $product]);
                    }, shouldOpenInNewTab: true)
                    ->hidden(fn(array $arguments, Repeater $component): bool => blank($component->getRawItemState($arguments['item'])['product_id'])),
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
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
