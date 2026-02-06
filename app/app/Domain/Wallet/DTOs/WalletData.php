<?php

declare(strict_types=1);

namespace App\Domain\Wallet\DTOs;

use App\Models\Wallet;
use DateTime;

/**
 * @psalm-api
 */
final readonly class WalletData
{
    private function __construct(
        public string $id,
        public string $address,
        public string $currency,
        public int $last_balance,
        public null|DateTime $last_balance_changed_at
    ) {
    }

    public static function fromModel(Wallet $wallet): self
    {
        return new self(
            $wallet->getId(),
            $wallet->getAddress(),
            strtoupper($wallet->getCurrency()),
            $wallet->getBalance(),
            $wallet->getBalanceChangedAt()
        );
    }
}
