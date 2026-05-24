@extends('layouts.app')

@section('title', 'Groups | SplitHub')

@section('content')
<section class="app-shell">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <span class="section-label">Group workspace</span>
                <h1 class="fw-bold mt-2 mb-0">All groups</h1>
            </div>
            <button class="btn btn-dark rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createGroupModal">
                <i class="bi bi-plus-lg me-1"></i> New group
            </button>
        </div>
        <div class="row g-3">
            @forelse ($groups as $group)
                <div class="col-sm-6 col-xl-4">
                    <a href="{{ route('groups.show', $group) }}" class="group-card d-block text-decoration-none h-100">
                        <span class="badge text-bg-light">{{ ucfirst($group->type) }}</span>
                        <h2 class="h4 mt-3">{{ $group->name }}</h2>
                        <p class="text-muted">{{ $group->description ?: 'Shared expense workspace' }}</p>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="mini-chip"><i class="bi bi-people"></i> {{ $group->members_count }} members</span>
                            <span class="mini-chip"><i class="bi bi-receipt"></i> {{ $group->expenses_count }} expenses</span>
                        </div>
                    </a>
                </div>
            @empty
                <div class="empty-state">
                    <i class="bi bi-folder-plus"></i>
                    <h2>No groups yet</h2>
                    <p>Start with a Goa trip, flatmates group, office lunch, or event team.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

@include('partials.create-group-modal')
@endsection
