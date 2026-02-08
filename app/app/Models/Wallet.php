<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\BigDecimalCast;
use App\Enums\Currency;
use Brick\Math\BigDecimal;
use Database\Factories\WalletFactory;
use DateTime;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(WalletFactory::class)]
class Wallet extends BaseUuidModel
{
    /** @use HasFactory<WalletFactory> */
    use HasFactory;

    protected $fillable = [
        'address',
        'currency',
        'last_balance',
        'last_balance_changed_at',
    ];

    protected $casts = [
        'currency' => Currency::class,
        'last_balance' => BigDecimalCast::class,
        'last_balance_changed_at' => 'datetime',
    ];

    public function history(): HasMany
    {
        return $this->hasMany(WalletBalanceHistory::class);
    }

    public function getId(): string
    {
        return $this->getAttribute('id');
    }

    public function getBalance(): BigDecimal
    {
        return $this->getAttribute('last_balance');
    }

    public function getAddress(): string
    {
        return $this->getAttribute('address');
    }

    public function getCurrency(): Currency
    {
        return $this->getAttribute('currency');
    }

    public function getBalanceChangedAt(): null|DateTime
    {
        return $this->getAttribute('last_balance_changed_at');
    }
}
