<?php

namespace App\Enums;

enum AccommodationType: string
{
    case HOTEL = 'Hotel';
    case CAMP = 'Camp';
    case BUILDING = 'Building';
    case APARTMENT = 'Apartment';
    case OTHER = 'Other';

    public static function options(): array
    {
        return [
            self::HOTEL->value => 'Hotel',
            self::CAMP->value => 'Camp',
            self::BUILDING->value => 'Building',
            self::APARTMENT->value => 'Apartment',
            self::OTHER->value => 'Other',
        ];
    }
}
