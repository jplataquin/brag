@extends('layouts.app')

@section('title', 'Manage Templates')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title m-0">
            <span class="page-title-accent"><i class="bi bi-images"></i></span> MANAGE TEMPLATES
        </h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Search Form -->
    <div class="card bg-dark border-secondary rounded-4 p-4 mb-4" style="background: rgba(10, 10, 30, 0.8);">
        <form action="{{ route('admin.templates.index') }}" method="GET" class="d-flex gap-2">
            <div class="flex-grow-1">
                <input type="text" name="search" class="form-control bg-dark text-white border-secondary" placeholder="Search by Template ID, Title, User Username, or Game Title" value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-neon-cyan px-4">
                <i class="bi bi-search"></i> Search
            </button>
            @if(request()->has('search'))
                <a href="{{ route('admin.templates.index') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </form>
    </div>

    <!-- Templates Table -->
    <div class="card bg-dark bg-opacity-75 border-secondary rounded-4 shadow-lg" style="backdrop-filter: blur(10px);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th scope="col" class="py-3 px-4 text-start">ID</th>
                            <th scope="col" class="py-3">Card Title</th>
                            <th scope="col" class="py-3">User</th>
                            <th scope="col" class="py-3">Game Title</th>
                            <th scope="col" class="py-3">Status</th>
                            <th scope="col" class="py-3 text-end px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td class="px-4 text-start">
                                    <span class="fw-bold" style="color: var(--neon-cyan);">#{{ $template->id }}</span>
                                </td>
                                <td>
                                    {{ $template->card_title }}
                                </td>
                                <td>
                                    @if($template->user)
                                        <span class="text-white">{{ $template->user->username }}</span>
                                    @else
                                        <span class="text-muted">Deleted User</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $template->gameTitle->name ?? 'Deleted Game' }}
                                </td>
                                <td>
                                    @if($template->trashed())
                                        <span class="badge bg-danger">Deleted</span>
                                    @else
                                        <span class="badge bg-success">Active</span>
                                    @endif
                                </td>
                                <td class="px-4 text-end">
                                    <a href="{{ route('admin.templates.edit', $template->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                    No templates found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($templates->hasPages())
            <div class="card-footer border-secondary border-top p-3 d-flex justify-content-center">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
