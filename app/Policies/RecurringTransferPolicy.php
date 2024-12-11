<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\RecurringTransfer;
use App\Models\User;

class RecurringTransferPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RecurringTransfer $recurringTransfer): bool
    {
        return $recurringTransfer->user()->is($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RecurringTransfer $recurringTransfer): bool
    {
        return $recurringTransfer->user()->is($user);
    }
}
