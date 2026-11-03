<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Camp;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Checkout>
 */
class CheckoutFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'camp_id' => Camp::factory(),
            'card_last_four' => fake()->numerify('####'),
            'amount' => 140000,
            'expired' => now()->addYear()->endOfMonth()->toDateString(),
            'payment_status' => PaymentStatus::Waiting,
            'paid_at' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'payment_status' => PaymentStatus::Paid,
            'paid_at' => now(),
        ]);
    }
}
