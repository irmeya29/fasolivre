<?php
// database/migrations/2026_01_26_000005_create_webhook_events_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('webhook_events', function (Blueprint $table) {
      $table->id();
      $table->string('provider')->default('yengapay');
      $table->string('event_id')->nullable()->index(); // si YengaPay en fournit un
      $table->string('signature')->nullable();
      $table->json('payload');
      $table->string('processing_status')->default('received'); // received|processed|failed
      $table->text('error')->nullable();
      $table->timestamps();

      $table->unique(['provider', 'event_id']); // si event_id existe
    });
  }
  public function down(): void { Schema::dropIfExists('webhook_events'); }
};
