<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leaders', function (Blueprint $table) {
            $table->string('password', 255)->nullable()->after('email');
            $table->tinyInteger('failed_attempts')->unsigned()->default(0)->after('last_login_ip');
            $table->timestamp('locked_until')->nullable()->after('failed_attempts');
            $table->timestamp('password_set_at')->nullable()->after('locked_until');
        });
    }

    public function down(): void
    {
        Schema::table('leaders', function (Blueprint $table) {
            $table->dropColumn(['password', 'failed_attempts', 'locked_until', 'password_set_at']);
        });
    }
};
