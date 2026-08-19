<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * body foi substituido por slides (JSON) na feature v2 do modal de aprendizado.
 * A coluna fica mantida apenas pra backward-compat (registros antigos que so
 * tinham body). Como o form do admin nao expoe mais o campo, INSERTs novos
 * (factories, futuros seeders, plataformas adicionadas ao enum sem seed)
 * quebravam com NOT NULL constraint violation em MySQL strict mode.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platform_feedbacks', function (Blueprint $table) {
            $table->text('body')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Reverte pra NOT NULL: garante que qualquer NULL existente vira '' primeiro
        // pra o ALTER nao explodir com "cannot be null" em prod.
        \DB::table('platform_feedbacks')->whereNull('body')->update(['body' => '']);

        Schema::table('platform_feedbacks', function (Blueprint $table) {
            $table->text('body')->nullable(false)->change();
        });
    }
};
