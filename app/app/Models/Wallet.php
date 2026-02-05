<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
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
}
