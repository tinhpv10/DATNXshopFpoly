<?php


namespace App\Enums;


use Filament\Support\Contracts\HasLabel;

enum CancelledStatus: string implements HasLabel
{
    case Cancelled1 = 'Thủ tục thanh toán rắc rối';
    case Cancelled2 = 'Tôi không có nhu cầu mua nửa';
    case Cancelled3 = 'Tôi không tìm thấy lý do phù hợp';

    public function getLabel(): string
    {
        return match ($this) {
            self::Cancelled1 => 'Thủ tục thanh toán rắc rối',
            self::Cancelled2 => 'Tôi không có nhu cầu mua nửa',
            self::Cancelled3 => 'Tôi không tìm thấy lý do phù hợp',

        };
    }

}
