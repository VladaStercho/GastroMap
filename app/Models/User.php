<?php

namespace App\Models;

use Database\Factories\UserFactory;
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
        'role',
        'avatar_url',
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

    // Зв'язок: Власник може мати багато закладів
    public function establishments()
    {
        return $this->hasMany(Establishment::class);
    }

    // Зв'язок: Користувач може залишати відгуки
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Хелпер для перевірки ролі
    public function isAdmin(): bool
    {
        return strtolower($this->role) === 'admin';
    }

    public function isOwner(): bool
    {
        return strtolower($this->role) === 'owner';
    }

    public function isUser(): bool
    {
        return strtolower($this->role) === 'user';
    }
}
