<?php
// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Livres de la bibliothèque de l'utilisateur.
     */
    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_user')
            ->withPivot(['progress', 'is_favorite'])
            ->withTimestamps();
    }

    /**
     * Livres favoris (via pivot).
     */
    public function favoriteBooks()
    {
        return $this->books()->wherePivot('is_favorite', true);
    }
}
