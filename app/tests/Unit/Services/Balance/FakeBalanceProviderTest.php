<?php

namespace Services\Balance;

use App\Enums\Currency;
use App\Services\Balance\FakeBalanceProvider;
use PHPUnit\Framework\TestCase;

final class FakeBalanceProviderTest extends TestCase
{
    public function test_supports_any_currency(): void
    {
        $provider = new FakeBalanceProvider();

        $this->assertTrue($provider->support(Currency::BTC));
        $this->assertTrue($provider->support(Currency::ETH));
        $this->assertTrue($provider->support(Currency::LTC));
    }
}
