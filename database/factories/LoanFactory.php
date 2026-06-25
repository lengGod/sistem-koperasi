<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Loan>
 */
class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'loan_number' => 'LN-'.$this->faker->unique()->numerify('######'),
            'principal_amount' => $this->faker->numberBetween(1000000, 50000000),
            'interest_rate' => $this->faker->randomFloat(2, 0, 3),
            'term_months' => $this->faker->numberBetween(6, 24),
            'monthly_installment' => $this->faker->numberBetween(150000, 3000000),
            'remaining_balance' => $this->faker->numberBetween(500000, 50000000),
            'disbursed_at' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'status' => $this->faker->randomElement(['draft', 'active', 'completed', 'overdue', 'cancelled']),
            'notes' => $this->faker->optional()->sentence(),
            'created_by' => null,
        ];
    }
}
