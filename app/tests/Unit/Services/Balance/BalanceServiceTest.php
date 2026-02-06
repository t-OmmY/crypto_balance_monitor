<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Balance;

use App\Models\Wallet;
use App\Services\Balance\BalanceProviderException;
use App\Services\Balance\BalanceProviderInterface;
use App\Services\Balance\BalanceService;
use Brick\Math\BigDecimal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

final class BalanceServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_uses_supported_provider(): void
    {
        $provider = new class implements BalanceProviderInterface {
            public function support(string $currency): bool
            {
                return $currency === 'BTC';
            }

            public function getBalance(string $currency, string $address): BigDecimal
            {
                return BigDecimal::of('1.23');
            }
        };

        $wallet = new Wallet([
            'currency' => 'BTC',
            'address' => 'addr',
        ]);

        $service = new BalanceService([$provider]);

        $this->assertEquals(1.23, $service->getBalance($wallet)->toFloat());
    }

    public function test_throws_exception_when_no_provider_supports_currency(): void
    {
        $provider = Mockery::mock(BalanceProviderInterface::class);
        $provider->shouldReceive('support')->andReturn(false);

        $this->expectException(BalanceProviderException::class);

        $service = new BalanceService([$provider]);

        $wallet = Wallet::factory()->create(['currency' => 'DOGE']);

        $service->getBalance($wallet);
    }
}
