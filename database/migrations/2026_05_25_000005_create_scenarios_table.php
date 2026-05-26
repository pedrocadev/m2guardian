<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scenarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->enum('platform', ['wapp', 'teams', 'email']);
            $table->string('slug', 60);
            $table->string('label', 120);
            $table->string('avatar', 8);
            $table->char('bg_color', 7);
            $table->string('preview', 255);
            $table->text('intro')->nullable();
            $table->json('content');
            $table->boolean('is_default')->default(false);
            $table->boolean('demo_eligible')->default(false);
            $table->unsignedInteger('version')->default(1);
            $table->enum('status', ['active', 'draft', 'archived'])->default('active');
            $table->foreignId('updated_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'slug']);
            $table->index(['platform', 'is_default', 'status']);
            $table->index('demo_eligible');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scenarios');
    }
};
