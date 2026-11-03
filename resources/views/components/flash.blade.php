@if (session('success'))
    <div class="container mt-4">
        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

@if ($errors->has('status'))
    <div class="container mt-4">
        <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
            {{ $errors->first('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif
