<?php

declare(strict_types=1);

use App\Models\RecurringTransfer;
use App\Models\User;
use App\Models\Wallet;

test('a recurring transfer can be created', function () {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->balance(100)->create(['user_id' => $user->id]);
    $data = [
        'user_id' => $user->id,
        'start_date' => now(),
        'end_date' => now()->addDays(30),
        'frequency' => 10,
        'recipient_email' => 'test@example.com',
        'amount' => 5,
        'reason' => 'test reason',
    ];

    $response = $this->actingAs($user)->postJson(route('recurringtransfers.store'), $data);

    $response->assertStatus(201);

    $this->assertDatabaseCount('recurring_transfers', 1);
});

test('a recurring transfer can be delete', function () {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->balance(100)->create(['user_id' => $user->id]);
    $recurringTransfer = RecurringTransfer::factory()->create(['user_id' => $user->id]);

    $this->assertDatabaseCount('recurring_transfers', 1);

    $route = route('recurringtransfers.destroy', ['recurringtransfer' => $recurringTransfer]);
    $response = $this->actingAs($user)->deleteJson($route);

    $response->assertStatus(204);

    $this->assertDatabaseCount('recurring_transfers', 0);
})->skip();
