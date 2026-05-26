<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->unique()->constrained('companies')->cascadeOnDelete();
            $table->string('brand_logo_url', 255)->nullable();
            $table->char('brand_primary_color', 7)->nullable();
            $table->string('email_sender_name', 120)->nullable();
            $table->text('email_signature')->nullable();
            $table->boolean('notify_leader_on_completion')->default(true);
            $table->boolean('notify_m2_on_completion')->default(true);
            $table->string('notify_m2_email', 180)->nullable();
            $table->string('locale', 8)->default('pt_BR');
            $table->string('timezone', 40)->default('America/Sao_Paulo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
