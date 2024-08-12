<?php


namespace App\Enums;


use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasLabel, HasIcon, HasColor
{
    case New = 'Mới';
    case Processing = 'Đang xử lý';
    case Shipped = 'Đã vận chuyển';
    case Delivered = 'Đã giao hàng';
    case Waitingdelivery = 'Chờ lấy hàng';
    case Cancelled = 'Đã hủy bỏ';
    const OnHold = 'Đơn tạm giữ';
    case Successprocessed = 'Đã xử lý';
    case NotProcessed = 'Chưa xử lý';
    case Pending = 'Pending';// các đơn mà shop chưa xử lý

    public function getLabel(): string
    {
        return match ($this) {
            self::New => 'Mới',
            self::Processing => 'Đang xử lý',
            self::Shipped => 'Đã vận chuyển',
            self::Delivered => 'Đã giao hàng',
            self::Cancelled => 'Đã hủy bỏ',
            self::OnHold => 'Đơn tạm giữ',
//            self::Success => 'Thành công', // Thêm label cho giá trị mới
//            self::Pending => 'Chờ thanh toán',
            self::Waitingdelivery => 'Chờ lấy hàng',
            self::Successprocessed => 'Đã xử lý',
            self::NotProcessed => 'Chưa xử lý'
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::New => 'info',
            self::Processing, self::Waitingdelivery, self::NotProcessed => 'warning',
            self::Shipped, self::Delivered, self::Successprocessed => 'success',
            self::Cancelled => 'danger',
            self::OnHold => 'gray',
            self::Pending => 'Pending',
//            self::Success => 'success', // Thêm color cho giá trị mới
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::New => 'heroicon-m-sparkles',
            self::Processing, self::Waitingdelivery => 'heroicon-m-arrow-path',
            self::Shipped => 'heroicon-m-truck',
            self::Delivered => 'heroicon-m-check-badge',
            self::Cancelled => 'heroicon-m-x-circle',
            self::OnHold => 'heroicon-m-finger-print',
            self::Successprocessed => 'heroicon-m-check',
            self::NotProcessed => 'heroicon-m-clock',
            self::Pending => '',

        };
    }

    // Phương thức để lấy tất cả các tùy chọn cho SelectColumn
    public static function options(): array
    {
        return array_map(
            fn($case) => $case->getLabel(),
            self::cases()
        );
    }

    // Phương thức để lấy giá trị cho SelectColumn
    public static function optionsWithValues(): array
    {
        return array_reduce(
            self::cases(),
            function ($carry, $case) {
                $carry[$case->getLabel()] = $case->value;
                return $carry;
            },
            []
        );
    }
}
