<?php

declare(strict_types=1);

namespace App\Domain\Wallet\Commands;

use App\Domain\Wallet\Results\CreateWalletResult;
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
    public function handle(CreateWallet $command): CreateWalletResult
    {
        $wallet = Wallet::where([
            'address' => $command->getAddress(),
            'currency' => $command->getCurrency()->value
        ])->first();
        if (null !== $wallet) {
            return new CreateWalletResult(
                $wallet->getId(),
                true,
            );
        }

        DB::beginTransaction();
        try {
            $wallet = Wallet::create([
                'address' => $command->getAddress(),
                'currency' => $command->getCurrency()->value,
                'last_balance' => 0,
            ]);

            WalletBalanceHistory::create([
                'wallet_id' => $wallet->getId(),
                'balance' => 0,
                'created_at' => now(),
            ]);

            DB::commit();

            UpdateWalletBalanceJob::dispatch($wallet->getId());

            return new CreateWalletResult(
                $wallet->getId(),
                false,
            );
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
