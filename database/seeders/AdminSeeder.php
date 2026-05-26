<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'suporte@m2cloud.com.br'],
            [
                'name' => 'M2 Cloud Suporte',
                'password' => Hash::make('M2Guardian@2026'),
                'role' => 'super',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
    }
}
