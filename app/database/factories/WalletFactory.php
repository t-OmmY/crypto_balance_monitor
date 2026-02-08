<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Wallet\Enums\WalletStatus;
use App\Enums\Currency;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/**
 * @extends Factory<Wallet>
 */
class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    #[Override]
    public function definition(): array
    {
        /** @var Currency $currency */
        $currency = $this->faker->randomElement(Currency::cases());

        return [
            'currency' => $currency->value,
            'status' =>  WalletStatus::ACTIVE->value,
            'address' => $this->fakeAddress($currency),
            'last_balance' => fake()->numberBetween(),
        ];
    }

    private function fakeAddress(Currency $currency): string
    {
        return match ($currency) {
            Currency::BTC => 'bc1q' . $this->faker->regexify('[a-z0-9]{38}'),
            Currency::ETH => '0x' . $this->faker->regexify('[a-f0-9]{40}'),
            Currency::LTC => 'ltc1' . $this->faker->regexify('[a-z0-9]{38}'),
        };
    }
}
