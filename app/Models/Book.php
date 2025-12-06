<?php

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

    /* ============================================
       RELATIONS
    ============================================ */

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // 🔥 Livres liés à un utilisateur (bibliothèque)
    public function users()
    {
        return $this->belongsToMany(User::class, 'book_user')->withTimestamps();
    }

    // 🔥 Favoris
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    // 🔥 Progression de lecture
    public function readingProgress()
    {
        return $this->hasMany(ReadingProgress::class);
    }


    /* ============================================
       SCOPES — PRO + OPTIMISÉS
    ============================================ */

    public function scopePublished($q)
    {
        return $q->where('status', 'published');
    }

    public function scopeDraft($q)
    {
        return $q->where('status', 'draft');
    }

    public function scopeFree($q)
    {
        return $q->where('access_type', 'free');
    }

    public function scopePaid($q)
    {
        return $q->where('access_type', 'paid');
    }

    public function scopeAudio($q)
    {
        return $q->whereNotNull('audio_file');
    }

    public function scopePdf($q)
    {
        return $q->whereNotNull('pdf_file');
    }

    public function scopeByCategory($q, $categoryId)
    {
        return $q->where('category_id', $categoryId);
    }

    public function scopeByAuthor($q, $authorId)
    {
        return $q->where('author_id', $authorId);
    }

    public function scopeSearch($q, $keyword)
    {
        return $q->where(function ($query) use ($keyword) {
            $query->where('title', 'LIKE', "%$keyword%")
                  ->orWhere('description', 'LIKE', "%$keyword%")
                  ->orWhereHas('author', fn($a) =>
                      $a->where('name', 'LIKE', "%$keyword%"))
                  ->orWhereHas('category', fn($c) =>
                      $c->where('name', 'LIKE', "%$keyword%"));
        });
    }


    /* ============================================
       EVENTS — GÉNÉRATION SLUG AUTO + SAFE
    ============================================ */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($book) {
            if (!$book->slug) {
                $book->slug = Str::slug($book->title) . '-' . Str::random(5);
            }
        });

        static::updating(function ($book) {
            if ($book->isDirty('title')) {
                $book->slug = Str::slug($book->title) . '-' . Str::random(5);
            }
        });
    }
}
