<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    protected $fillable = [
        'provider',
        'event_id',
        'event_hash',
        'payload_hash',      // ✅ AJOUT
        'signature',
        'payload',
        'processing_status',
        'error',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
