<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\WalletBalanceLowEvent;
use App\Notifications\BalanceLowNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class WalletNotificationListener implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(WalletBalanceLowEvent $event): void
    {
        $event->wallet->user->notify(new BalanceLowNotification($event->wallet));
    }
}
