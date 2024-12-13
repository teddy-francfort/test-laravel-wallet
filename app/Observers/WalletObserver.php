<?php

declare(strict_types=1);

namespace App\Observers;

use App\Events\WalletBalanceLowEvent;
use App\Models\Wallet;

class WalletObserver
{
    /**
     * Handle the Wallet "updated" event.
     */
    public function updated(Wallet $wallet): void
    {
        if ($wallet->wasChanged('balance')) {
            if ($wallet->isBalanceLow()) {
                WalletBalanceLowEvent::dispatch($wallet);
            }
        }
    }
}
