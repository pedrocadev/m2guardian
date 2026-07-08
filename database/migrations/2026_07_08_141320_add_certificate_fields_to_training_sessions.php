<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('training_sessions', function (Blueprint $table) {
            $table->string('certificate_name', 120)->nullable()->after('passed');
            $table->timestamp('certificate_issued_at')->nullable()->after('certificate_name');
        });
    }

    public function down(): void
    {
        Schema::table('training_sessions', function (Blueprint $table) {
            $table->dropColumn(['certificate_name', 'certificate_issued_at']);
        });
    }
};
