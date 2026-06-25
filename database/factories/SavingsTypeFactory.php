<?php

namespace Database\Factories;

use App\Models\SavingsType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SavingsType>
 */
class SavingsTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'SV-'.strtoupper($this->faker->unique()->bothify('??###')),
            'name' => $this->faker->randomElement([
                'Simpanan Pokok',
                'Simpanan Wajib',
                'Simpanan Sukarela',
            ]),
            'description' => $this->faker->sentence(),
            'is_mandatory' => $this->faker->boolean(40),
            'is_active' => true,
        ];
    }
}
