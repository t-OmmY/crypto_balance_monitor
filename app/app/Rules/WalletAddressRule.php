<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\Currency;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Override;

final class WalletAddressRule implements ValidationRule
{
    private const string BTC_ADDRESS_PATTERN = '/^(1|3|bc1)[a-zA-Z0-9]{25,39}$/';
    private const string ETH_ADDRESS_PATTERN = '/^0x[a-fA-F0-9]{40}$/';
    private const string LTC_ADDRESS_PATTERN = '/^(L|M|ltc1)[a-zA-Z0-9]{26,39}$/';

    public function __construct(
        private readonly Currency $currency
    ) {
    }

    #[Override]
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (false === is_string($value)) {
            $fail('The :attribute must be a string.');
            return;
        }

        $pattern = match ($this->currency) {
            Currency::BTC => self::BTC_ADDRESS_PATTERN,
            Currency::ETH => self::ETH_ADDRESS_PATTERN,
            Currency::LTC => self::LTC_ADDRESS_PATTERN,
        };

        if (null === $pattern) {
            return;
        }

        if (false === (bool) preg_match($pattern, $value)) {
            $fail('The :attribute has invalid format for selected currency.');
        }
    }
}
