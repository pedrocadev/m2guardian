<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('releases', function (Blueprint $table) {
            $table->id();
            $table->string('title', 180);
            $table->date('released_at');
            $table->longText('content');           // markdown
            $table->boolean('published')->default(false);
            $table->timestamps();

            $table->index(['published', 'released_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};
