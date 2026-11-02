<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}" type="text/css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <title>Sign In | CampCode</title>
</head>

<body>
    <section class="login-user">
        <div class="left">
            <img src="{{ asset('images/ill_login_new.png') }}" alt="">
        </div>
        <div class="right">
            <a href="{{ route('welcome') }}">
                <img src="{{ asset('images/logo.png') }}" class="logo" alt="CampCode">
            </a>
            <h1 class="header-third">Start Today</h1>
            <p class="subheader">Because tomorrow become never</p>

            <form action="{{ route('login.store') }}" method="POST" class="basic-form">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" id="remember" name="remember" value="1" class="form-check-input">
                    <label for="remember" class="form-check-label">Ingat saya</label>
                </div>
                <button type="submit" class="w-100 btn btn-primary">Sign In</button>
                <p class="mt-3">
                    Belum punya akun? <a href="{{ route('register') }}">Sign Up</a>
                </p>
            </form>

            <img src="{{ asset('images/people.png') }}" class="people" alt="">
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
</body>
</html>