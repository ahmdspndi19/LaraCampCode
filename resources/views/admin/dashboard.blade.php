@extends('layout.app')

@section('content')
<section class="dashboard my-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-12 header-wrap mt-4">
                <p class="story">ADMIN</p>
                <h2 class="primary-header">All Checkouts</h2>
            </div>
        </div>

        <div class="row mt-4 text-center">
            <div class="col-lg-3 col-6 mb-3">
                <p class="story mb-1">WAITING</p>
                <h3>{{ $summary['waiting'] }}</h3>
            </div>
            <div class="col-lg-3 col-6 mb-3">
                <p class="story mb-1">PAID</p>
                <h3 class="text-green">{{ $summary['paid'] }}</h3>
            </div>
            <div class="col-lg-3 col-6 mb-3">
                <p class="story mb-1">CANCELED</p>
                <h3 class="text-red">{{ $summary['canceled'] }}</h3>
            </div>
            <div class="col-lg-3 col-6 mb-3">
                <p class="story mb-1">REVENUE</p>
                <h3>${{ number_format($summary['revenue']) }}</h3>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger mt-3">{{ $errors->first() }}</div>
        @endif

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
                                <td>${{ number_format($checkout->camp->price) }}</td>
                                <td>{{ $checkout->created_at->format('d M Y') }}</td>
                                <td>
                                    <strong class="{{ $checkout->status_class }}">{{ $checkout->status_label }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('invoice', $checkout) }}" class="btn btn-secondary btn-sm">Invoice</a>

                                        @if ($checkout->payment_status === 'waiting')
                                            <form action="{{ route('admin.checkout.update', $checkout) }}" method="POST">
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
                                <td colspan="7" class="text-center py-5">Belum ada checkout.</td>
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