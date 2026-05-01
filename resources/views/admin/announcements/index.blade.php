@extends('layouts.app')

@section('title', 'Manage Announcements')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="orbitron text-cyan"><i class="bi bi-megaphone-fill"></i> ANNOUNCEMENTS</h1>
        <a href="{{ route('announcements.create') }}" class="btn btn-neon">
            <i class="bi bi-plus-lg"></i> CREATE NEW
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="neon-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr class="orbitron text-cyan">
                        <th class="ps-4">TITLE</th>
                        <th>STATUS</th>
                        <th>CREATED AT</th>
                        <th class="text-end pe-4">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($announcements as $announcement)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ $announcement->title }}</div>
                                <div class="small text-muted">{{ Str::limit(strip_tags($announcement->content), 100) }}</div>
                            </td>
                            <td>
                                @if($announcement->is_published)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td>{{ $announcement->created_at->format('M j, Y H:i') }}</td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" x-data x-on:click="window.neonConfirm('Are you sure you want to delete this announcement?').then(c => { if(c) document.getElementById('delete-announcement-{{ $announcement->id }}').submit() })">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-announcement-{{ $announcement->id }}" action="{{ route('announcements.destroy', $announcement) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted italic">
                                No announcements found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $announcements->links() }}
    </div>
</div>
@endsection
