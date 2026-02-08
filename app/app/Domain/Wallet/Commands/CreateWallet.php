<?php

declare(strict_types=1);

namespace App\Domain\Wallet\Commands;

use App\Enums\Currency;

final readonly class CreateWallet
{
    public function __construct(
        private string $address,
        private Currency $currency
    ) {
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}
