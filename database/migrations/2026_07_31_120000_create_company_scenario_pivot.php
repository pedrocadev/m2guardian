<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migra o vinculo scenarios.company_id (belongs-to) para pivot company_scenario (many-to-many).
 *
 * Motivacao: um cenario agora pode ser vinculado a MULTIPLAS empresas. A regra de visibilidade
 * pra colaborador vira: empresa com vinculos → SO os vinculados; sem vinculos → SO os is_default.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_scenario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scenario_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['company_id', 'scenario_id']);
        });

        // Copia vinculos existentes (scenarios.company_id != NULL) pra pivot.
        $rows = DB::table('scenarios')
            ->whereNotNull('company_id')
            ->select('id as scenario_id', 'company_id')
            ->orderBy('id')
            ->get();

        foreach ($rows as $row) {
            DB::table('company_scenario')->insert([
                'company_id'  => $row->company_id,
                'scenario_id' => $row->scenario_id,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // Dropa FK + unique composto de scenarios.company_id em Blueprints isolados com
        // try/catch: banco local do dev pode ter estado parcial de migracoes anteriores;
        // SQLite (testes) exige FK dropada antes do drop de coluna; prod tem tudo intacto.
        try {
            Schema::table('scenarios', fn (Blueprint $t) => $t->dropForeign(['company_id']));
        } catch (\Throwable $e) { /* FK ja nao existia */ }

        try {
            Schema::table('scenarios', fn (Blueprint $t) => $t->dropUnique(['company_id', 'slug']));
        } catch (\Throwable $e) { /* unique composto ja nao existia */ }

        Schema::table('scenarios', function (Blueprint $table) {
            $table->dropColumn('company_id');
            $table->unique('slug'); // slug agora precisa ser unico globalmente
        });
    }

    public function down(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        // Restaura scenarios.company_id pegando o PRIMEIRO vinculo do pivot (por company_id asc).
        // Perda de dados esperada: cenarios com >1 empresa vinculada mantem so a de menor id.
        $rows = DB::table('company_scenario')
            ->select('scenario_id', DB::raw('MIN(company_id) as company_id'))
            ->groupBy('scenario_id')
            ->get();

        foreach ($rows as $row) {
            DB::table('scenarios')->where('id', $row->scenario_id)->update([
                'company_id' => $row->company_id,
            ]);
        }

        Schema::table('scenarios', function (Blueprint $table) {
            $table->unique(['company_id', 'slug']);
        });

        Schema::drop('company_scenario');
    }
};
