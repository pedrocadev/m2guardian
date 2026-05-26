<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('magic_links', function (Blueprint $table) {
            $table->id();
            $table->char('token_hash', 64)->unique();
            $table->string('tokenable_type', 60);
            $table->unsignedBigInteger('tokenable_id');
            $table->enum('purpose', ['leader_login', 'collaborator_training', 'admin_recovery']);
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at');

            $table->index(['tokenable_type', 'tokenable_id']);
            $table->index(['expires_at', 'consumed_at']);
            $table->index('purpose');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('magic_links');
    }
};
