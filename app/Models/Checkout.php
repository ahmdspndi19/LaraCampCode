<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Checkout extends Model
{
    use HasFactory, SoftDeletes;

    // Nomor kartu penuh dan CVC TIDAK disimpan. Hanya 4 digit terakhir.
    // "amount" adalah snapshot harga saat checkout, jadi invoice lama
    // tidak berubah walaupun harga camp diubah.
    protected $fillable = [
        'user_id',
        'camp_id',
        'card_last_four',
        'amount',
        'expired',
        'payment_status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'expired' => 'date',
            'paid_at' => 'datetime',
            'payment_status' => PaymentStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function camp(): BelongsTo
    {
        return $this->belongsTo(Camp::class)->withTrashed();
    }

    public function getInvoiceNumberAttribute(): string
    {
        return 'INV-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    public function getAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format((int) $this->amount, 0, ',', '.');
    }

    public function getMaskedCardAttribute(): string
    {
        return '**** **** **** ' . $this->card_last_four;
    }
}
