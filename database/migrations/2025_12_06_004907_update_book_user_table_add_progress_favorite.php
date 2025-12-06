<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_user', function (Blueprint $table) {
            // Progression de lecture (%)
            $table->integer('progress')->default(0)->after('book_id');

            // Favoris
            $table->boolean('is_favorite')->default(false)->after('progress');
        });
    }

    public function down(): void
    {
        Schema::table('book_user', function (Blueprint $table) {
            $table->dropColumn(['progress', 'is_favorite']);
        });
    }
};
