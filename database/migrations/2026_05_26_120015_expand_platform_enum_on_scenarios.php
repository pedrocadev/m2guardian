<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL/MariaDB: altera o enum diretamente
        DB::statement("ALTER TABLE scenarios MODIFY COLUMN platform ENUM('wapp','teams','email','outro') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE scenarios MODIFY COLUMN platform ENUM('wapp','teams','email') NOT NULL");
    }
};
