<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    case Payment = 'Thanh toán khi nhận hàng';
    case PayVN = 'Thanh toán VNPAY';

    public function getLabel(): string
    {
        return match ($this) {
            self::Payment => 'Thanh toán khi nhận hàng',
            self::PayVN => 'Thanh toán VNPAY',
        };
    }

}
