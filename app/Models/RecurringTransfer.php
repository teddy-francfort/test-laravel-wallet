<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringTransfer extends Model
{
    /** @use HasFactory<\Database\Factories\RecurringTransferFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'frequency',
        'recipient_email',
        'amount',
        'reason',
    ];
}
