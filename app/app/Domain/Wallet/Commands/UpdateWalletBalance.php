<?php

declare(strict_types=1);

namespace App\Domain\Wallet\Commands;

final readonly class UpdateWalletBalance
{
    public function __construct(
        private string $walletId
    ) {
    }

    public function getWalletId(): string
    {
        return $this->walletId;
    }
}
