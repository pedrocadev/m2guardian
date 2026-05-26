<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminFactory extends Factory
{
    protected $model = Admin::class;

    public function definition(): array
    {
        return [
            'name'            => fake()->name(),
            'email'           => fake()->unique()->safeEmail(),
            'password'        => bcrypt('password'),
            'role'            => 'operator',
            'status'          => 'active',
            'failed_attempts' => 0,
        ];
    }
}
