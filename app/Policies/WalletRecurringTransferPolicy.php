<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\WalletRecurringTransfer;

class WalletRecurringTransferPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WalletRecurringTransfer $recurringTransfer): bool
    {
        return $recurringTransfer->wallet->user->is($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WalletRecurringTransfer $recurringTransfer): bool
    {
        return $recurringTransfer->wallet->user->is($user);
    }
}
