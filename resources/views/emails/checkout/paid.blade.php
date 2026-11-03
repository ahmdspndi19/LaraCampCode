<x-mail::message>
# Pembayaran diterima

Halo, {{ $checkout->user->name }}. Pembayaran untuk **{{ $checkout->camp->title }}** sudah dikonfirmasi.

<x-mail::table>
| Item | Detail |
| :--- | :--- |
| Invoice | {{ $checkout->invoice_number }} |
| Total | {{ $checkout->amount_formatted }} |
| Status | {{ $checkout->payment_status->label() }} |
</x-mail::table>

<x-mail::button :url="route('dashboard')">
Buka Dashboard
</x-mail::button>

Selamat belajar,<br>
{{ config('app.name') }}
</x-mail::message>
