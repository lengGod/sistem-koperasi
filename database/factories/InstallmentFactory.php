<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\Installment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Installment>
 */
class InstallmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'loan_id' => Loan::factory(),
            'installment_number' => $this->faker->numberBetween(1, 24),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'principal_amount' => $this->faker->numberBetween(100000, 2000000),
            'interest_amount' => $this->faker->numberBetween(5000, 250000),
            'amount' => $this->faker->numberBetween(100000, 2500000),
            'paid_amount' => $this->faker->numberBetween(0, 2500000),
            'paid_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now')?->format('Y-m-d'),
            'status' => $this->faker->randomElement(['pending', 'partial', 'paid', 'late']),
            'notes' => $this->faker->optional()->sentence(),
            'created_by' => null,
        ];
    }
}
