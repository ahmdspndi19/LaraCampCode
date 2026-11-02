<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Camp extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'slug', 'price'];

    public function benefits(): HasMany
    {
        return $this->hasMany(CampBenefit::class);
    }

    public function checkouts(): HasMany
    {
        return $this->hasMany(Checkout::class);
    }

    // 280000 -> "280K"
    public function getPriceKAttribute(): string
    {
        return ($this->price / 1000) . 'K';
    }
}