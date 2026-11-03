@extends('layout.app')

@section('title', 'My Bootcamps')

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
            <div class="table-responsive">
                <table class="table align-middle">
                    <tbody>
                        @forelse ($checkouts as $checkout)
                            <tr>
                                <td width="18%">
                                    <img src="{{ asset('images/item_bootcamp.png') }}" height="120" alt="">
                                </td>
                                <td>
                                    <p class="mb-2"><strong>{{ $checkout->camp->title }}</strong></p>
                                    <p class="mb-0">{{ $checkout->created_at->format('F d, Y') }}</p>
                                </td>
                                <td><strong>{{ $checkout->amount_formatted }}</strong></td>
                                <td>
                                    <span class="{{ $checkout->payment_status->badgeClass() }}">
                                        {{ $checkout->payment_status->label() }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('invoice', $checkout) }}" class="btn btn-primary">View Invoice</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    Belum ada bootcamp. <a href="{{ route('welcome') }}#pricing">Pilih program</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
