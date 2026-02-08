<?php

declare(strict_types=1);

namespace App\Enums;

enum Currency: string
{
    case BTC = 'BTC';
    case ETH = 'ETH';
    case LTC = 'LTC';

    public static function values(): array
    {
        return array_map(
            static fn (self $currency) => $currency->value,
            self::cases()
        );
    }
}
