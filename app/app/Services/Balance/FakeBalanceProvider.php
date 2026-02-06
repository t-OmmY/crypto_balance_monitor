<?php

declare(strict_types=1);

namespace App\Services\Balance;

use Brick\Math\BigDecimal;
use Override;

final class FakeBalanceProvider implements BalanceProviderInterface
{
    #[Override]
    public function support(string $currency): bool
    {
        return true;
    }

    #[Override]
    public function getBalance(string $currency, string $address): BigDecimal
    {
        $response = match ($currency) {
            'BTC' => 1.2345,
            'LTC' => 42.0,
            'ETH' => 0.9876,
            default => 0.0,
        };

        return BigDecimal::of($response);
    }
}
