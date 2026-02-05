<?php declare(strict_types=1);

namespace App\Domain\Wallet\Queries;

final readonly class GetWalletById
{
    public function __construct(
        private string $id
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
