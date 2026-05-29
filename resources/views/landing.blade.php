@extends('layouts.app')

@section('title', 'SplitHub | Smart Expense Sharing')

@section('content')
<section class="hero-wrap">
    <div class="container">
        <div class="row align-items-center g-5 min-vh-hero">
            <div class="col-lg-6">
                <div class="hero-kicker">Shared expenses without the confusion</div>
                <h1 class="display-3 fw-black hero-title">SplitHub</h1>
                <p class="lead hero-copy">Create groups for trips, roommates, events, and teams. Add expenses, split equally or custom-wise, and instantly see the clean settlement path.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a class="btn btn-dark btn-lg rounded-pill px-4" href="{{ route('register') }}">Create account</a>
                    <a class="btn btn-outline-dark btn-lg rounded-pill px-4" href="{{ route('login') }}">Open dashboard</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-visual mx-auto">
                    <span class="hero-pulse pulse-one"></span>
                    <span class="hero-pulse pulse-two"></span>
                    <img src="{{ asset('images/splithub-hero.png') }}" alt="SplitHub split payment dashboard preview">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-band" id="features">
    <div class="container">
        <div class="row g-4">
            @foreach ([
                ['icon' => 'people', 'title' => 'Groups that fit real life', 'body' => 'Trips, flatmates, office lunches, weddings, and recurring team spends.'],
                ['icon' => 'sliders', 'title' => 'Flexible split rules', 'body' => 'Equal, percentage, and custom split support with payer validation.'],
                ['icon' => 'shuffle', 'title' => 'Net balance simplification', 'body' => 'Turns many messy bills into a few clear settlement actions.'],
                ['icon' => 'bar-chart-line', 'title' => 'Reports and charts', 'body' => 'Monthly and category summaries powered by JSON endpoints.'],
            ] as $feature)
                <div class="col-md-6 col-xl-3">
                    <div class="feature-tile h-100">
                        <i class="bi bi-{{ $feature['icon'] }}"></i>
                        <h3>{{ $feature['title'] }}</h3>
                        <p>{{ $feature['body'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
