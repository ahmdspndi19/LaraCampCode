<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Camp extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'slug', 'description', 'price'];

    public function benefits(): HasMany
    {
        return $this->hasMany(CampBenefit::class);
    }

    public function checkouts(): HasMany
    {
        return $this->hasMany(Checkout::class);
    }

    // 280000 -> "280K", 150500 -> "150.5K"
    public function getPriceKAttribute(): string
    {
        $k = rtrim(rtrim(number_format($this->price / 1000, 1, '.', ''), '0'), '.');

        return $k . 'K';
    }

    // 280000 -> "Rp 280.000"
    public function getPriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
