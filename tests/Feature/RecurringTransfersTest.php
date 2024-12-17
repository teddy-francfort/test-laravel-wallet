<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletRecurringTransfer;

use function Pest\Laravel\actingAs;

test('recurring transfers page is displayed', function () {
    $user = User::factory()->has(Wallet::factory()->richChillGuy())->create();
    $wallet = Wallet::factory()->richChillGuy()->for($user)->create();
    $recurringTransfers = WalletRecurringTransfer::factory()->for($wallet)->create();

    $response = actingAs($user)->get(route('recurringtransfers.index'));

    $response
        ->assertOk()
        ->assertSeeTextInOrder([
            __('Recurring transfers'),
            'My recurring transfers',
        ]);
});

test('create a recurring transfer', function () {
    $user = User::factory()->has(Wallet::factory()->richChillGuy())->create();

    $data = [
        'user_id' => $user->id,
        'start_date' => now(),
        'end_date' => now()->addDays(30),
        'frequency' => 10,
        'recipient_email' => 'test@example.com',
        'amount' => 5,
        'reason' => 'test reason',
    ];

    $response = actingAs($user)
        ->from(route('recurringtransfers.index'))
        ->post(route('recurringtransfers.store'), $data);

    $response
        ->assertRedirect(route('recurringtransfers.index'))
        ->assertSessionHas('recurring-transfer-status', 'created');

    actingAs($user)->get(route('recurringtransfers.index'))
        ->assertSeeTextInOrder([
            __('My recurring transfers'),
            $data['start_date']->format('Y-m-d'),
            $data['end_date']->format('Y-m-d'),
            $data['frequency'],
            $data['recipient_email'],
            $data['amount'],
            $data['reason'],
        ]);
});

test('delete a recurring transfer', function () {
    $user = User::factory()->has(Wallet::factory()->richChillGuy())->create();

    $data = [
        'source_id' => $user->wallet->id,
        'start_date' => now(),
        'end_date' => now()->addDays(30),
        'frequency' => 10,
        'recipient_email' => 'test@example.com',
        'amount' => 5,
        'reason' => 'test reason',
    ];

    $recurringTransfer = WalletRecurringTransfer::factory()->create($data);

    actingAs($user)->get(route('recurringtransfers.index'))
        ->assertSeeTextInOrder([
            __('My recurring transfers'),
            $data['start_date']->format('Y-m-d'),
            $data['end_date']->format('Y-m-d'),
            $data['frequency'],
            $data['recipient_email'],
            $data['amount'],
            $data['reason'],
        ]);

    $response = actingAs($user)
        ->from(route('recurringtransfers.index'))
        ->delete(route('recurringtransfers.destroy', $recurringTransfer->id));

    $response
        ->assertRedirect(route('recurringtransfers.index'))
        ->assertSessionHas('recurring-transfer-status', 'deleted');

    actingAs($user)->get(route('recurringtransfers.index'))
        ->assertDontSeeText(
            $data['reason']
        );
});
