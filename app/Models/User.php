<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Hidden attributes.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * -----------------------------------------
     * RELATION : Livres liés à l'utilisateur
     * -----------------------------------------
     * Livre acheté / gratuit / ajouté à la bibliothèque
     */
    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_user')
                    ->withTimestamps();
    }

    /**
     * Optionnel : livres premium achetés uniquement
     */
    public function purchasedBooks()
    {
        return $this->books()->where('access_type', 'paid');
    }

    public function favorites()
    {
    return $this->belongsToMany(Book::class, 'favorites')->withTimestamps();
    }

    public function readingProgress()
    {
    return $this->hasMany(ReadingProgress::class);
    }

}
