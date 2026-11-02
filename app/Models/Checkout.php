<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Checkout extends Model
{
    use HasFactory, SoftDeletes;

    public const WAITING = 'waiting';
    public const PAID = 'paid';
    public const CANCELED = 'canceled';

    protected $fillable = [
        'user_id',
        'camp_id',
        'card_number',
        'expired',
        'cvc',
        'payment_status',
    ];

    protected function casts(): array
    {
        return ['expired' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function getInvoiceNumberAttribute(): string
    {
        return 'INV-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    public function getStatusClassAttribute(): string
    {
        return match ($this->payment_status) {
            self::PAID => 'text-green',
            self::CANCELED => 'text-red',
            default => '',
        };
    }

    public function camp(): BelongsTo
    {
        return $this->belongsTo(Camp::class)->withTrashed();
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            self::PAID => 'Success',
            self::CANCELED => 'Canceled',
            default => 'Waiting for Payment',
        };
    }
}
