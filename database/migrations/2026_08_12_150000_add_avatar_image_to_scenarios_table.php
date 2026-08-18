<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            // Path relativo do disco public (ex: 'scenarios/avatars/abc123.jpg').
            // Nullable: cenarios existentes continuam usando o campo 'avatar' (emoji) como fallback.
            $table->string('avatar_image', 255)->nullable()->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            $table->dropColumn('avatar_image');
        });
    }
};
