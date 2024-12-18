<?php

declare(strict_types=1);

use App\Jobs\ExecuteWalletRecurringTransfer;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->command = 'wallet:recurring-transactions:process';

    //Create another wallet (and user) with an email to send the transfer to
    $this->anotherUser = \App\Models\Wallet::factory()->create()->user;

    $this->frequency = 5;

    //Create recurring transfer
    $this->recurringTransfer = \App\Models\WalletRecurringTransfer::factory()->create([
        'start_date' => now(),
        'end_date' => now()->addMonth(),
        'frequency' => $this->frequency,
        'recipient_email' => $this->anotherUser->email,
        'amount' => 10,
        'reason' => 'test reason',
    ]);

});

it('run successfully', function () {
    $this->artisan($this->command)->assertSuccessful();
});

test('The recurring transfer job must be dispatched every X days (according to the periodicity defined by the user)', function (int $xDaysAfter, bool $transferShouldBeDispatched) {
    Queue::fake([
        ExecuteWalletRecurringTransfer::class,
    ]);

    // Set time
    $this->travel($xDaysAfter)->days();
    $this->travelTo(now()->setTime(2, 0, 0));

    // Run the command
    $this->artisan($this->command)->assertSuccessful();

    // Assert job dispatched
    if ($transferShouldBeDispatched) {
        //Make sure the job is dispatched
        Queue::assertPushed(function (ExecuteWalletRecurringTransfer $job) {
            return $job->walletRecurringTransfer->is($this->recurringTransfer);
        });
    } else {
        //Make sure the job is not dispatched
        Queue::assertNotPushed(function (ExecuteWalletRecurringTransfer $job) {
            return $job->walletRecurringTransfer->is($this->recurringTransfer);
        });
    }
})->with([
    'not dispatched if day of creation' => [0, false],
    'dispatched first frequency date' => [5, true],
    'not dispatched if not frequency date' => [8, false],
    'dispatched if frequency date' => [10, true],
]);

test('A recurring transfer job must not be dispatched if start date is in the future', function () {
    Queue::fake([
        ExecuteWalletRecurringTransfer::class,
    ]);

    $this->recurringTransfer->updateQuietly([
        'start_date' => now()->addDays(30),
        'end_date' => now()->addYear(),
    ]);

    // Set time
    $this->travel(5)->days();

    // Run the command
    $this->artisan($this->command)->assertSuccessful();

    //Make sure the job is not dispatched
    Queue::assertNotPushed(function (ExecuteWalletRecurringTransfer $job) {
        return $job->walletRecurringTransfer->is($this->recurringTransfer);
    });
});

test('A recurring transfer job must not be dispatched if end date is in the past', function () {
    Queue::fake([
        ExecuteWalletRecurringTransfer::class,
    ]);

    $this->recurringTransfer->updateQuietly([
        'start_date' => now()->subDays(90),
        'end_date' => now()->subDays(30),
    ]);

    // Set time
    $this->travel(5)->days();

    // Run the command
    $this->artisan($this->command)->assertSuccessful();

    //Make sure the job is not dispatched
    Queue::assertNotPushed(function (ExecuteWalletRecurringTransfer $job) {
        return $job->walletRecurringTransfer->is($this->recurringTransfer);
    });
});

test('A recurring transfer job must not be dispatched if already made', function () {
    Queue::fake([
        ExecuteWalletRecurringTransfer::class,
    ]);

    $this->recurringTransfer->updateQuietly([
        'start_date' => now()->subDays(90),
        'end_date' => now()->addYear(),
    ]);

    // Set time
    $this->travel(5)->days();

    //Create a transfer from recurring
    $currentFrequencyTransfer = \App\Models\WalletTransfer::factory()->create([
        'source_id' => $this->recurringTransfer->source_id,
        'target_id' => $this->anotherUser->wallet->getKey(),
        'recurring_transfer_id' => $this->recurringTransfer->getKey(),
        'amount' => $this->recurringTransfer->amount,
    ]);

    // Run the command
    $this->artisan($this->command)->assertSuccessful();

    //Make sure the job is not dispatched
    Queue::assertNotPushed(function (ExecuteWalletRecurringTransfer $job) {
        return $job->walletRecurringTransfer->is($this->recurringTransfer);
    });
});
