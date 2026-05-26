<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 180)->unique();
            $table->string('password');
            $table->enum('role', ['super', 'operator'])->default('operator');
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->tinyInteger('failed_attempts')->unsigned()->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->enum('status', ['active', 'suspended', 'disabled'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('locked_until');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
