<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('webhook_events', function (Blueprint $table) {
            $table->string('event_hash', 64)->nullable()->after('event_id');
            $table->unique('event_hash');
        });
    }

    public function down(): void
    {
        Schema::table('webhook_events', function (Blueprint $table) {
            $table->dropUnique(['event_hash']);
            $table->dropColumn('event_hash');
        });
    }
};
