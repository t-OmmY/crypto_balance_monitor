<?php

namespace Services\Balance;
use App\Services\Balance\BalanceProviderException;
use App\Services\Balance\EtherscanProvider;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\TestCase;

class EtherscanProviderTest extends TestCase
{
    public function test_returns_balance(): void
    {
        Http::fake([
            'api.etherscan.io/*' => Http::response([
                'result' =>  100000000000000000,
            ]),
        ]);

        $provider = new EtherscanProvider('key', 'https://api.etherscan.io/v2/api');

        $this->assertEquals(1.0, $provider->getBalance('ETH', 'addr')->toFloat());
    }

    public function test_etherscan_provider_requires_api_key(): void
    {
        $provider = new EtherscanProvider(null, 'https://api.etherscan.io/v2/api');

        $this->expectException(BalanceProviderException::class);

        $provider->getBalance('ETH', 'address');
    }
}
