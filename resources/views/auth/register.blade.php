@extends('layouts.app')

@section('title', 'Register | SplitHub')

@section('content')
<section class="auth-shell">
    <div class="auth-card">
        <div class="mb-4">
            <span class="section-label">Start splitting smarter</span>
            <h1 class="h2 fw-bold mt-2">Create your account</h1>
        </div>
        <form method="POST" action="{{ route('register') }}" class="d-grid gap-3">
            @csrf
            <div>
                <label class="form-label">Name</label>
                <input class="form-control form-control-lg" type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div>
                <label class="form-label">Email</label>
                <input class="form-control form-control-lg" type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div>
                <label class="form-label">Mobile</label>
                <input class="form-control form-control-lg" type="text" name="mobile" value="{{ old('mobile') }}">
            </div>
            <div>
                <label class="form-label">Password</label>
                <input class="form-control form-control-lg" type="password" name="password" required>
            </div>
            <div>
                <label class="form-label">Confirm password</label>
                <input class="form-control form-control-lg" type="password" name="password_confirmation" required>
            </div>
            <button class="btn btn-dark btn-lg rounded-pill">Create account</button>
            <p class="mb-0 text-center text-muted">Already registered? <a href="{{ route('login') }}">Login</a></p>
        </form>
    </div>
</section>
@endsection
