@extends('layouts.app')

@section('title', 'Login | SplitHub')

@section('content')
<section class="auth-shell">
    <div class="auth-card">
        <div class="mb-4">
            <span class="section-label">Welcome back</span>
            <h1 class="h2 fw-bold mt-2">Log in to SplitHub</h1>
        </div>
        <form method="POST" action="{{ route('login') }}" class="d-grid gap-3">
            @csrf
            <div>
                <label class="form-label">Email</label>
                <input class="form-control form-control-lg" type="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div>
                <label class="form-label">Password</label>
                <input class="form-control form-control-lg" type="password" name="password" required>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <button class="btn btn-dark btn-lg rounded-pill">Login</button>
            <p class="mb-0 text-center text-muted">New here? <a href="{{ route('register') }}">Create an account</a></p>
        </form>
    </div>
</section>
@endsection
