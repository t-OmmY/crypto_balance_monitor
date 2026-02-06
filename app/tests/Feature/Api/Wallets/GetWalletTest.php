<?php

namespace Api\Wallets;

use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetWalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_single_wallet(): void
    {
        $wallet = Wallet::factory()->create();

        $this->getJson("/api/wallets/{$wallet->getId()}")
            ->assertOk()
            ->assertJsonStructure([
                'id',
                'address',
                'currency',
                'last_balance',
                'last_balance_changed_at',
            ]);
    }
}
