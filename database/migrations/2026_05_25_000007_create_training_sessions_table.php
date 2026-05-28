<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collaborator_id')->constrained('collaborators')->cascadeOnDelete();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedSmallInteger('total_scenarios');
            $table->unsignedSmallInteger('total_questions');
            $table->unsignedSmallInteger('score')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('client_user_agent', 255)->nullable();
            $table->string('client_ip', 45)->nullable();
            $table->timestamps();

            $table->unique('collaborator_id');
            $table->index('completed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_sessions');
    }
};
