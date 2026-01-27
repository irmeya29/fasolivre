<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Ajouter payload_hash
        Schema::table('webhook_events', function (Blueprint $table) {
            $table->string('payload_hash', 64)->nullable()->after('signature')->index();
        });

        // 2) Supprimer l'unique provider+event_id (nom exact Laravel)
        Schema::table('webhook_events', function (Blueprint $table) {
            $table->dropUnique('webhook_events_provider_event_id_unique');
        });

        // 3) Mettre un unique provider+payload_hash
        Schema::table('webhook_events', function (Blueprint $table) {
            $table->unique(['provider', 'payload_hash'], 'webhook_events_provider_payloadhash_unique');
        });
    }

    public function down(): void
    {
        Schema::table('webhook_events', function (Blueprint $table) {
            $table->dropUnique('webhook_events_provider_payloadhash_unique');
        });

        Schema::table('webhook_events', function (Blueprint $table) {
            $table->dropColumn('payload_hash');
        });

        // Remettre l'unique original
        Schema::table('webhook_events', function (Blueprint $table) {
            $table->unique(['provider', 'event_id'], 'webhook_events_provider_event_id_unique');
        });
    }
};
