<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domain\Wallet\Commands\UpdateWalletBalance;
use App\Domain\Wallet\Commands\UpdateWalletBalanceHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
    public function handle(UpdateWalletBalanceHandler $handler): void
    {
        $handler->handle(
            new UpdateWalletBalance($this->walletId)
        );
    }
}
