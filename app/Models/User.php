<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'preferred_sources',
        'preferred_categories'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function preferredCategories(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_string($value) ? explode(',', $value) : $value,
            set: fn ($value) => is_array($value) ? implode(',', $value) : $value,
        );
    }

    protected function preferredSources(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => is_string($value) ? explode(',', $value) : $value,
            set: fn ($value) => is_array($value) ? implode(',', $value) : $value,
        );
    }
}
