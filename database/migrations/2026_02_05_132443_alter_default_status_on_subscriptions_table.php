<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // MySQL: modification du default
        DB::statement("ALTER TABLE subscriptions MODIFY status VARCHAR(255) NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE subscriptions MODIFY status VARCHAR(255) NOT NULL DEFAULT 'active'");
    }
};
