@extends('layouts.app')

@section('title', 'Manage Digital Cards')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title m-0">
            <span class="page-title-accent"><i class="bi bi-cpu"></i></span> MANAGE DIGITAL CARDS
        </h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Search Form -->
    <div class="card bg-dark border-secondary rounded-4 p-4 mb-4" style="background: rgba(10, 10, 30, 0.8);">
        <form action="{{ route('admin.cards.index') }}" method="GET" class="d-flex gap-2">
            <div class="flex-grow-1">
                <input type="text" name="search" class="form-control bg-dark text-white border-secondary" placeholder="Search by Card ID, Owner Username, or Template Name" value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-neon-cyan px-4">
                <i class="bi bi-search"></i> Search
            </button>
            @if(request()->has('search'))
                <a href="{{ route('admin.cards.index') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </form>
    </div>

    <!-- Cards Table -->
    <div class="card bg-dark bg-opacity-75 border-secondary rounded-4 shadow-lg" style="backdrop-filter: blur(10px);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th scope="col" class="py-3 px-4 text-start">Card ID</th>
                            <th scope="col" class="py-3">Template</th>
                            <th scope="col" class="py-3">Owner</th>
                            <th scope="col" class="py-3">Level</th>
                            <th scope="col" class="py-3">Status</th>
                            <th scope="col" class="py-3 text-end px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cards as $card)
                            <tr>
                                <td class="px-4 text-start">
                                    <span class="fw-bold" style="color: var(--neon-yellow);">#{{ $card->id }}</span>
                                </td>
                                <td>
                                    {{ $card->template()->withTrashed()->first()->card_title ?? 'Deleted Template' }}
                                </td>
                                <td>
                                    @if($card->owner)
                                        <span class="text-white">{{ $card->owner->username }}</span>
                                    @else
                                        <span class="text-muted">None</span>
                                    @endif
                                    @if($card->is_trophy)
                                        <i class="bi bi-trophy-fill text-warning ms-1" title="Trophy"></i>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge" style="background: {{ $card->rarity_color }}; color: #000;">{{ $card->level_name }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $card->status === 'Maintained' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $card->status }}
                                    </span>
                                </td>
                                <td class="px-4 text-end">
                                    <a href="{{ route('admin.cards.edit', $card->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                    No digital cards found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($cards->hasPages())
            <div class="card-footer border-secondary border-top p-3 d-flex justify-content-center">
                {{ $cards->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
