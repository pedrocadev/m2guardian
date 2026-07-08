<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Removida ao introduzir múltiplas tentativas por colaborador (feature "refazer teste").
        // MariaDB exige um índice antes de dropar o unique para não invalidar a FK.
        Schema::table('training_sessions', function (Blueprint $table) {
            $table->index('collaborator_id', 'training_sessions_collaborator_id_index');
        });

        Schema::table('training_sessions', function (Blueprint $table) {
            $table->dropUnique('training_sessions_collaborator_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('training_sessions', function (Blueprint $table) {
            $table->dropIndex('training_sessions_collaborator_id_index');
            $table->unique('collaborator_id', 'training_sessions_collaborator_id_unique');
        });
    }
};
