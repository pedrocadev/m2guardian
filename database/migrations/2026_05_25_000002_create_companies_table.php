<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 180);
            $table->char('cnpj', 14)->nullable()->unique();
            $table->string('slug', 60)->unique();
            $table->enum('license', ['demo', 'pro'])->default('demo');
            $table->unsignedInteger('max_collaborators')->default(3);
            $table->enum('status', ['active', 'suspended', 'expired'])->default('active');
            $table->timestamp('license_expires_at')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email', 180)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by_admin_id')->constrained('admins')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['license', 'status']);
            $table->index('created_by_admin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
