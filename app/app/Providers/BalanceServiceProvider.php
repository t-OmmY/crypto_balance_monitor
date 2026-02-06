<?php

namespace App\Providers;

use App\Services\Balance\BalanceService;
use App\Services\Balance\BlockchairProvider;
use App\Services\Balance\EtherscanProvider;
use App\Services\Balance\FakeBalanceProvider;
use Illuminate\Support\ServiceProvider;
use Override;

final class BalanceServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        if (true === config('balance.use_fake')) {
            $this->app
                ->when(BalanceService::class)
                ->needs('$providers')
                ->give(fn () => [new FakeBalanceProvider()]);

            return;
        }

        $this->app->bind(BlockchairProvider::class, function () {
            return new BlockchairProvider(
                config('services.blockchair.api_key'),
                config('services.blockchair.base_url'),
            );
        });

        $this->app->bind(EtherscanProvider::class, function () {
            return new EtherscanProvider(
                config('services.etherscan.api_key'),
                config('services.etherscan.base_url'),
            );
        });

        $this->app
            ->when(BalanceService::class)
            ->needs('$providers')
            ->give(function ($app) {
                return collect(config('balance.providers'))
                    ->map(fn ($provider) => $app->make($provider))
                    ->all();
            });
    }
}
