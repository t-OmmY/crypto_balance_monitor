<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Wallet;
use App\Models\WalletBalanceHistory;
use App\Services\Balance\BalanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

final class UpdateWalletBalanceJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public string $walletId;

    public int $tries = 3;

    public function backoff(): array
    {
        return [10, 30, 60];
    }

    public function __construct(string $walletId)
    {
        $this->walletId = $walletId;
    }

    /**
     * @throws Throwable
     */
    public function handle(BalanceService $balanceService): void
    {
        $wallet = Wallet::find($this->walletId);

        if (null === $wallet) {
            return;
        }

        $newBalance = $balanceService->getBalance($wallet);

        DB::beginTransaction();
        try {
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
