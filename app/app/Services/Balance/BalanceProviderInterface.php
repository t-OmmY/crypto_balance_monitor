<?php

declare(strict_types=1);

namespace App\Services\Balance;

use Brick\Math\BigDecimal;

interface BalanceProviderInterface
{
    public function support(string $currency): bool;

    /**
     * @throws BalanceProviderException
     */
    public function getBalance(string $currency, string $address): BigDecimal;
}
