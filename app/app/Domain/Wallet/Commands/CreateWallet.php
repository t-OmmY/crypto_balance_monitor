<?php

declare(strict_types=1);

namespace App\Domain\Wallet\Commands;

final readonly class CreateWallet
{
    public function __construct(
        private string $address,
        private string $currency
    ) {
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
