<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletBalanceHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'wallet_id',
        'balance',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
