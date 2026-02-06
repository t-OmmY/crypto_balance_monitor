<?php

declare(strict_types=1);

namespace App\Domain\Wallet\Commands;

use App\Domain\Wallet\DTOs\WalletData;
use App\Jobs\UpdateWalletBalanceJob;
use App\Models\Wallet;
use App\Models\WalletBalanceHistory;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateWalletHandler
{
    /**
     * @throws Throwable
     */
    public function handle(CreateWallet $command): WalletData
    {
        $wallet = Wallet::where([
            'address' => $command->getAddress(),
            'currency' => $command->getCurrency()
        ])->first();
        if (null !== $wallet) {
            return WalletData::fromModel($wallet);
        }

        DB::beginTransaction();
        try {
            $wallet = Wallet::create([
                'address' => $command->getAddress(),
                'currency' => $command->getCurrency(),
                'last_balance' => 0,
            ]);

            WalletBalanceHistory::create([
                'wallet_id' => $wallet->getId(),
                'balance' => 0,
                'created_at' => now(),
            ]);

            DB::commit();

            UpdateWalletBalanceJob::dispatch($wallet->getId());

            return WalletData::fromModel($wallet);
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
