<?php

// app/Models/Book.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'category_id',
        'title',
        'slug',
        'description',
        'format',
        'access_type',
        'price',
        'cover',
        'pdf_file',
        'audio_file',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Lecteurs de ce livre
    public function readers()
    {
        return $this->belongsToMany(User::class, 'book_user')
            ->withPivot(['progress', 'is_favorite'])
            ->withTimestamps();
    }

    /* === Scopes (comme tu avais) === */

    public function scopePublished($q)
    {
        return $q->where('status', 'published');
    }

    public function scopeFree($q)
    {
        return $q->where('access_type', 'free');
    }

    public function scopePaid($q)
    {
        return $q->where('access_type', 'paid');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($book) {
            if (empty($book->slug)) {
                $book->slug = Str::slug($book->title) . '-' . Str::random(4);
            }
        });

        static::updating(function ($book) {
            if ($book->isDirty('title')) {
                $book->slug = Str::slug($book->title) . '-' . Str::random(4);
            }
        });
    }
}
