<?php

namespace Api\Wallets;

use App\Jobs\UpdateWalletBalanceJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

final class CreateWalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_wallet(): void
    {
        Queue::fake();

        $response = $this->postJson('/api/wallets', [
            'currency' => 'BTC',
            'address' => 'address123',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('wallets', [
            'currency' => 'BTC',
            'address' => 'address123',
        ]);

        Queue::assertPushed(UpdateWalletBalanceJob::class, 1);
    }

    public function test_can_not_create_wallet_throw_validation(): void
    {
        $response = $this->postJson('/api/wallets', [
            'address' => 'test-address',
            'currency' => 'UAH',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('wallets', 0);
    }
}
