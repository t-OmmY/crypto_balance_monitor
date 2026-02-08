<?php

declare(strict_types=1);

namespace App\Services\Balance;

use App\Enums\Currency;
use Brick\Math\BigDecimal;
use Override;

final class FakeBalanceProvider implements BalanceProviderInterface
{
    #[Override]
    public function support(Currency $currency): bool
    {
        return true;
    }

    #[Override]
    public function getBalance(Currency $currency, string $address): BigDecimal
    {
        $response = match ($currency) {
            Currency::BTC => 1.2345,
            Currency::ETH => 42.0,
            Currency::LTC => 0.9876
        };

        return BigDecimal::of($response);
    }
}
