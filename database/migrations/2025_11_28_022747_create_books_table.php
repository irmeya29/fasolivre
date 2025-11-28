<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            $table->foreignId('author_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // pdf / audio / pdf_audio
            $table->string('format')->default('pdf');

            // free / paid / subscription
            $table->string('access_type')->default('free');

            $table->decimal('price', 10, 2)->default(0);

            $table->string('cover')->nullable();
            $table->string('pdf_file')->nullable();
            $table->string('audio_file')->nullable();

            // draft / published
            $table->string('status')->default('draft');

            $table->timestamp('published_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
