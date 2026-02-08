<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Wallet\Commands;

use App\Domain\Wallet\Commands\UpdateWalletBalance;
use App\Domain\Wallet\Commands\UpdateWalletBalanceHandler;
use App\Domain\Wallet\Enums\WalletStatus;
use App\Domain\Wallet\Exceptions\WalletNotFoundException;
use App\Enums\Currency;
use App\Models\Wallet;
use App\Services\Balance\BalanceService;
use Brick\Math\BigDecimal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

final class UpdateWalletBalanceHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_wallet_balance_is_updated_and_status_becomes_active(): void
    {
        $wallet = Wallet::factory()->create([
            'status' => WalletStatus::CREATED,
            'currency' => Currency::BTC,
        ]);

        $balanceService = Mockery::mock(BalanceService::class);
        $balanceService
            ->shouldReceive('getBalance')
            ->once()
            ->andReturn(BigDecimal::of(1.234));

        $handler = new UpdateWalletBalanceHandler($balanceService);

        $handler->handle(new UpdateWalletBalance($wallet->getId()));

        $wallet->refresh();

        self::assertSame(WalletStatus::ACTIVE, $wallet->getStatus());
        self::assertSame(1.234, $wallet->getBalance()->toFloat());
    }

    public function test_exception_is_thrown_when_wallet_not_found(): void
    {
        $this->expectException(WalletNotFoundException::class);

        $handler = app(UpdateWalletBalanceHandler::class);
        $handler->handle(new UpdateWalletBalance('79dd8d71-ffa2-497f-ad80-9f7cb65c99cc'));
    }
}
