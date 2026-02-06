<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/**
 * @extends Factory<Wallet>
 */
class WalletFactory extends Factory
{
    #[Override]
    public function definition(): array
    {
        return [
            'address' => fake()->name(),
            'currency' => fake()->currencyCode(),
            'last_balance' => fake()->numberBetween(),
        ];
    }
}
