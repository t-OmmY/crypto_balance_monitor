<?php

declare(strict_types=1);

namespace App\Domain\Wallet\Enums;

enum WalletStatus: string
{
    case CREATED = 'created';
    case SYNCING = 'syncing';
    case ACTIVE  = 'active';
    case FAILED  = 'failed';

    public function isUpdatable(): bool
    {
        return in_array($this, [
            self::CREATED,
            self::ACTIVE,
        ], true);
    }
}
