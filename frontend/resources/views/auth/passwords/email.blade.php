@extends('layouts.mobile_app')
@section('content')
<div class="mx-auto" style="max-width: 400px;">
    <h4 class="fw-bold mb-3"><i class="bi bi-envelope"></i> Forgot Password</h4>
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">E-Mail Address <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
            @error('email')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
        <div class="d-grid mb-2">
            <button type="submit" class="btn btn-success">Send Password Reset Link</button>
        </div>
        <div class="mb-2">
            <a href="{{ route('login') }}" class="small">Back to Login</a>
        </div>
    </form>
</div>
@endsection 