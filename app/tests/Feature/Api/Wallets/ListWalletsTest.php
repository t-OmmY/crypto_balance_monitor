<?php

namespace Api\Wallets;

use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ListWalletsTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_paginated_wallets(): void
    {
        Wallet::factory()->count(3)->create();

        $this->getJson('/api/wallets')
            ->assertOk()
            ->assertJsonStructure([
                'links',
                'data' => [
                    '*' => [
                        'id',
                        'address',
                        'currency',
                        'status',
                        'last_balance',
                        'last_balance_changed_at',
                    ],
                ]
            ]);
    }
}
