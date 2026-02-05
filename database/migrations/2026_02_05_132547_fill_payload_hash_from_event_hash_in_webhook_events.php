<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Copie event_hash -> payload_hash quand payload_hash est NULL
        DB::statement("UPDATE webhook_events SET payload_hash = event_hash WHERE payload_hash IS NULL AND event_hash IS NOT NULL");
    }

    public function down(): void
    {
        // On ne supprime rien en down (safe)
    }
};
