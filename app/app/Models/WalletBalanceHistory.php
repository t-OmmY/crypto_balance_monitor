<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\BigDecimalCast;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletBalanceHistory extends BaseUuidModel
{
    public $timestamps = false;

    protected $fillable = [
        'wallet_id',
        'balance',
        'created_at',
    ];

    protected $casts = [
        'last_balance' => BigDecimalCast::class,
        'created_at' => 'datetime',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
