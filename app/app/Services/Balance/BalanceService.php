<?php

declare(strict_types=1);

namespace App\Services\Balance;

use App\Models\Wallet;
use Brick\Math\BigDecimal;
use Illuminate\Support\Facades\Log;

class BalanceService
{
    /**
     * @param iterable<BalanceProviderInterface> $providers
     *
     * @psalm-api
     */
    public function __construct(
        private readonly iterable $providers
    ) {
    }

    public function getBalance(Wallet $wallet): BigDecimal
    {
        foreach ($this->providers as $provider) {
            if (false === $provider->support($wallet->getCurrency())) {
                continue;
            }

            try {
                return $provider->getBalance(
                    $wallet->getCurrency(),
                    $wallet->getAddress()
                );
            } catch (BalanceProviderException $e) {
                Log::warning('Balance provider failed', [
                    'provider' => get_class($provider),
                    'wallet_id' => $wallet->getId(),
                    'currency' => $wallet->getCurrency()->value,
                    'message' => $e->getMessage(),
                ]);

                continue;
            }
        }

        Log::error('No balance provider available', [
            'wallet_id' => $wallet->getId(),
            'currency' => $wallet->getCurrency()->value,
        ]);

        throw new BalanceProviderException('No provider could fetch balance');
    }
}
