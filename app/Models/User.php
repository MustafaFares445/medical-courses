<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public const TYPE_STUDENT = 'student';
    public const TYPE_ADMIN = 'admin';
    public const TYPE_SUPER_ADMIN = 'super_admin';

    protected $fillable = ['name', 'email', 'password', 'user_type', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    public function isAdmin(): bool
    {
        return in_array($this->user_type, [self::TYPE_ADMIN, self::TYPE_SUPER_ADMIN], true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->user_type === self::TYPE_SUPER_ADMIN;
    }

    public function isStudent(): bool
    {
        return $this->user_type === self::TYPE_STUDENT;
    }

    /** @return HasMany<Order, $this> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** @return HasMany<CourseAccess, $this> */
    public function courseAccesses(): HasMany
    {
        return $this->hasMany(CourseAccess::class);
    }

    /** @return HasMany<BookAccess, $this> */
    public function bookAccesses(): HasMany
    {
        return $this->hasMany(BookAccess::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
