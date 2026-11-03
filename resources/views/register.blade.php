@extends('layout.auth')

@section('title', 'Sign Up')
@section('heading', 'Create Account')
@section('subheading', 'Join today, learn something new')

@section('content')
    <form action="{{ route('register.store') }}" method="POST" class="basic-form" novalidate>
        @csrf

        <x-form-field name="name" label="Full Name" required autofocus autocomplete="name" />
        <x-form-field name="email" label="Email Address" type="email" required autocomplete="email" />
        <x-form-field name="occupation" label="Occupation" autocomplete="organization-title" />
        <x-form-field name="password" label="Password" type="password" minlength="8" required autocomplete="new-password" />
        <x-form-field name="password_confirmation" label="Confirm Password" type="password" minlength="8" required autocomplete="new-password" />

        <button type="submit" class="w-100 btn btn-primary">Sign Up</button>
        <p class="mt-3">
            Sudah punya akun? <a href="{{ route('login') }}">Sign In</a>
        </p>
    </form>
@endsection
