<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Establishment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'search_keywords',
        'type',
        'phone',
        'description',
        'address',
        'city',
        'latitude',
        'longitude',
        'average_check',
        'has_wifi',
        'has_terrace',
        'is_pet_friendly',
        'laptop_friendly',
        'is_approved',
        'menu_pdf',
        'opening_time',
        'closing_time',
        'photos',
    ];

    protected $casts = [
        'has_wifi'       => 'boolean',
        'has_terrace'    => 'boolean',
        'is_pet_friendly'=> 'boolean',
        'laptop_friendly'=> 'boolean',
        'is_approved'    => 'boolean',
        'photos'         => 'array', // Автоматично перетворює JSON з бази в масив PHP
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
