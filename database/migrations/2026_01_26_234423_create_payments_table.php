<?php
// database/migrations/2026_01_26_000003_create_payments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('payments', function (Blueprint $table) {
      $table->id();

      $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

      // Ce que le paiement finance (achat livre / abonnement) via relation polymorphique
      $table->nullableMorphs('payable'); // payable_type, payable_id

      $table->string('provider')->default('yengapay');
      $table->string('provider_intent_id')->nullable()->index();  // ex: "clx36jfpl..."
      $table->string('reference')->unique();                      // TON identifiant
      $table->string('provider_project_id')->default('58192');
      $table->string('provider_group_id')->nullable();            // organization_id

      $table->decimal('amount', 10, 2);                           // montant demandé
      $table->decimal('fees', 10, 2)->default(0);
      $table->string('currency', 10)->default('XOF');

      $table->string('status')->default('PENDING');               // PENDING|SUCCESS|FAILED|CANCELLED...
      $table->boolean('is_used')->default(false);

      $table->text('checkout_url')->nullable();
      $table->text('token')->nullable();                          // sensible → évite de logger

      $table->json('provider_payload')->nullable();               // réponse brute create intent
      $table->timestamp('paid_at')->nullable();

      $table->timestamps();

      $table->index(['provider', 'status']);
    });
  }

  public function down(): void { Schema::dropIfExists('payments'); }
};
