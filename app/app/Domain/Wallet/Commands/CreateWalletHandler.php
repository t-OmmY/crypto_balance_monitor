<?php declare(strict_types=1);

namespace App\Domain\Wallet\Commands;

use App\Models\Wallet;
use App\Models\WalletBalanceHistory;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateWalletHandler
{
    /**
     * @throws Throwable
     */
    public function handle(CreateWallet $command): Wallet
    {
        $wallet = Wallet::where([
            'address' => $command->getAddress(),
            'currency' => $command->getCurrency()
        ])->first();
        if (null !== $wallet) {
            return $wallet;
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

            return $wallet;
        } catch (Throwable $exception) {
            DB::rollBack();

            //todo what if?
            throw $exception;
        }
    }
}
