<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Leader;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaderFactory extends Factory
{
    protected $model = Leader::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name'       => fake()->name(),
            'email'      => fake()->unique()->safeEmail(),
            'status'     => 'active',
        ];
    }
}
