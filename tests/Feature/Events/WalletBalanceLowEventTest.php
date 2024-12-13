<?php

declare(strict_types=1);

use App\Events\WalletBalanceLowEvent;
use App\Models\Wallet;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;

test('when an wallet balance low event is dispatched, a notification is sent to the wallet user', function () {
    Notification::fake();
    Config::set('wallet.balance_low_level', 10);

    $wallet = Wallet::factory()->balance(5)->create();
    $user = $wallet->user;
    WalletBalanceLowEvent::dispatch($wallet);

    Notification::assertSentTo($user, \App\Notifications\BalanceLowNotification::class);
});

test('when an wallet balance low event is dispatched, a notification is not sent to the wallet user if above low level in the meantime', function () {
    Notification::fake();
    Config::set('wallet.balance_low_level', 10);

    $wallet = Wallet::factory()->balance(100)->create();
    $user = $wallet->user;
    WalletBalanceLowEvent::dispatch($wallet);

    Notification::assertSentTimes(\App\Notifications\BalanceLowNotification::class, 0);
    Notification::assertNotSentTo($user, \App\Notifications\BalanceLowNotification::class);
});
