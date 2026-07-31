<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite (testes) armazena como TEXT, nao precisa alterar enum.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE scenarios MODIFY COLUMN platform ENUM('wapp','teams','email','telegram','outro') NOT NULL");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE scenarios MODIFY COLUMN platform ENUM('wapp','teams','email','outro') NOT NULL");
    }
};
