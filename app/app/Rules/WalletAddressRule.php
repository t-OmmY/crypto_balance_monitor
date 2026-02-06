<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Override;

final class WalletAddressRule implements ValidationRule
{
    private const array CURRENCY_REGEX_MAP = [
        'BTC' => '/^(1|3|bc1)[a-zA-Z0-9]{25,39}$/',
        'ETH' => '/^0x[a-fA-F0-9]{40}$/',
        'LTC' => '/^(L|M|ltc1)[a-zA-Z0-9]{26,39}$/',
    ];

    public function __construct(
        private readonly string $currency
    ) {
    }

    #[Override]
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (false === is_string($value)) {
            $fail('The :attribute must be a string.');
            return;
        }

        $pattern = self::CURRENCY_REGEX_MAP[$this->currency] ?? null;
        if (null === $pattern) {
            return;
        }

        if (false === (bool) preg_match($pattern, $value)) {
            $fail('The :attribute has invalid format for selected currency.');
        }
    }
}
