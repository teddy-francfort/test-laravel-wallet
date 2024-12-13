<?php

declare(strict_types=1);

use App\Events\WalletBalanceLowEvent;
use App\Models\Wallet;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;

test('an event is sent when balance is low', function () {
    Event::fake([WalletBalanceLowEvent::class]);

    Config::set('wallet.balance_low_level', 10);
    $wallet = Wallet::factory()->balance(100)->create();
    Event::assertNotDispatched(WalletBalanceLowEvent::class);

    $wallet->balance = 5;
    $wallet->save();

    Event::assertDispatched(function (WalletBalanceLowEvent $event) use ($wallet) {
        return $event->wallet->is($wallet);
    });
});
