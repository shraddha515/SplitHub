@extends('layouts.app')

@section('title', $group->name.' | SplitHub')

@section('content')
<section class="app-shell" data-group-id="{{ $group->id }}" data-balance-url="{{ route('ajax.groups.balances', $group) }}">
    <div class="container">
        <div class="group-hero mb-4">
            <div>
                <span class="section-label">{{ ucfirst($group->type) }} group</span>
                <h1 class="fw-bold mt-2 mb-2">{{ $group->name }}</h1>
                <p class="mb-0">{{ $group->description ?: 'Track shared spending, settlement, and member balances.' }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-light rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#memberModal"><i class="bi bi-person-plus me-1"></i> Member</button>
                <button class="btn btn-dark rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#expenseModal"><i class="bi bi-receipt me-1"></i> Expense</button>
                <button class="btn btn-outline-dark rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#settlementModal"><i class="bi bi-check2-circle me-1"></i> Settle</button>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="stat-card accent-blue"><span>Total group expense</span><strong id="groupTotal">₹{{ number_format($summary['total'], 2) }}</strong></div></div>
            <div class="col-md-4"><div class="stat-card accent-green"><span>Members</span><strong>{{ $group->members->count() }}</strong></div></div>
            <div class="col-md-4"><div class="stat-card accent-rose"><span>Settlements</span><strong>{{ $group->settlements->count() }}</strong></div></div>
        </div>

        <div class="row g-4">
            <div class="col-xl-4">
                <div class="panel h-100">
                    <h2 class="h5 fw-bold mb-3">Simplified balances</h2>
                    <div id="balanceList" class="d-grid gap-2">
                        @include('partials.balance-list', ['transfers' => $summary['simplified']])
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="panel h-100">
                    <h2 class="h5 fw-bold mb-3">Recent expenses</h2>
                    <div class="timeline">
                        @forelse ($group->expenses->sortByDesc('expense_date') as $expense)
                            <div class="timeline-item">
                                <span class="category-dot" style="--dot: {{ $expense->category?->color ?? '#0ea5e9' }}"></span>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between gap-3">
                                        <strong>{{ $expense->title }}</strong>
                                        <strong>₹{{ number_format($expense->amount, 2) }}</strong>
                                    </div>
                                    <p class="text-muted small mb-0">{{ optional($expense->expense_date)->format('d M Y') }} · {{ ucfirst($expense->split_type) }} split</p>
                                </div>
                            </div>
                        @empty
                            <div class="empty-mini">No expenses yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="panel h-100">
                    <h2 class="h5 fw-bold mb-3">Members</h2>
                    <div class="member-stack">
                        @foreach ($group->members as $member)
                            <div class="member-pill">
                                <span>{{ str($member->name)->substr(0, 1)->upper() }}</span>
                                <div>
                                    <strong>{{ $member->name }}</strong>
                                    <small>{{ $member->email }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('partials.member-modal')
@include('partials.expense-modal')
@include('partials.settlement-modal')
@endsection
