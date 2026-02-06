<?php

declare(strict_types=1);

namespace App\Casts;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Override;

/**
 * @implements CastsAttributes<BigDecimal, string>
 */
final class BigDecimalCast implements CastsAttributes
{
    #[Override]
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value !== null ? BigDecimal::of($value) : null;
    }

    #[Override]
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $bigDecimal = $value instanceof BigDecimal ? $value : BigDecimal::of($value);

        return (string) $bigDecimal->toScale(18, RoundingMode::Down);
    }
}
