@extends('layouts.clerk_app')
@section('content')
<div class="clerk-dashboard-wrapper d-flex">
    <div class="clerk-sidebar p-4 d-flex flex-column align-items-center" style="min-width:220px;max-width:250px;background:#f8f9f4;height:100vh;">
        <div class="mb-4 text-center">
            <i class="bi bi-person-circle" style="font-size:3.5rem;color:#888;"></i>
            <div class="fw-semibold mt-2 mb-1">Clerk name</div>
        </div>
        <div class="w-100">
            <a href="{{ route('clerk.dashboard') }}" class="clerk-sidebar-link d-flex align-items-center mb-3 @if(request()->routeIs('clerk.dashboard')) active @endif">Dashboard</a>
            <a href="{{ route('clerk.profile.edit') }}" class="clerk-sidebar-link d-flex align-items-center mb-3 @if(request()->routeIs('clerk.profile.edit')) active @endif">Edit profile</a>
            <a href="{{ route('clerk.notifications.index') }}" class="clerk-sidebar-link d-flex align-items-center mb-3 @if(request()->routeIs('clerk.notifications.index')) active @endif">Notification</a>
        </div>
    </div>
    <div class="flex-grow-1 p-4">
        <!-- Existing notification content -->
        @yield('clerk_notification_content')
        <form action="{{ route('customer.notifications.deleteAll') }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete All</button>
        </form>
    </div>
</div>
@push('styles')
<style>
.clerk-sidebar-link { color: #222; font-weight: 500; font-size: 1.08rem; text-decoration: none; transition: color 0.18s; border-radius: 6px; padding: 8px 12px; }
.clerk-sidebar-link.active, .clerk-sidebar-link:hover { background: #e6f2e6; color: #4CAF50 !important;}
</style>
@endpush
@endsection
