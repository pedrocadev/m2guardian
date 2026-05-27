<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite (tests): nao suporta ALTER ENUM; colunas ja sao TEXT, ignorar
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE scenarios MODIFY COLUMN platform ENUM('wapp','teams','email','outro') NOT NULL");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE scenarios MODIFY COLUMN platform ENUM('wapp','teams','email') NOT NULL");
    }
};
