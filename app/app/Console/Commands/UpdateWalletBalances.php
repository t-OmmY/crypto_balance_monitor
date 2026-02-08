<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Wallet\Enums\WalletStatus;
use App\Jobs\UpdateWalletBalanceJob;
use App\Models\Wallet;
use Illuminate\Console\Command;

final class UpdateWalletBalances extends Command
{
    /**
     * @var string
     */
    protected $signature = 'wallets:update-balances';

    /**
     * @var string
     */
    protected $description = 'Dispatch jobs to update wallets balances';

    public function handle(): int
    {
        Wallet::query()
            ->select('id')
            ->whereIn('status', [
                WalletStatus::CREATED,
                WalletStatus::ACTIVE,
            ])
            ->chunkById(100, function ($wallets) {
                /** @var Wallet $wallet */
                foreach ($wallets as $wallet) {
                    UpdateWalletBalanceJob::dispatch($wallet->getId());
                }
            });

        $this->info('Wallet balance update jobs dispatched.');

        return self::SUCCESS;
    }
}
