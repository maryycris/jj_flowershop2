@extends('layouts.mobile_app')
@section('content')
<div class="mx-auto" style="max-width: 400px;">
    <h4 class="fw-bold mb-3"><i class="bi bi-key"></i> Reset Password</h4>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">
        <div class="mb-3">
            <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
            <input type="password" class="form-control" id="password" name="password" required placeholder="New Password">
            @error('password')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Confirm New Password">
        </div>
        <div class="d-grid mb-2">
            <button type="submit" class="btn btn-success">Reset Password</button>
        </div>
        <div class="mb-2">
            <a href="{{ route('login') }}" class="small">Back to Login</a>
        </div>
    </form>
</div>
@endsection 