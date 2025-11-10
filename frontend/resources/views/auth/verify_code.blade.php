@extends('layouts.mobile_app')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/loginstyle.css') }}">
@endsection
@section('content')
<div class="container-fluid vh-100 d-flex flex-column justify-content-center align-items-center bg-light">
    <div class="card shadow-lg p-2" style="max-width: 320px; width: 100%; margin-top: 12px; margin-bottom: 12px;">
        <div class="card-body p-2">
            <h2 class="text-center text-success mb-2" style="font-weight: 700;">Verification</h2>
            <p class="text-center text-muted mb-2">Enter the 6-digit code sent to your selected channel to complete registration.</p>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
            {{-- Verification code is now only sent via SMS. No demo code shown in UI. --}}
            @if(isset($expired) && $expired)
                <div class="alert alert-warning">Your verification code has expired. Please resend a new code.</div>
                <form method="POST" action="{{ route('verify.code.resend') }}">
                    @csrf
                    <div class="d-grid mb-2">
                        <button type="submit" class="btn btn-secondary btn-sm">Resend Code</button>
                    </div>
                </form>
            @else
                <form method="POST" action="{{ route('verify.code.submit') }}">
                    @csrf
                    <div class="mb-2">
                        <label for="verification_code" class="form-label visually-hidden">Verification Code</label>
                        <input type="text" name="verification_code" id="verification_code" class="form-control form-control-sm" placeholder="Enter 6-digit code" maxlength="6" required autofocus>
                        @error('verification_code')
                            <span class="text-danger" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="d-grid mb-2">
                        <button type="submit" class="btn btn-success btn-sm">Verify</button>
                    </div>
                </form>
                <form method="POST" action="{{ route('verify.code.resend') }}">
                    @csrf
                    <div class="d-grid mb-2">
                        <button type="submit" class="btn btn-link btn-sm">Resend Code</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection 