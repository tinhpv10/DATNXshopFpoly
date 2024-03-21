<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UserAddressRole: string implements HasLabel
{
    case SinhVien = 'Sinh viên';
    case GiangVien = 'Giảng viên';
    case NhanVien = 'Nhân viên';
    case NguoiToChuc = 'Người tổ chức';

    public function getName(): string
    {
        return match ($this) {
            self::SinhVien => 'Sinh viên',
            self::GiangVien => 'Giảng viên',
            self::NhanVien => 'Nhân viên',
            self::NguoiToChuc => 'Người tổ chức',
        };
    }

    public function getLabel(): string
    {
        return $this->name;
    }

}
