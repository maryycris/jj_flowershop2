@extends('layouts.customer_app')

@section('content')
<style>
    .change-password-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        padding: 32px 32px 24px 32px;
        max-width: 700px;
        margin: 24px auto 0 auto;
    }
    .change-password-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 8px;
        color: #444;
        text-align: center;
    }
    .change-password-subtitle {
        font-size: 1rem;
        color: #888;
        margin-bottom: 24px;
        text-align: center;
    }
    .form-label {
        font-weight: 500;
        color: #333;
    }
    .form-control {
        border-radius: 6px;
        border: 1px solid #cfe3d8;
        background: #f8faf8;
    }
    .btn-green {
        background: #7bb47b;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 8px 28px;
        font-weight: 600;
        transition: background 0.2s;
        width: 100px;
        margin: 0 auto;
        display: block;
    }
    .btn-green:hover {
        background: #5a9c5a;
    }
</style>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-3 col-lg-3">
            @include('customer.sidebar')
        </div>
        <div class="col-md-9 col-lg-7">
            <div class="py-4 px-3 d-flex flex-column align-items-center justify-content-start">
                <div class="change-password-card">
                    <div class="change-password-title">Reset Password</div>
                    <div class="change-password-subtitle">Build a stronger password</div>
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('customer.account.update_password') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Old Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                        </div>
                        <button type="submit" class="btn btn-green">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 