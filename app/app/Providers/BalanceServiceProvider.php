<?php

namespace App\Providers;

use App\Services\Balance\BalanceService;
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
