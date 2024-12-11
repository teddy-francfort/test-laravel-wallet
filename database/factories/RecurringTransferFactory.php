<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RecurringTransfer>
 */
class RecurringTransferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'frequency' => 10,
            'recipient_email' => 'test@test.com',
            'amount' => 10,
            'reason' => 'test reason',
        ];
    }
}
