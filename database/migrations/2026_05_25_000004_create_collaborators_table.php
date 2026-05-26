<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collaborators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('invited_by_leader_id')->constrained('leaders')->restrictOnDelete();
            $table->string('name', 120)->nullable();
            $table->string('email', 180);
            $table->string('department', 80)->nullable();
            $table->enum('profile', ['rh', 'financeiro', 'operacao', 'outro'])->default('outro');
            $table->timestamp('invited_at');
            $table->timestamp('first_access_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedSmallInteger('score')->nullable();
            $table->unsignedSmallInteger('total_questions')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'email']);
            $table->index('completed_at');
            $table->index('profile');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collaborators');
    }
};
