@extends('layout.auth')

@section('title', 'Sign In')
@section('heading', 'Start Today')
@section('subheading', 'Because tomorrow become never')

@section('content')
    <form action="{{ route('login.store') }}" method="POST" class="basic-form" novalidate>
        @csrf

        <x-form-field name="email" label="Email Address" type="email" required autofocus autocomplete="email" />
        <x-form-field name="password" label="Password" type="password" required autocomplete="current-password" />

        <div class="form-check mb-3">
            <input type="checkbox" id="remember" name="remember" value="1" class="form-check-input">
            <label for="remember" class="form-check-label">Ingat saya</label>
        </div>

        <button type="submit" class="w-100 btn btn-primary">Sign In</button>
        <p class="mt-3">
            Belum punya akun? <a href="{{ route('register') }}">Sign Up</a>
        </p>
    </form>
@endsection
