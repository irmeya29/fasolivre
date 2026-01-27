<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookPurchase extends Model
{
    protected $table = 'book_purchases';

    protected $fillable = [
        'user_id',
        'book_id',
        'payment_id',
        'price',
        'currency',
        'purchased_at',
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
