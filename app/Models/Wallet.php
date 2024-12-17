<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\WalletObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(WalletObserver::class)]
class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    /**
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<WalletTransaction>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function isBalanceLow(): bool
    {
        return $this->balance < config('wallet.balance_low_level');
    }

    public function recurringTransfers(): HasMany
    {
        return $this->hasMany(WalletRecurringTransfer::class, 'source_id');
    }
}
