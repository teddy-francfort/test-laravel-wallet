<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WalletRecurringTransfer extends Model
{
    /** @use HasFactory<\Database\Factories\WalletRecurringTransferFactory> */
    use HasFactory;

    protected $table = 'wallet_recurring_transfers';

    protected $fillable = [
        'source_id',
        'start_date',
        'end_date',
        'frequency',
        'recipient_email',
        'amount',
        'reason',
    ];

    protected function casts()
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'source_id');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(WalletTransfer::class, 'recurring_transfer_id', 'id');
    }
}
