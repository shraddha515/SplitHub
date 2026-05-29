@extends('layouts.app')

@section('title', 'Dashboard | SplitHub')

@section('content')
@php
    $totalExpense = $portfolio->sum(fn ($row) => $row['summary']['total']);
    $net = $portfolio->sum(fn ($row) => $row['summary']['balances']->firstWhere('user_id', auth()->id())['balance'] ?? 0);
@endphp

<section class="app-shell">
    <div class="container">
        <div class="dashboard-hero mb-4">
            <div class="dashboard-hero-copy">
                <div>
                    <span class="section-label">Personal dashboard</span>
                    <h1 class="fw-bold mt-2 mb-0">Hi {{ auth()->user()->name }}, your money map is ready.</h1>
                </div>
                <button class="btn btn-dark rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createGroupModal">
                    <i class="bi bi-plus-lg me-1"></i> New group
                </button>
            </div>
            <div class="dashboard-hero-image">
                <img src="{{ asset('images/splithub-hero.png') }}" alt="SplitHub split payment dashboard preview">
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card accent-blue">
                    <span>Total tracked</span>
                    <strong>₹{{ number_format($totalExpense, 2) }}</strong>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card accent-green">
                    <span>Net position</span>
                    <strong>{{ $net >= 0 ? '+' : '-' }}₹{{ number_format(abs($net), 2) }}</strong>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card accent-rose">
                    <span>Active groups</span>
                    <strong>{{ $groups->count() }}</strong>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="panel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 fw-bold mb-0">Groups</h2>
                        <a href="{{ route('groups.index') }}" class="btn btn-sm btn-light rounded-pill">View all</a>
                    </div>
                    <div class="row g-3">
                        @forelse ($portfolio as $row)
                            @php
                                $userBalance = $row['summary']['balances']->firstWhere('user_id', auth()->id())['balance'] ?? 0;
                            @endphp
                            <div class="col-md-6">
                                <a class="group-card d-block text-decoration-none" href="{{ route('groups.show', $row['group']) }}">
                                    <div class="d-flex justify-content-between gap-3">
                                        <div>
                                            <span class="badge text-bg-light">{{ ucfirst($row['group']->type) }}</span>
                                            <h3 class="h5 mt-3 mb-1">{{ $row['group']->name }}</h3>
                                            <p class="text-muted mb-0">{{ $row['group']->members->count() }} members</p>
                                        </div>
                                        <div class="text-end">
                                            <span class="small text-muted">Total</span>
                                            <div class="fw-bold">₹{{ number_format($row['summary']['total'], 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="balance-strip mt-3 {{ $userBalance >= 0 ? 'good' : 'bad' }}">
                                        {{ $userBalance >= 0 ? 'You are owed' : 'You owe' }}
                                        <strong>₹{{ number_format(abs($userBalance), 2) }}</strong>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="bi bi-people"></i>
                                <h3>No groups yet</h3>
                                <p>Create your first trip, flat, office, or event group to start tracking shared costs.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="panel h-100">
                    <h2 class="h5 fw-bold mb-3">Monthly report</h2>
                    <canvas id="monthlyChart" height="280" data-report-url="{{ route('reports.monthly') }}"></canvas>
                </div>
            </div>
        </div>
    </div>
</section>

@include('partials.create-group-modal')
@endsection
