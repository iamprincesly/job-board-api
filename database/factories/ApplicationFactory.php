<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_job_id' => Job::inRandomOrder()->first(),
            'candidate_id' => Candidate::inRandomOrder()->first(),
            'cover_letter' => $this->faker->sentence(),
            'resume_path' => $this->faker->url()
        ];
    }
}
