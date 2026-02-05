<?php declare(strict_types=1);

namespace App\Domain\Wallet\Queries;

use App\Domain\Wallet\DTOs\WalletData;
use App\Models\Wallet;

final readonly class GetWalletByIdHandler
{
    public function handle(GetWalletById $query): WalletData
    {
        $wallet = Wallet::where('id', $query->getId())->firstOrFail();

        return WalletData::fromModel($wallet);
    }
}
