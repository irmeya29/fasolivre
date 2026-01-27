<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'payable_type','payable_id',
        'provider','provider_intent_id','reference',
        'provider_project_id','provider_group_id',
        'amount','fees','currency',
        'status','is_used',
        'checkout_url','token',
        'provider_payload','paid_at',
    ];

    protected $casts = [
        'provider_payload' => 'array',
        'paid_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function payable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
