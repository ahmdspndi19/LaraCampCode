@extends('layout.app')

@section('content')
<section class="dashboard my-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-12 header-wrap mt-4">
                <p class="story">DASHBOARD</p>
                <h2 class="primary-header">My Bootcamps</h2>
            </div>
        </div>
        <div class="row my-5">
            <table class="table">
                <tbody>
                    @forelse ($checkouts as $checkout)
                    <tr class="align-middle">
                        <td width="18%">
                            <img src="{{ asset('images/item_bootcamp.png') }}" height="120" alt="">
                        </td>
                        <td>
                            <p class="mb-2"><strong>{{ $checkout->camp->title }}</strong></p>
                            <p class="mb-0">{{ $checkout->created_at->format('F d, Y') }}</p>
                        </td>
                        <td>
                            <strong>${{ number_format($checkout->camp->price) }}</strong>
                        </td>
                        <td>
                            <strong class="{{ $checkout->payment_status === 'paid' ? 'text-green' : ($checkout->payment_status === 'canceled' ? 'text-red' : '') }}">
                                {{ $checkout->status_label }}
                            </strong>
                        </td>
                        <td>
                            <a href="{{ route('invoice', $checkout) }}" class="btn btn-primary">View Invoice</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            Belum ada bootcamp. <a href="{{ route('welcome') }}">Pilih program</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection