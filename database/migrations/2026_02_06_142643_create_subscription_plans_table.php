<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 10)->default('XOF');
            $table->unsignedInteger('duration_days')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'price']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
