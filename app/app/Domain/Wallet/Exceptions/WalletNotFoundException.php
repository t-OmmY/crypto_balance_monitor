<?php

declare(strict_types=1);

namespace App\Domain\Wallet\Exceptions;

use RuntimeException;

final class WalletNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Wallet not found');
    }
}
