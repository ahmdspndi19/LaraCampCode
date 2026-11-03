<x-mail::message>
# Halo, {{ $checkout->user->name }}

Checkout kamu sudah kami terima. Selesaikan pembayaran agar bootcamp aktif.

<x-mail::table>
| Item | Detail |
| :--- | :--- |
| Invoice | {{ $checkout->invoice_number }} |
| Bootcamp | {{ $checkout->camp->title }} |
| Total | {{ $checkout->amount_formatted }} |
| Status | {{ $checkout->payment_status->label() }} |
</x-mail::table>

<x-mail::button :url="route('invoice', $checkout)">
Lihat Invoice
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
