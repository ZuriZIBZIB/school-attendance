<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Hadir = 'hadir';
    case Izin = 'izin';
    case Sakit = 'sakit';
    case Alpa = 'alpa';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Hadir => '#16a34a',
            self::Izin => '#2563eb',
            self::Sakit => '#f59e0b',
            self::Alpa => '#dc2626',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Hadir => 'bg-green-100 text-green-700',
            self::Izin => 'bg-blue-100 text-blue-700',
            self::Sakit => 'bg-amber-100 text-amber-700',
            self::Alpa => 'bg-red-100 text-red-700',
        };
    }
}