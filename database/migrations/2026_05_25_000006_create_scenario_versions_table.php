<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scenario_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained('scenarios')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->json('content_snapshot');
            $table->foreignId('edited_by_admin_id')->constrained('admins')->restrictOnDelete();
            $table->string('edit_summary', 255)->nullable();
            $table->timestamp('created_at');

            $table->unique(['scenario_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scenario_versions');
    }
};
