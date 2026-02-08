<?php

declare(strict_types=1);

namespace App\Domain\Wallet\Results;

final readonly class CreateWalletResult
{
    public function __construct(
        private string $walletId,
        private bool $alreadyExists
    ) {
    }

    public function getWalletId(): string
    {
        return $this->walletId;
    }

    public function isAlreadyExists(): bool
    {
        return $this->alreadyExists;
    }
}
