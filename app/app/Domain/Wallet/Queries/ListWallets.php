<?php declare(strict_types=1);

namespace App\Domain\Wallet\Queries;

final readonly class ListWallets
{
    public function __construct(
        private int $perPage
    ) {
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }
}
