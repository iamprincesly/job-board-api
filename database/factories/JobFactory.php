<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::inRandomOrder()->first(),
            'title' => $this->faker->paragraph(),
            'description' => $this->faker->sentence(),
            'location' => $this->faker->word(),
            'salary_range' => rand(100, 1999999),
            'is_remote' => $this->faker->randomElement([true, false]),
            'published_at' => $this->faker->randomElement([null, now()])
        ];
    }
}
