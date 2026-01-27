<?php
// database/migrations/2026_01_26_000004_create_book_purchases_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('book_purchases', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->cascadeOnDelete();
      $table->foreignId('book_id')->constrained()->cascadeOnDelete();
      $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();

      $table->decimal('price', 10, 2)->default(0);
      $table->string('currency', 10)->default('XOF');
      $table->timestamp('purchased_at')->nullable();

      $table->timestamps();

      $table->unique(['user_id', 'book_id']); // pas de double achat
    });
  }
  public function down(): void { Schema::dropIfExists('book_purchases'); }
};
