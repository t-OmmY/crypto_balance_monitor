<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Balance;

use App\Domain\Wallet\Enums\WalletStatus;
use App\Enums\Currency;
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
            public function support(Currency $currency): bool
            {
                return $currency === Currency::BTC;
            }

            public function getBalance(Currency $currency, string $address): BigDecimal
            {
                return BigDecimal::of('1.23');
            }
        };

        $wallet = new Wallet([
            'currency' => Currency::BTC,
            'status' => WalletStatus::ACTIVE,
            'address' => 'addr',
        ]);

        $service = new BalanceService([$provider]);

        $this->assertEquals(1.23, $service->getBalance($wallet)->toFloat());
    }

    public function test_throws_exception_when_no_provider_supports_currency(): void
    {
        $providerMock = Mockery::mock(BalanceProviderInterface::class);
        $providerMock->shouldReceive('support')->andReturn(false);

        $this->expectException(BalanceProviderException::class);

        $service = new BalanceService([$providerMock]);

        $walletMock = Mockery::mock(Wallet::class);
        $walletMock->shouldReceive('getCurrency')->andReturn(Currency::ETH);
        $walletMock->shouldReceive('getId')->andReturn('foobar');

        $service->getBalance($walletMock);
    }
}
