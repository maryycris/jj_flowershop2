@extends('layouts.admin_app')

@section('content')
@push('styles')
<style>
/* Loyalty Cards Styling - matching invoice page hierarchy */
.card-title {
    font-size: 1.1rem !important;
    font-weight: 600;
}

.card-header h5, .card-header h6 {
    font-size: 0.95rem !important;
    font-weight: 600;
}

/* Table styling */
.table {
    font-size: 0.85rem;
    background-color: white;
}

.table thead th {
    font-size: 0.8rem !important;
    font-weight: 600;
    padding: 0.5rem 0.3rem;
    vertical-align: middle;
    background-color: #e6f4ea;
}

.table tbody td {
    font-size: 0.85rem;
    padding: 0.4rem 0.3rem;
    vertical-align: middle;
}

/* Form controls */
.form-control {
    font-size: 0.85rem;
}

.form-label {
    font-size: 0.85rem;
    font-weight: 500;
}

/* Buttons */
.btn-success {
    font-size: 0.85rem;
    padding: 0.35rem 0.7rem;
}

.btn-success i, .btn-success .bi {
    font-size: 0.85rem;
}

.btn-sm {
    font-size: 0.85rem;
    padding: 0.25rem 0.5rem;
}
</style>
@endpush

<div class="container-fluid" style="margin-top: -2rem; padding-top: 0.5rem;">
    <div class="card shadow mb-4">
        <div class="card-header" style="background: #e6f4ea;">
            <h5 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">Loyalty Cards Management</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Stamps</th>
                            <th>Updated</th>
                            <th style="width:260px">Adjust</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cards as $card)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center justify-content-between">
                                    @php
                                        $userName = 'Unknown User';
                                        if (isset($card->user) && $card->user) {
                                            $userName = $card->user->first_name ?? ($card->user->name ?? 'Unknown User');
                                        }
                                    @endphp
                                    <span>{{ $userName }} (ID: {{ $card->user_id }})</span>
                                    <a href="{{ route('admin.loyalty.history', $card) }}" class="btn btn-success btn-sm">
                                        <i class="bi bi-clock-history"></i> History
                                    </a>
                                </div>
                            </td>
                            <td>{{ $card->stamps_count }}/5</td>
                            <td>{{ $card->updated_at->diffForHumans() }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.loyalty.adjust', $card) }}" class="d-flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="delta" class="form-control" value="1" min="-5" max="5" style="width:80px">
                                    <input type="text" name="reason" class="form-control" placeholder="Reason (optional)">
                                    <button class="btn btn-success btn-sm">
                                        <i class="bi bi-check-lg"></i> Apply
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center">No loyalty cards yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($cards->hasPages())
                <x-pagination 
                    :currentPage="$cards->currentPage()" 
                    :totalPages="$cards->lastPage()" 
                    :baseUrl="request()->url()" 
                    :queryParams="request()->query()" 
                />
            @endif
        </div>
    </div>
</div>
@endsection


