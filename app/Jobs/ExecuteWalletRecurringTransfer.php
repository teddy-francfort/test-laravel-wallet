<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\WalletRecurringTransfer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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
    public function handle(): void
    {
        //
    }
}
