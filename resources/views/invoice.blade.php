@extends('layout.app')

@section('title', 'Invoice ' . $checkout->invoice_number)

@section('content')
<section class="my-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-12">
                <div class="d-flex justify-content-between align-items-start mb-5">
                    <div>
                        <p class="story mb-1">INVOICE</p>
                        <h2 class="primary-header">{{ $checkout->invoice_number }}</h2>
                        <p class="mb-0">{{ $checkout->created_at->format('F d, Y') }}</p>
                    </div>
                    <span class="{{ $checkout->payment_status->badgeClass() }}">
                        {{ $checkout->payment_status->label() }}
                    </span>
                </div>

                <div class="mb-5">
                    <p class="story mb-1">BILLED TO</p>
                    <p class="mb-0"><strong>{{ $checkout->user->name }}</strong></p>
                    <p class="mb-0">{{ $checkout->user->email }}</p>
                    @if ($checkout->user->occupation)
                        <p class="mb-0">{{ $checkout->user->occupation }}</p>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Bootcamp</th>
                                <th class="text-end">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $checkout->camp->title }}</td>
                                <td class="text-end">{{ $checkout->amount_formatted }}</td>
                            </tr>
                            <tr>
                                <td class="text-end"><strong>Total</strong></td>
                                <td class="text-end"><strong>{{ $checkout->amount_formatted }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p class="mt-4 mb-1">Kartu: {{ $checkout->masked_card }}</p>
                @if ($checkout->paid_at)
                    <p class="mb-0">Dibayar pada: {{ $checkout->paid_at->format('F d, Y H:i') }}</p>
                @endif

                <div class="no-print mt-5 d-flex gap-2">
                    <button onclick="window.print()" class="btn btn-primary">Print</button>
                    <a href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('dashboard') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
