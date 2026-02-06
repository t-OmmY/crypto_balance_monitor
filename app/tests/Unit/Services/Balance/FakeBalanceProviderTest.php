<?php

namespace Services\Balance;

use App\Services\Balance\FakeBalanceProvider;
use PHPUnit\Framework\TestCase;

final class FakeBalanceProviderTest extends TestCase
{
    public function test_supports_any_currency(): void
    {
        $provider = new FakeBalanceProvider();

        $this->assertTrue($provider->support('BTC'));
        $this->assertTrue($provider->support('ETH'));
        $this->assertTrue($provider->support('LTC'));
    }
}
