<?php

declare(strict_types=1);

namespace App\Services\Balance;

use App\Enums\Currency;
use Brick\Math\BigDecimal;

interface BalanceProviderInterface
{
    public function support(Currency $currency): bool;

    /**
     * @throws BalanceProviderException
     */
    public function getBalance(Currency $currency, string $address): BigDecimal;
}
