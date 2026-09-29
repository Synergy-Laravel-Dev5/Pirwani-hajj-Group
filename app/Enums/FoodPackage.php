<?php

namespace App\Enums;

enum FoodPackage: string
{
    case FULL_BOARD = 'Full Board';
    case HALF_BOARD = 'Half Board';
    case BED_BREAKFAST = 'Bed & Breakfast';
    case NO_MEALS = 'No Meals';
    case OTHER = 'Other';

    public static function options(): array
    {
        return [
            self::FULL_BOARD->value => 'Full Board',
            self::HALF_BOARD->value => 'Half Board',
            self::BED_BREAKFAST->value => 'Bed & Breakfast',
            self::NO_MEALS->value => 'No Meals',
            self::OTHER->value => 'Other',
        ];
    }
}
