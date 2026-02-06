<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Balance;

use App\Services\Balance\BalanceProviderException;
use App\Services\Balance\BlockchairProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class BlockchairProviderTest extends TestCase
{
    public function test_returns_balance(): void
    {
        Http::fake([
            'api.blockchair.com/*' => Http::response([
                'data' => [
                    'addr' => [
                        'address' => [
                            'balance' => 100_000_000,
                        ],
                    ],
                ],
            ]),
        ]);

        $provider = new BlockchairProvider('key', 'https://api.blockchair.com');

        $this->assertEquals(1.0, $provider->getBalance('BTC', 'addr')->toFloat());
    }

    public function test_requires_api_key(): void
    {
        $this->expectException(BalanceProviderException::class);

        (new BlockchairProvider(null, 'https://api.blockchair.com'))
            ->getBalance('BTC', 'addr');
    }
}
