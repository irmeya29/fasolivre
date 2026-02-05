<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE webhook_events MODIFY event_hash VARCHAR(64) NOT NULL");
        DB::statement("ALTER TABLE webhook_events MODIFY payload_hash VARCHAR(64) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE webhook_events MODIFY event_hash VARCHAR(64) NULL");
        DB::statement("ALTER TABLE webhook_events MODIFY payload_hash VARCHAR(64) NULL");
    }
};
