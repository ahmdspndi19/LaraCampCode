@extends('layout.app')

@section('title', 'Admin - All Checkouts')

@section('content')
<section class="dashboard my-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-12 header-wrap mt-4">
                <p class="story">ADMIN</p>
                <h2 class="primary-header">All Checkouts</h2>
            </div>
        </div>

        <div class="row mt-4 g-3">
            <div class="col-lg-3 col-6">
                <div class="summary-card">
                    <p class="story mb-1">WAITING</p>
                    <h3>{{ $summary['waiting'] }}</h3>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="summary-card">
                    <p class="story mb-1">PAID</p>
                    <h3 class="text-green">{{ $summary['paid'] }}</h3>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="summary-card">
                    <p class="story mb-1">CANCELED</p>
                    <h3 class="text-red">{{ $summary['canceled'] }}</h3>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="summary-card">
                    <p class="story mb-1">REVENUE</p>
                    <h3>Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-2 mt-4 filter-bar">
            <div class="col-12 col-md-auto">
                <select name="status" class="form-select" aria-label="Filter status">
                    <option value="">Semua status</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s->value }}" @selected($status === $s)>{{ $s->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-auto">
                <input type="search" name="q" value="{{ $search }}" class="form-control" placeholder="Cari nama / email">
            </div>
            <div class="col-12 col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                @if ($status || $search !== '')
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </form>

        <div class="row my-4">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>User</th>
                            <th>Bootcamp</th>
                            <th>Price</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($checkouts as $checkout)
                            <tr>
                                <td>{{ $checkout->invoice_number }}</td>
                                <td>
                                    {{ $checkout->user->name }}<br>
                                    <small>{{ $checkout->user->email }}</small>
                                </td>
                                <td>{{ $checkout->camp->title }}</td>
                                <td>{{ $checkout->amount_formatted }}</td>
                                <td>{{ $checkout->created_at->format('d M Y') }}</td>
                                <td>
                                    <span class="{{ $checkout->payment_status->badgeClass() }}">
                                        {{ $checkout->payment_status->label() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('invoice', $checkout) }}" class="btn btn-secondary btn-sm">Invoice</a>

                                        @if ($checkout->payment_status === \App\Enums\PaymentStatus::Waiting)
                                            <form action="{{ route('admin.checkout.update', $checkout) }}" method="POST"
                                                  onsubmit="return confirm('Tandai {{ $checkout->invoice_number }} sebagai lunas?')">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="payment_status" value="paid">
                                                <button type="submit" class="btn btn-primary btn-sm">Mark Paid</button>
                                            </form>
                                            <form action="{{ route('admin.checkout.update', $checkout) }}" method="POST"
                                                  onsubmit="return confirm('Batalkan {{ $checkout->invoice_number }}?')">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="payment_status" value="canceled">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Cancel</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    {{ ($status || $search !== '') ? 'Tidak ada checkout yang cocok dengan filter.' : 'Belum ada checkout.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $checkouts->links() }}
        </div>
    </div>
</section>
@endsection
