<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('nickname', 80)->nullable()->after('name');
        });

        // Backfill: empresas existentes recebem o name atual como apelido inicial
        DB::table('companies')
            ->whereNull('nickname')
            ->update(['nickname' => DB::raw('name')]);
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('nickname');
        });
    }
};
