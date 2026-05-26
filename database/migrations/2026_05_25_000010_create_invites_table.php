<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invites', function (Blueprint $table) {
            $table->id();
            $table->string('invitable_type', 60);
            $table->unsignedBigInteger('invitable_id');
            $table->string('sent_to_email', 180);
            $table->string('sent_by_type', 60);
            $table->unsignedBigInteger('sent_by_id');
            $table->foreignId('magic_link_id')->nullable()->constrained('magic_links')->nullOnDelete();
            $table->enum('status', ['queued', 'sent', 'delivered', 'bounced', 'opened'])->default('queued');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->text('error_message')->nullable();
            $table->tinyInteger('retry_count')->unsigned()->default(0);
            $table->timestamps();

            $table->index(['invitable_type', 'invitable_id']);
            $table->index('status');
            $table->index('sent_to_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invites');
    }
};
