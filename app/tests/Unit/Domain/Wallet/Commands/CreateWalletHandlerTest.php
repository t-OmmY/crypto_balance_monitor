<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Wallet\Commands;

use App\Domain\Wallet\Commands\CreateWallet;
use App\Domain\Wallet\Commands\CreateWalletHandler;
use App\Domain\Wallet\Enums\WalletStatus;
use App\Enums\Currency;
use App\Jobs\UpdateWalletBalanceJob;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

final class CreateWalletHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_wallet_is_created_with_created_status(): void
    {
        Queue::fake();

        $handler = app(CreateWalletHandler::class);

        $command = new CreateWallet(
            address: 'bc1qvalidbtcaddress1234567890',
            currency: Currency::BTC
        );

        $result = $handler->handle($command);

        $wallet = Wallet::findOrFail($result->getWalletId());
        Queue::assertPushed(UpdateWalletBalanceJob::class, 1);

        self::assertSame(WalletStatus::CREATED, $wallet->getStatus());
        self::assertSame('BTC', $wallet->getCurrency()->value);
    }

    public function test_existing_wallet_is_not_duplicated(): void
    {
        Wallet::factory()->create([
            'address' => 'bc1qvalidbtcaddress1234567890',
            'currency' => Currency::BTC,
        ]);

        $handler = app(CreateWalletHandler::class);

        $command = new CreateWallet(
            address: 'bc1qvalidbtcaddress1234567890',
            currency: Currency::BTC
        );

        $result = $handler->handle($command);

        self::assertCount(1, Wallet::all());
        self::assertNotNull($result->getWalletId());
    }
}
