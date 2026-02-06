<?php

declare(strict_types=1);

namespace App\Domain\Wallet\Queries;

use App\Domain\Wallet\DTOs\WalletData;
use App\Models\Wallet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ListWalletsHandler
{
    public function handle(ListWallets $query): LengthAwarePaginator
    {
        return Wallet::paginate($query->getPerPage())
            ->through(fn ($wallet) => WalletData::fromModel($wallet));
    }
}
