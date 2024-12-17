<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'source_id');
    }
}
