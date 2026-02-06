<?php

namespace Jobs;

use App\Jobs\UpdateWalletBalanceJob;
use App\Models\Wallet;
use App\Services\Balance\BalanceService;
use Brick\Math\BigDecimal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UpdateWalletBalanceJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_creates_balance_history(): void
    {
        $wallet = Wallet::factory()->create();

        $this->mock(BalanceService::class, function ($mock) {
            $mock->shouldReceive('getBalance')->once()->andReturn(BigDecimal::of(10.5));
        });

        UpdateWalletBalanceJob::dispatchSync($wallet->getId());

        $this->assertDatabaseHas('wallet_balance_histories', [
            'wallet_id' => $wallet->getId(),
            'balance' => 10.5,
        ]);
    }
}
