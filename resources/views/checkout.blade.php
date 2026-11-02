@extends('layout.app')

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
                        <div class="col-lg-5 col-12">
                            <div class="item-bootcamp">
                                <img src="{{ asset('images/item_bootcamp.png') }}" alt="" class="cover">
                                <h1 class="package">{{ strtoupper($camp->title) }}</h1>
                                <p class="description">
                                    Bootcamp ini akan mengajak Anda untuk belajar penuh mulai dari pengenalan dasar sampai
                                    membangun sebuah projek asli
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-1 col-12"></div>
                        <div class="col-lg-6 col-12">
                            <form action="{{ route('checkout.store', $camp) }}" method="POST" class="basic-form">
                                @csrf

                                @if ($errors->any())
                                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                                @endif

                                <div class="mb-4">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" id="email" class="form-control"
                                        value="{{ auth()->user()->email }}" readonly>
                                </div>
                                <div class="mb-4">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" id="name" name="name" class="form-control"
                                        value="{{ old('name', auth()->user()->name) }}" required>
                                </div>
                                <div class="mb-4">
                                    <label for="occupation" class="form-label">Occupation</label>
                                    <input type="text" id="occupation" name="occupation" class="form-control"
                                        value="{{ old('occupation', auth()->user()->occupation) }}" required>
                                </div>
                                <div class="mb-4">
                                    <label for="card_number" class="form-label">Card Number</label>
                                    <input type="text" id="card_number" name="card_number" class="form-control"
                                        inputmode="numeric" maxlength="19" value="{{ old('card_number') }}" required>
                                </div>
                                <div class="mb-5">
                                    <div class="row">
                                        <div class="col-lg-6 col-12">
                                            <label for="expired" class="form-label">Expired</label>
                                            <input type="text" id="expired" name="expired" class="form-control"
                                                placeholder="MM/YYYY" maxlength="7" value="{{ old('expired') }}" required>
                                        </div>
                                        <div class="col-lg-6 col-12">
                                            <label for="cvc" class="form-label">CVC</label>
                                            <input type="text" id="cvc" name="cvc" class="form-control"
                                                inputmode="numeric" maxlength="4" value="{{ old('cvc') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="w-100 btn btn-primary">Pay Now</button>
                                <p class="text-center subheader mt-4">
                                    <img src="{{ asset('images/ic_secure.svg') }}" alt=""> Your payment is secure
                                    and encrypted.
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
