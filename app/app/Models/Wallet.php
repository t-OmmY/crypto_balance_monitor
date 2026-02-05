<?php declare(strict_types=1);

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends BaseUuidModel
{
    use HasFactory;

    protected $fillable = [
        'address',
        'currency',
        'last_balance',
        'last_balance_changed_at',
    ];

    protected $casts = [
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

    public function getBalance(): int
    {
        return $this->getAttribute('last_balance');
    }

    public function getAddress(): string
    {
        return $this->getAttribute('address');
    }

    public function getCurrency(): string
    {
        return $this->getAttribute('currency');
    }

    public function getBalanceChangedAt(): null|DateTime
    {
        return $this->getAttribute('last_balance_changed_at');
    }
}
