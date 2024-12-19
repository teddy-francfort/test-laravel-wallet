<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\PerformWalletTransfer;
use App\Exceptions\InsufficientBalance;
use App\Exceptions\RecipientEmailUserNotFoundException;
use App\Models\User;
use App\Models\WalletRecurringTransfer;
use App\Models\WalletTransfer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class ExecuteWalletRecurringTransfer implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public WalletRecurringTransfer $walletRecurringTransfer,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(PerformWalletTransfer $performWalletTransfer): void
    {
        // Check if the recipient email user exists
        if (User::query()->where('email', $this->walletRecurringTransfer->recipient_email)->doesntExist()) {
            $this->fail(new RecipientEmailUserNotFoundException);

            return;
        }

        // Check if the recurring transfer has already been made
        if (
            WalletTransfer::query()
                ->whereDate('created_at', Carbon::now())
                ->where('recurring_transfer_id', $this->walletRecurringTransfer->getKey())
                ->exists()
        ) {
            $this->fail();

            return;
        }

        try {
            $performWalletTransfer->execute(
                sender: $this->walletRecurringTransfer->wallet->user,
                recipient: User::query()->where('email', $this->walletRecurringTransfer->recipient_email)->sole(),
                amount: $this->walletRecurringTransfer->amount,
                reason: 'recurring transfer',
                walletRecurringTransfer: $this->walletRecurringTransfer
            );
        } catch (InsufficientBalance $insufficientBalanceException) {
            $this->fail($insufficientBalanceException);

            return;
        }
    }
}
