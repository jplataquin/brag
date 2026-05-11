@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5 fw-bold text-uppercase" style="color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0, 240, 255, 0.5); font-family: 'Orbitron', sans-serif;">
                <i class="bi bi-shield-lock"></i> Parental Consents
            </h1>
            <p class="text-secondary lead">Review and approve IDs for minor users (Ages 13-17).</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show bg-dark border-success text-success mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show bg-dark border-danger text-danger mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card bg-dark bg-opacity-75 border-secondary mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr class="text-uppercase" style="font-family: 'Orbitron', sans-serif; font-size: 0.8rem; background: rgba(255,255,255,0.05);">
                            <th class="ps-4">User</th>
                            <th>Age</th>
                            <th>Parent / Guardian</th>
                            <th>Submission Date</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingConsents as $user)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $user->avatar_url }}" alt="Avatar" class="rounded-circle me-3" style="width: 40px; height: 40px; border: 1px solid var(--neon-cyan);">
                                        <div>
                                            <div class="fw-bold text-white">{{ $user->username }}</div>
                                            <div class="small text-secondary">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($user->birthdate)->age }}</td>
                                <td>
                                    <div class="fw-bold text-info">{{ $user->parent_firstname }} {{ $user->parent_lastname }}</div>
                                    <div class="small text-secondary">Born: {{ $user->parent_birthdate->format('M j, Y') }} ({{ \Carbon\Carbon::parse($user->parent_birthdate)->age }} yrs)</div>
                                </td>
                                <td>{{ $user->created_at->format('M j, Y H:i') }}</td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.parental_consents.view_id', $user) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-image"></i> View ID
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                onclick="confirmAction('{{ route('admin.parental_consents.approve', $user) }}', 'Approve parental consent for {{ $user->username }}?')">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmAction('{{ route('admin.parental_consents.reject', $user) }}', 'REJECT and PURGE account for {{ $user->username }}? This action is permanent.')">
                                            <i class="bi bi-trash"></i> Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-secondary">
                                    <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                    No pending parental consents found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $pendingConsents->links() }}
    </div>
</div>

<form id="action-form" method="POST" style="display: none;">
    @csrf
</form>

@endsection

@section('scripts')
<script>
function confirmAction(url, message) {
    window.neonConfirm(message, 'CONFIRM ACTION').then((confirmed) => {
        if (confirmed) {
            const form = document.getElementById('action-form');
            form.action = url;
            form.submit();
        }
    });
}
</script>
@endsection
