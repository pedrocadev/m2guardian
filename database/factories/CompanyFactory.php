<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $name = fake()->company();
        return [
            'name'                => $name,
            'slug'                => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 9999),
            'license'             => 'demo',
            'max_collaborators'   => 3,
            'status'              => 'active',
            'created_by_admin_id' => Admin::factory(),
        ];
    }
}
