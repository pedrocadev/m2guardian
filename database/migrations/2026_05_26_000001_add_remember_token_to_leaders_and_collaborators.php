<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leaders', function (Blueprint $table) {
            if (!Schema::hasColumn('leaders', 'remember_token')) {
                $table->rememberToken();
            }
        });

        Schema::table('collaborators', function (Blueprint $table) {
            if (!Schema::hasColumn('collaborators', 'remember_token')) {
                $table->rememberToken();
            }
        });
    }

    public function down(): void
    {
        Schema::table('leaders', function (Blueprint $table) {
            $table->dropColumn('remember_token');
        });

        Schema::table('collaborators', function (Blueprint $table) {
            $table->dropColumn('remember_token');
        });
    }
};
