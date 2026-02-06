<?php

namespace Console;

use App\Jobs\UpdateWalletBalanceJob;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UpdateWalletBalancesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_dispatches_jobs(): void
    {
        Queue::fake();

        Wallet::factory()->count(3)->create();

        $this->artisan('wallets:update-balances')
            ->assertExitCode(0);

        Queue::assertPushed(UpdateWalletBalanceJob::class, 3);
    }
}
