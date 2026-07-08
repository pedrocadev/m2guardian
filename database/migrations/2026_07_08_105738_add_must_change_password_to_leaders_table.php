<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leaders', function (Blueprint $table) {
            $table->boolean('must_change_password')->default(false)->after('password_set_at');
        });

        // Backfill: quem tem senha mas nunca logou é obrigado a trocar no próximo acesso.
        DB::table('leaders')
            ->whereNotNull('password')
            ->whereNull('last_login_at')
            ->update(['must_change_password' => true]);
    }

    public function down(): void
    {
        Schema::table('leaders', function (Blueprint $table) {
            $table->dropColumn('must_change_password');
        });
    }
};
