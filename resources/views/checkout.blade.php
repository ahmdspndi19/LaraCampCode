@extends('layout.app')

@section('title', 'Checkout ' . $camp->title)

@section('content')
    <section class="checkout">
        <div class="container">
            <div class="row text-center pb-70">
                <div class="col-lg-12 col-12 header-wrap">
                    <p class="story">YOUR FUTURE CAREER</p>
                    <h2 class="primary-header">Checkout Bootcamp</h2>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-9 col-12">
                    <div class="row">
                        <div class="col-lg-5 col-12 mb-4">
                            <div class="item-bootcamp">
                                <img src="{{ asset('images/item_bootcamp.png') }}" alt="" class="cover">
                                <h1 class="package">{{ strtoupper($camp->title) }}</h1>
                                <p class="description">
                                    {{ $camp->description ?: 'Bootcamp ini akan mengajak Anda untuk belajar penuh mulai dari pengenalan dasar sampai membangun sebuah projek asli' }}
                                </p>
                                @if ($camp->benefits->isNotEmpty())
                                    <ul class="benefit-list">
                                        @foreach ($camp->benefits as $benefit)
                                            <li>{{ $benefit->name }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                                <p class="price-tag">{{ $camp->price_formatted }}</p>
                            </div>
                        </div>
                        <div class="col-lg-1 col-12"></div>
                        <div class="col-lg-6 col-12">
                            <form action="{{ route('checkout.store', $camp) }}" method="POST" class="basic-form" id="checkout-form" novalidate>
                                @csrf

                                @error('camp')
                                    <div class="alert alert-warning">{{ $message }}</div>
                                @enderror

                                <x-form-field name="email" label="Email Address" type="email"
                                    :value="auth()->user()->email" readonly />
                                <x-form-field name="name" label="Full Name"
                                    :value="auth()->user()->name" required autocomplete="name" />
                                <x-form-field name="occupation" label="Occupation"
                                    :value="auth()->user()->occupation" required />
                                <x-form-field name="card_number" label="Card Number"
                                    inputmode="numeric" maxlength="23" placeholder="0000 0000 0000 0000"
                                    autocomplete="off" required />

                                <div class="row">
                                    <div class="col-lg-6 col-12">
                                        <x-form-field name="expired" label="Expired"
                                            inputmode="numeric" maxlength="7" placeholder="MM/YY"
                                            autocomplete="off" required />
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <x-form-field name="cvc" label="CVC"
                                            inputmode="numeric" maxlength="4" autocomplete="off" required />
                                    </div>
                                </div>

                                <button type="submit" class="w-100 btn btn-primary mt-2">
                                    Pay Now &middot; {{ $camp->price_formatted }}
                                </button>
                                <p class="sim-note mt-4 mb-0">
                                    Ini simulasi pembayaran. Jangan masukkan kartu asli. Sistem hanya
                                    menyimpan 4 digit terakhir kartu, bukan nomor lengkap atau CVC.
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (() => {
            const form = document.getElementById('checkout-form');
            if (!form) return;

            const card = form.querySelector('#card_number');
            const exp = form.querySelector('#expired');
            const cvc = form.querySelector('#cvc');
            const btn = form.querySelector('button[type="submit"]');
            const btnLabel = btn.innerHTML;
            const digits = (v) => v.replace(/\D+/g, '');

            // 4242424242424242 -> 4242 4242 4242 4242
            card.addEventListener('input', () => {
                card.value = digits(card.value).slice(0, 19).replace(/(.{4})/g, '$1 ').trim();
            });

            // 1228 -> 12/28
            exp.addEventListener('input', () => {
                const d = digits(exp.value).slice(0, 6);
                exp.value = d.length > 2 ? d.slice(0, 2) + '/' + d.slice(2) : d;
            });

            cvc.addEventListener('input', () => {
                cvc.value = digits(cvc.value).slice(0, 4);
            });

            // Cegah klik ganda saat form dikirim.
            form.addEventListener('submit', () => {
                btn.disabled = true;
                btn.textContent = 'Memproses...';
            });

            // Tombol kembali aktif bila halaman dipulihkan dari cache browser.
            window.addEventListener('pageshow', () => {
                btn.disabled = false;
                btn.innerHTML = btnLabel;
            });
        })();
    </script>
@endpush
