<?php

declare(strict_types=1);

namespace App\Domain\Wallet\Commands;

use App\Domain\Wallet\Exceptions\WalletNotFoundException;
use App\Models\Wallet;
use App\Models\WalletBalanceHistory;
use App\Services\Balance\BalanceService;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * @psalm-api
 */
final readonly class UpdateWalletBalanceHandler
{
    public function __construct(
        private BalanceService $balanceService
    ) {
    }

    /**
     * @throws Throwable
     */
    public function handle(UpdateWalletBalance $command): void
    {
        $wallet = Wallet::find($command->getWalletId());
        if (null === $wallet) {
            throw new WalletNotFoundException();
        }

        DB::beginTransaction();

        try {
            $newBalance = $this->balanceService->getBalance($wallet);

            WalletBalanceHistory::create([
                'wallet_id' => $wallet->getId(),
                'balance' => $newBalance,
                'created_at' => now(),
            ]);

            if (false === $newBalance->isEqualTo($wallet->getBalance())) {
                $wallet->update([
                    'last_balance' => $newBalance,
                    'last_balance_changed_at' => now(),
                ]);
            }

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
