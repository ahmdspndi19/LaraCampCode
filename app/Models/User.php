<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    // is_admin sengaja TIDAK mass-assignable supaya tidak bisa diisi
    // lewat request. Set lewat forceFill() di seeder / tinker.
    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'occupation',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function checkouts(): HasMany
    {
        return $this->hasMany(Checkout::class);
    }
}
