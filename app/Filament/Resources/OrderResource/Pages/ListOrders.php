<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;


class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

//    protected function getHeaderActions(): array
//    {
//        return [
//            Actions\CreateAction::make(),
//        ];
//    }

    public function getTabs(): array
    {
        //self::New => 'Mới',
        //self::Processing => 'Đang xử lý',
        //self::Shipped => 'Đã vận chuyển',
        //self::Delivered => 'Đã giao hàng',
        //self::Cancelled => 'Đã hủy bỏ',
        //self::OnHold => 'Đơn tạm giữ',
        $processing = Order::processingStatus();
        $shipped = Order::shippedStatus();
        $Delivered = Order::deliveredStatus();
        $NotProcessed = Order::NotProcessed();
        return [
//            'Tất cả' => Tab::make('Tất cả'),
            'Chờ duyệt (' . $processing . ')' => Tab::make()->query(fn($query) => $query->where('status', 'Đang xử lý')),
            'Chưa xử lý (' . $NotProcessed . ')' => Tab::make()->query(fn($query) => $query->where('status', 'Chưa xử lý')),
            'Vận chuyển (' . $shipped . ')' => Tab::make()->query(fn($query) => $query->where('status', 'Đã vận chuyển')),
            'Thành công (' . $Delivered . ')' => Tab::make()->query(fn($query) => $query->where('status', 'Đã giao hàng')),
            'Đơn huỷ' => Tab::make()->query(fn($query) => $query->where('status', 'Đã hủy bỏ'))
        ];
    }


}
