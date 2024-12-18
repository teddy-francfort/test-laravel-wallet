<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ExecuteWalletRecurringTransfer;
use App\Models\WalletRecurringTransfer;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProcessWalletRecurringTransactionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:recurring-transactions:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /** @var Collection<WalletRecurringTransfer> $recurringTransfers */

        //Exclude recurring transfers not started yet or already ended
        //Exclude transfers already made for the day
        $recurringTransfers = WalletRecurringTransfer::query()
            ->whereNotExists(function (Builder $query) {
                $query->select(DB::raw(1))
                    ->from('wallet_transfers')
                    ->whereColumn('wallet_transfers.recurring_transfer_id', 'wallet_recurring_transfers.id');
            })
            ->whereDate('start_date', '<=', Carbon::now()->startOfDay())
            ->whereDate('end_date', '>=', Carbon::now()->startOfDay())
            ->lazyById(100, 'id');

        foreach ($recurringTransfers as $recurringTransfer) {

            //Skip, if the number of days since creation is not a multiple of the frequency
            $numberOfDaysStartingDate = (int) $recurringTransfer->start_date->diffInDays(now()->startOfDay());

            if ($numberOfDaysStartingDate === 0 || $numberOfDaysStartingDate % $recurringTransfer->frequency) {
                continue;
            }

            ExecuteWalletRecurringTransfer::dispatch($recurringTransfer);
        }

        return self::SUCCESS;
    }
}
