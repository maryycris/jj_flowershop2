@extends('layouts.driver_mobile')

@section('content')
<div class="text-center mb-4">
    <!-- Banner/UI Reference -->
    <img src="/images/rider_UI.png" alt="Driver UI Reference" style="max-width: 100%; border-radius: 15px; margin-bottom: 24px; box-shadow: 0 4px 16px #dbe7db;">
    <h4 class="fw-bold mt-3" style="color: #356e35; letter-spacing: 1px;">Welcome, {{ Auth::user()->name ?? 'Driver' }}!</h4>
    <p class="text-muted">Your delivery dashboard is ready.</p>
</div>
<div class="card shadow-lg mb-4" style="border: none; border-radius: 16px; background: #f7faf7;">
    <div class="card-body text-center">
        <div style="font-size: 2.3rem; color: #2a7e2a;"><i class="bi bi-truck"></i></div>
        <h5 class="card-title mt-2 mb-2" style="color: #3a5d37; font-weight: 600;">Today's Deliveries</h5>
        <p class="display-5 fw-bold mb-1" style="color: #216f21;">{{ isset($toDeliver) ? $toDeliver->count() : 0 }}</p>
        <div class="small text-muted mb-0">Deliveries assigned to you today</div>
    </div>
</div>
<ul class="list-group mb-4">
    <li class="list-group-item d-flex justify-content-between align-items-center" style="font-size: 1.08rem;">
        <span class="fw-semibold"><i class="bi bi-truck me-2"></i>Go to your Orders</span>
        <a href="{{ route('driver.orders.index') }}" class="btn btn-success btn-sm px-3"><i class="bi bi-chevron-right"></i></a>
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-person me-2"></i>Account/Profile</span>
        <a href="{{ route('driver.profile') }}" class="btn btn-outline-success btn-sm px-3"><i class="bi bi-chevron-right"></i></a>
    </li>
</ul>
@endsection
