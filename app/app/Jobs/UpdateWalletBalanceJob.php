<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Wallet;
use App\Models\WalletBalanceHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateWalletBalanceJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public string $walletId;

    public function __construct(string $walletId)
    {
        $this->walletId = $walletId;
    }

    public function handle(): void
    {
        $wallet = Wallet::find($this->walletId);

        if (null === $wallet) {
            return;
        }

        /**
         * todo rewrite it
         * 🔧 Тимчасово: fake balance
         * Далі замінимо на BalanceProvider
         */
        $newBalance = random_int(0, 1000) / 10;

        WalletBalanceHistory::create([
            'wallet_id' => $wallet->getId(),
            'balance' => $newBalance,
            'created_at' => now(),
        ]);

        if ((string) $wallet->getBalance() !== (string) $newBalance) {
            $wallet->update([
                'last_balance' => $newBalance,
                'last_balance_changed_at' => now(),
            ]);
        }
    }
}
