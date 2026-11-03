<?php

namespace App\Mail\Checkout;

use App\Models\Checkout;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Paid extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Checkout $checkout)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pembayaran diterima ' . $this->checkout->invoice_number,
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.checkout.paid');
    }
}
