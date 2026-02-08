<?php

namespace Api\Wallets;

use App\Domain\Wallet\Enums\WalletStatus;
use App\Jobs\UpdateWalletBalanceJob;
use App\Models\Wallet;
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
            'address' => '15cHRgVrGKz7qp2JL2N5mkB2MCFGLcnHxv',
        ]);

        $response->assertCreated();
        $response->assertJsonStructure([
            'id'
        ]);

        $this->assertDatabaseHas('wallets', [
            'status' => WalletStatus::CREATED->value,
            'currency' => 'BTC',
            'address' => '15cHRgVrGKz7qp2JL2N5mkB2MCFGLcnHxv',
        ]);

        Queue::assertPushed(UpdateWalletBalanceJob::class, 1);
    }

    public function test_creates_wallet_already_exists(): void
    {
        Queue::fake();
        $wallet = Wallet::factory()->create();

        $response = $this->postJson('/api/wallets', [
            'currency' => $wallet->getCurrency()->value,
            'status' => $wallet->getStatus()->value,
            'address' => $wallet->getAddress(),
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'id'
        ]);

        Queue::assertNotPushed(UpdateWalletBalanceJob::class, 1);
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

    public function test_invalid_eth_address_fails_validation(): void
    {
        $response = $this->postJson('/api/wallets', [
            'currency' => 'ETH',
            'address' => 'invalid',
        ]);

        $response->assertStatus(422);
    }
}
