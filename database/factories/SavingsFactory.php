<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\Savings;
use App\Models\SavingsType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Savings>
 */
class SavingsFactory extends Factory
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
            'savings_type_id' => SavingsType::factory(),
            'transaction_type' => $this->faker->randomElement(['deposit', 'withdrawal']),
            'amount' => $this->faker->numberBetween(50000, 5000000),
            'transaction_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'notes' => $this->faker->optional()->sentence(),
            'reference_number' => 'SVT-'.$this->faker->unique()->numerify('######'),
            'created_by' => null,
        ];
    }
}
