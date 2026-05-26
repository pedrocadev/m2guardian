<?php

namespace Database\Factories;

use App\Models\Collaborator;
use App\Models\Company;
use App\Models\Leader;
use Illuminate\Database\Eloquent\Factories\Factory;

class CollaboratorFactory extends Factory
{
    protected $model = Collaborator::class;

    public function definition(): array
    {
        return [
            'company_id'          => Company::factory(),
            'invited_by_leader_id'=> Leader::factory(),
            'name'                => fake()->name(),
            'email'               => fake()->unique()->safeEmail(),
            'department'          => fake()->randomElement(['RH', 'Financeiro', 'TI', 'Operação']),
            'profile'             => fake()->randomElement(['rh', 'financeiro', 'operacao', 'outro']),
            'invited_at'          => now(),
        ];
    }
}
