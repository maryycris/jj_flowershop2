@extends('layouts.driver_mobile')

@section('content')
<div class="text-center mb-4">
    <div class="position-relative d-inline-block">
        @php
            $pp = Auth::user()->profile_picture;
            $profileSrc = $pp ? (filter_var($pp, FILTER_VALIDATE_URL) ? $pp : asset('storage/' . $pp)) : asset('images/default-avatar.png');
        @endphp
        <img src="{{ $profileSrc }}" 
             alt="Profile Picture" 
             class="rounded-circle" 
             style="width: 100px; height: 100px; object-fit: cover;"
             onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';">
        <button class="btn btn-sm btn-primary position-absolute bottom-0 end-0" onclick="document.getElementById('profilePicture').click()">
            <i class="bi bi-camera"></i>
        </button>
    </div>
    <h4 class="fw-bold mt-2">{{ Auth::user()->name }}</h4>
    <p class="text-muted">Driver</p>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-person me-2"></i>Personal Information</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('driver.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <input type="file" id="profilePicture" name="profile_picture" accept="image/*" style="display: none;" onchange="previewImage(this)">
            
            <div class="row mb-3">
                <div class="col-12">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ Auth::user()->name }}" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" value="{{ Auth::user()->email }}" readonly>
                    <small class="text-muted">Email cannot be changed</small>
                </div>
                <div class="col-6">
                    <label for="contact_number" class="form-label">Contact Number</label>
                    <input type="text" class="form-control" id="contact_number" name="contact_number" value="{{ Auth::user()->contact_number }}" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-6">
                    <label for="sex" class="form-label">Gender</label>
                    <select class="form-select" id="sex" name="sex" required>
                        <option value="M" {{ Auth::user()->sex === 'M' ? 'selected' : '' }}>Male</option>
                        <option value="F" {{ Auth::user()->sex === 'F' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="col-6">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" value="{{ Auth::user()->username }}" readonly>
                    <small class="text-muted">Username cannot be changed</small>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-check-circle me-1"></i>Update Profile
            </button>
        </form>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Change Password</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('driver.profile.password') }}">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="current_password" class="form-label">Current Password</label>
                <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>
            
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            
            <div class="mb-3">
                <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
            </div>
            
            <button type="submit" class="btn btn-warning w-100">
                <i class="bi bi-key me-1"></i>Change Password
            </button>
        </form>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = input.parentElement.querySelector('img');
            img.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection 