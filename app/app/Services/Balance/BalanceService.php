<?php

declare(strict_types=1);

namespace App\Services\Balance;

use App\Models\Wallet;
use Brick\Math\BigDecimal;

final readonly class BalanceService
{
    /**
     * @param iterable<BalanceProviderInterface> $providers
     *
     * @psalm-api
     */
    public function __construct(
        private iterable $providers
    ) {
    }

    public function getBalance(Wallet $wallet): BigDecimal
    {
        foreach ($this->providers as $provider) {
            if (false === $provider->support($wallet->getCurrency())) {
                continue;
            }

            return $provider->getBalance($wallet->getCurrency(), $wallet->getAddress());
        }

        throw new BalanceProviderException('Unsupported currency');
    }
}
