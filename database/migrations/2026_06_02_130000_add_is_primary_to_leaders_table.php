<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leaders', function (Blueprint $table) {
            $table->boolean('is_primary')->default(false)->after('role_label');
        });

        // Backfill: o líder mais antigo (menor id) de cada empresa vira o "primary"
        // historicamente. Isso garante compat retroativa.
        DB::statement("
            UPDATE leaders
            SET is_primary = 1
            WHERE id IN (
                SELECT min_id FROM (
                    SELECT MIN(id) AS min_id
                    FROM leaders
                    WHERE deleted_at IS NULL
                    GROUP BY company_id
                ) AS first_leaders
            )
        ");
    }

    public function down(): void
    {
        Schema::table('leaders', function (Blueprint $table) {
            $table->dropColumn('is_primary');
        });
    }
};
