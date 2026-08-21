<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adiciona sort_order pra permitir reordenacao manual dos cenarios via admin
 * (drag-and-drop no Filament). A ordem tambem se aplica ao fluxo do colaborador:
 * o CollaboratorController::getScenariosFor() usa sort_order em vez de id.
 *
 * Backfill: sort_order recebe o valor do id atual, preservando a ordem
 * existente. Novos cenarios criados pelo admin herdam sort_order = 0 e ficam
 * no topo ate serem reordenados.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('id')->index();
        });

        DB::statement('UPDATE scenarios SET sort_order = id');
    }

    public function down(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
            $table->dropColumn('sort_order');
        });
    }
};
