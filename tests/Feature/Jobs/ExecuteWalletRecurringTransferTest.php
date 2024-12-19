<?php

declare(strict_types=1);

use App\Actions\PerformWalletTransfer;
use App\Exceptions\InsufficientBalance;
use App\Exceptions\RecipientEmailUserNotFoundException;
use App\Jobs\ExecuteWalletRecurringTransfer;
use App\Models\Wallet;
use App\Models\WalletRecurringTransfer;
use App\Models\WalletTransfer;

beforeEach(function () {
    $this->freezeTime();
    //Create another wallet (and user) with an email to send the transfer to
    $this->anotherUser = Wallet::factory()->create()->user;

    $this->frequency = 5;

    //Create recurring transfer
    $this->recurringTransfer = WalletRecurringTransfer::factory()
        ->walletBalance(100)
        ->create([
            'start_date' => now()->subDays($this->frequency),
            'end_date' => now()->addMonth(),
            'frequency' => $this->frequency,
            'recipient_email' => $this->anotherUser->email,
            'amount' => 10,
            'reason' => 'test reason',
        ]);
});

it('makes a transfer', function () {
    expect($this->recurringTransfer->transfers)->toHaveCount(0);

    ExecuteWalletRecurringTransfer::dispatchSync($this->recurringTransfer);

    $this->recurringTransfer->refresh();
    expect($this->recurringTransfer->transfers)->toHaveCount(1);
    $this->assertEquals($this->recurringTransfer->getKey(), $this->recurringTransfer->transfers()->first()->recurring_transfer_id);
});

it('fails if transfer is already made', function () {
    //Create a transfer from recurring
    $currentFrequencyTransfer = WalletTransfer::factory()->create([
        'source_id' => $this->recurringTransfer->source_id,
        'target_id' => $this->anotherUser->wallet->getKey(),
        'recurring_transfer_id' => $this->recurringTransfer->getKey(),
        'amount' => $this->recurringTransfer->amount,
    ]);
    expect($this->recurringTransfer->transfers)->toHaveCount(1);

    $job = (new ExecuteWalletRecurringTransfer($this->recurringTransfer))->withFakeQueueInteractions();
    $job->handle(app(PerformWalletTransfer::class));

    $this->recurringTransfer->refresh();
    $job->assertFailed();
    expect($this->recurringTransfer->transfers)->toHaveCount(1);
    $this->assertEquals($this->recurringTransfer->getKey(), $this->recurringTransfer->transfers()->first()->recurring_transfer_id);
});

it('fails if wallet amount is not sufficient', function () {
    $this->recurringTransfer->updateQuietly([
        'amount' => 200,
    ]);
    $this->recurringTransfer->wallet->updateQuietly([
        'wallet' => 100,
    ]);
    $this->recurringTransfer->refresh();

    expect($this->recurringTransfer->transfers)->toHaveCount(0);

    $job = (new ExecuteWalletRecurringTransfer($this->recurringTransfer))->withFakeQueueInteractions();

    $job->handle(app(PerformWalletTransfer::class));

    $this->recurringTransfer->refresh();
    $job->assertFailed();
    expect($job->job->failedWith)->toBeInstanceOf(InsufficientBalance::class);
    expect($this->recurringTransfer->transfers)->toHaveCount(0);
});

it('fails if recipient email is not found', function () {
    $this->recurringTransfer->updateQuietly([
        'amount' => 10,
        'recipient_email' => 'notexisting@test.com',
    ]);
    $this->recurringTransfer->wallet->updateQuietly([
        'wallet' => 100,
    ]);
    $this->recurringTransfer->refresh();

    expect($this->recurringTransfer->transfers)->toHaveCount(0);

    $job = (new ExecuteWalletRecurringTransfer($this->recurringTransfer))->withFakeQueueInteractions();

    $job->handle(app(PerformWalletTransfer::class));

    $this->recurringTransfer->refresh();
    $job->assertFailed();
    expect($job->job->failedWith)->toBeInstanceOf(RecipientEmailUserNotFoundException::class);
    expect($this->recurringTransfer->transfers)->toHaveCount(0);
});
