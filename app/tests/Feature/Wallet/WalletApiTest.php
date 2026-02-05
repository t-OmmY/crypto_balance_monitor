<?php

namespace tests\Feature\Wallet;

use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_wallet(): void
    {
        $response = $this->postJson('/api/wallets', [
            'address' => 'test-address',
            'currency' => 'BTC',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseCount('wallets', 1);
    }

    public function test_can_list_wallets(): void
    {
        Wallet::factory()->count(3)->create();

        $this->getJson('/api/wallets')
            ->assertStatus(200)
            ->assertJsonStructure(['data' => [
                '*' => [
                    'id',
                    'address',
                    'currency',
                    'last_balance',
                    'last_balance_changed_at',
                ]
            ]]);
    }

    public function test_can_get_wallet(): void
    {
        /** @var Wallet $wallet */
        $wallet = Wallet::factory()->createOne();

        $this->getJson(sprintf('/api/wallets/%s', $wallet->getId()))
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'address',
                'currency',
                'last_balance',
                'last_balance_changed_at',
            ]);
    }
}
