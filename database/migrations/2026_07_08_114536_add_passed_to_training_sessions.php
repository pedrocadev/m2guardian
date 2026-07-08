<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('training_sessions', function (Blueprint $table) {
            $table->boolean('passed')->nullable()->after('score');
        });

        // Backfill: sessões já concluídas ganham passed = (score/total >= 80%)
        DB::statement("
            UPDATE training_sessions
            SET passed = CASE
                WHEN completed_at IS NULL OR total_questions = 0 THEN NULL
                WHEN (score * 100.0 / total_questions) >= 80 THEN 1
                ELSE 0
            END
        ");
    }

    public function down(): void
    {
        Schema::table('training_sessions', function (Blueprint $table) {
            $table->dropColumn('passed');
        });
    }
};
