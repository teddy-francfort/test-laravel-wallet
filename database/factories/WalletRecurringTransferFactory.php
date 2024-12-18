<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WalletRecurringTransfer>
 */
class WalletRecurringTransferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'source_id' => Wallet::factory(),
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'frequency' => 10,
            'recipient_email' => $this->faker->safeEmail(),
            'amount' => 10,
            'reason' => 'test reason',
        ];
    }
}
