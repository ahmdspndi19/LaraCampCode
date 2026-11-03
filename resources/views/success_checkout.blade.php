@extends('layout.app')

@section('title', 'Berhasil Checkout')

@section('content')
    <section class="checkout">
        <div class="container">
            <div class="row text-center">
                <div class="col-lg-12 col-12">
                    <img src="{{ asset('images/ill_register.png') }}" height="400" class="mb-5" alt="">
                </div>
                <div class="col-lg-12 col-12 header-wrap mt-4">
                    <p class="story">WHAT A DAY!</p>
                    <h2 class="primary-header">Berhasil Checkout</h2>
                    <p class="subheader">Selesaikan pembayaran agar bootcamp kamu aktif.</p>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        @if (session('invoice'))
                            <a href="{{ route('invoice', session('invoice')) }}" class="btn btn-secondary">Lihat Invoice</a>
                        @endif
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">My Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
