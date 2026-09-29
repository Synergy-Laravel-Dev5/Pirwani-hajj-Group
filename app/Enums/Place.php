<?php

namespace App\Enums;

enum Place: string
{
    case MAKKAH = 'Makkah';
    case MADINAH = 'Madinah';
    case MINA = 'Mina';
    case ARAFAT = 'Arafat';
    case AZIZIA = 'Azizia';
    case OTHER = 'Other';

    public static function options(): array
    {
        return [
            self::MAKKAH->value => 'Makkah',
            self::MADINAH->value => 'Madinah',
            self::MINA->value => 'Mina',
            self::ARAFAT->value => 'Arafat',
            self::AZIZIA->value => 'Azizia',
            self::OTHER->value => 'Other',
        ];
    }
}
