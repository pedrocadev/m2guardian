<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_session_id')->constrained('training_sessions')->cascadeOnDelete();
            $table->foreignId('collaborator_id')->constrained('collaborators')->cascadeOnDelete();
            $table->foreignId('scenario_id')->constrained('scenarios')->restrictOnDelete();
            $table->unsignedInteger('scenario_version');
            $table->unsignedSmallInteger('question_index');
            $table->string('chosen_option_key', 8);
            $table->boolean('is_correct');
            $table->unsignedInteger('response_time_ms')->nullable();
            $table->timestamp('answered_at');

            $table->index(['collaborator_id', 'scenario_id']);
            $table->index(['scenario_id', 'is_correct']);
            $table->index('answered_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
