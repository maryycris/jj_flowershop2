@extends('layouts.customer_app')

@section('content')
@include('components.customer.alt_nav', ['active' => 'profile'])
<style>
    .profile-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        padding: 36px 40px 28px 40px;
        max-width: 980px;
        margin: 20px auto 0 auto;
        position: relative;
        border: 1px solid #eef3ef;
    }
    .profile-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 4px;
        color: #2c3e50;
        background: #eafbe7;
        padding: 8px 12px;
        border-radius: 8px;
    }
    .profile-section {
        margin-bottom: 18px;
    }
    .profile-label {
        font-weight: 600;
        color: #546e5b;
        min-width: 160px;
        display: inline-block;
    }
    .profile-value {
        color: #1f2d27;
        font-style: normal;
    }
    .edit-details-btn {
        background: #7bb47b;
        color: #fff;
        border: none;
        border-radius: 24px;
        padding: 8px 24px;
        font-weight: 600;
        font-size: 0.9rem;
        letter-spacing: .1px;
        transition: transform .15s ease, box-shadow .15s ease;
        display: block;
        margin: 6px auto 0 auto;
        box-shadow: 0 6px 14px rgba(122, 179, 122, .25);
    }
    .edit-details-btn:hover {
        background: #5a9c5a;
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(122, 179, 122, .32);
    }
    .profile-image {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        background: #e0e0e0;
        display: inline-block;
    }
    .edit-image-btn {
        background: #e0e0e0;
        color: #444;
        border: none;
        border-radius: 4px;
        font-size: 0.85rem;
        padding: 2px 10px;
        margin-left: 10px;
        margin-top: 8px;
    }
    .edit-image-btn:hover {
        background: #cfe3d8;
    }
    .profile-image-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
    }
    /* Divider styling */
    .profile-divider { border: 0; height: 1px; background: linear-gradient(to right, transparent, #e7efe7, transparent); margin: 10px 0 20px; }
    
    /* Custom Success Alert Styling */
    .custom-success-alert {
        background: #e8f5e8;
        border: 1px solid #7bb47b;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        box-shadow: 0 2px 8px rgba(123, 180, 123, 0.15);
    }
    
    .custom-success-alert .alert-icon {
        background: #7bb47b;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        flex-shrink: 0;
    }
    
    .custom-success-alert .alert-message {
        color: #2d5a2d;
        font-weight: 500;
        flex: 1;
        font-size: 14px;
    }
    
    .custom-success-alert .alert-close {
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: background-color 0.2s;
        flex-shrink: 0;
    }
    
    .custom-success-alert .alert-close:hover {
        background: rgba(0, 0, 0, 0.1);
        color: #333;
    }
    @media (max-width: 767.98px) {
        .main-content-with-sidebar { margin-left: 0 !important; max-width: 100% !important; }
        .container-fluid { padding: 0.5rem 0.25rem !important; }
        /* Make the inner column a bit wider and reduce outer gutters */
        .row.justify-content-center > .col-12,
        .row.justify-content-center > .col-md-9,
        .row.justify-content-center > .col-lg-8 { width: 96% !important; }
        .container-fluid { padding-top: 4px !important; }
        .profile-card { padding: 16px 14px; margin: 10px auto 0 auto; border-radius: 12px; }
        .profile-title { font-size: 1rem; padding: 6px 10px; }
        .profile-image { width: 72px; height: 72px; }
        .profile-section { margin-bottom: 12px; }
        .profile-label { min-width: auto; display: block; font-size: 0.92rem; margin-bottom: 2px; }
        .profile-value { font-size: 0.98rem; }
        .edit-details-btn { width: 100%; border-radius: 8px; padding: 10px 14px; }
        .profile-divider { margin: 8px 0 16px; }
        .row > .col-md-6 { margin-bottom: 12px; }
        .btn-green { width: 100% !important; }
        /* Make profile image unclickable on mobile */
        .profile-image-mobile {
            cursor: default !important;
            pointer-events: none !important;
        }
        .profile-image-mobile + .small {
            display: none !important;
        }
        /* Responsive modal sizing/positioning */
        #editDetailsModal .modal-dialog,
        #editImageModal .modal-dialog {
            width: 95vw !important;
            max-width: 95vw !important;
            margin: 10vh auto !important;
        }
        #editDetailsModal .modal-body { max-height: 60vh; overflow-y: auto; }
        #editImageModal .modal-body { min-height: 220px; }
        /* Ensure modals overlay the sticky mobile navbar */
        .modal { z-index: 6000 !important; }
        .modal-backdrop { z-index: 5990 !important; }
    }
</style>
<div class="container-fluid" style="padding-top: 8px;">
    <div class="row justify-content-center">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-3 d-none d-md-block">
            @include('customer.sidebar')
        </div>
        <!-- Main Content -->
        <div class="col-12 col-md-9 col-lg-8 main-content-with-sidebar" style="margin-left: 25%; max-width: calc(75% - 30px);">
            <div class="py-3 px-3 d-flex flex-column align-items-center justify-content-start">
                @if(session('reminder'))
                    <div class="alert alert-warning" style="max-width:700px;margin:20px auto 0 auto;">
                        {{ session('reminder') }}
                    </div>
                @endif
                <div class="row" style="width: 100%; margin: 0 auto;">
                    <!-- Left Column - My Personal Info -->
                    <div class="col-md-6">
                        <div class="profile-card">
                            <div class="profile-title mb-2 text-center">My Personal Info</div>
                            <div class="mb-3 text-center">
                                <img src="{{
                                    $user->profile_picture
                                        ? (\Illuminate\Support\Str::startsWith($user->profile_picture, 'http')
                                            ? $user->profile_picture
                                            : asset('storage/' . $user->profile_picture))
                                        : 'https://via.placeholder.com/100'
                                }}" alt="Profile" class="rounded-circle profile-image-mobile d-md-none" style="width: 100px; height: 100px; object-fit: cover; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#editImageModal">
                                <img src="{{
                                    $user->profile_picture
                                        ? (\Illuminate\Support\Str::startsWith($user->profile_picture, 'http')
                                            ? $user->profile_picture
                                            : asset('storage/' . $user->profile_picture))
                                        : 'https://via.placeholder.com/100'
                                }}" alt="Profile" class="rounded-circle d-none d-md-inline-block" style="width: 100px; height: 100px; object-fit: cover; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#editImageModal">
                                <div class="small text-muted d-none d-md-block">Click image to change</div>
                            </div>
                            <hr class="profile-divider">
                            <div class="profile-section" style="margin-bottom: 16px;"><span class="profile-label">First Name:</span> <span class="profile-value">{{ Auth::user()->first_name ?? 'N/A' }}</span></div>
                            <div class="profile-section" style="margin-bottom: 16px;"><span class="profile-label">Last Name:</span> <span class="profile-value">{{ Auth::user()->last_name ?? 'N/A' }}</span></div>
                            <div class="profile-section" style="margin-bottom: 16px;"><span class="profile-label">E-mail:</span> <span class="profile-value">{{ Auth::user()->email ?? 'N/A' }}</span></div>
                            <div class="profile-section" style="margin-bottom: 16px;"><span class="profile-label">Cellphone Number:</span> <span class="profile-value">{{ Auth::user()->contact_number ?? 'N/A' }}</span></div>
                            <div class="profile-section" style="margin-bottom: 24px;"><span class="profile-label">Address:</span> <span class="profile-value">
                                @php
                                    $user = Auth::user();
                                    $defaultAddress = $user->addresses()->where('is_default', true)->first() ?? $user->addresses()->first();
                                    if ($defaultAddress) {
                                        $parts = [];
                                        if (!empty($defaultAddress->street_address)) $parts[] = $defaultAddress->street_address;
                                        if (!empty($defaultAddress->barangay)) $parts[] = $defaultAddress->barangay;
                                        if (!empty($defaultAddress->municipality)) $parts[] = $defaultAddress->municipality;
                                        if (!empty($defaultAddress->city)) $parts[] = $defaultAddress->city;
                                        echo implode(', ', $parts);
                                    } else {
                                        $parts = [];
                                        if (!empty($user->street_address)) $parts[] = $user->street_address;
                                        if (!empty($user->barangay)) $parts[] = $user->barangay;
                                        if (!empty($user->municipality)) $parts[] = $user->municipality;
                                        if (!empty($user->city)) $parts[] = $user->city;
                                        echo $parts ? implode(', ', $parts) : (!empty($user->address) ? $user->address : 'N/A');
                                    }
                                @endphp
                            </span></div>
                            <button class="edit-details-btn" data-bs-toggle="modal" data-bs-target="#editDetailsModal" style="background: #7bb47b; color: #fff; border: none; border-radius: 4px; padding: 8px 24px; font-weight: 600; font-size: 0.9rem; display: block; margin: 0 auto;">EDIT DETAILS</button>
                        </div>
                    </div>
                    
                    <!-- Right Column - Change Password -->
                    <div class="col-md-6">
                        <div class="profile-card">
                            <div class="profile-title mb-2">Change Password</div>
                            <hr class="profile-divider">
                            <form action="{{ route('customer.account.update_password') }}" method="POST">
                                @csrf
                                @if($errors->any())
                                <div class="alert alert-danger py-2 px-3">
                                    {{ $errors->first() }}
                                </div>
                                @endif
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password *</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password *</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">Confirm New Password *</label>
                                    <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                                </div>
                                <button type="submit" class="edit-details-btn" style="background: #7bb47b; color: #fff; border: none; border-radius: 4px; padding: 8px 24px; font-weight: 600; font-size: 0.9rem; display: block; margin: 0 auto;">CHANGE PASSWORD</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Details Modal -->
<div class="modal fade" id="editDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #ffffff;">
            <div class="modal-header">
                <h5 class="modal-title">Edit Personal Info</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('customer.account.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
            <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                    <!-- Profile Picture Upload Section - Mobile Only -->
                    <div class="d-md-none mb-4 text-center">
                        <label class="form-label d-block mb-2">Profile Picture</label>
                        <input type="file" name="profile_picture" accept="image/*" id="profilePicInputEditModal" style="display:none;">
                        <label for="profilePicInputEditModal" id="uploadImageLabelEditModal" style="cursor:pointer;">
                            <div class="d-flex flex-column align-items-center justify-content-center" style="background: #eafbe6; border-radius: 8px; padding: 20px 32px; border: 2px dashed #4CAF50;">
                                <i class="bi bi-cloud-upload" style="font-size: 1.5rem; color: #4CAF50;"></i>
                                <span style="font-size: 0.9rem; color: #4CAF50; font-weight: 600; margin-top: 0.5rem;">Upload image</span>
                            </div>
                        </label>
                        <img id="imagePreviewEditModal" src="{{ 
                            $user->profile_picture
                                ? (\Illuminate\Support\Str::startsWith($user->profile_picture, 'http')
                                    ? $user->profile_picture
                                    : asset('storage/' . $user->profile_picture))
                                : 'https://via.placeholder.com/100'
                        }}" style="margin-top: 12px; max-width: 100px; border-radius: 50%;" />
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">First Name *</label>
                            <input type="text" class="form-control" name="first_name" value="{{ Auth::user()->first_name ?? '' }}" required style="text-transform: capitalize;">
                        </div>
                        <div class="col">
                            <label class="form-label">Last Name *</label>
                            <input type="text" class="form-control" name="last_name" value="{{ Auth::user()->last_name ?? '' }}" required style="text-transform: capitalize;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" name="email" value="{{ Auth::user()->email ?? '' }}" readonly required>
                    </div>
                {{-- Address fields removed; address is managed via Address Book --}}
                <div class="mb-3">
                        <label class="form-label">Cellphone Number *</label>
                        <input type="text" class="form-control" name="contact_number" value="{{ Auth::user()->contact_number ?? '' }}" required placeholder="Enter your cellphone number">
                </div>
            </div>
            <div class="modal-footer">
                    <button type="submit" class="btn btn-green" style="width: 100px;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Image Modal -->
<div class="modal fade" id="editImageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #f8faf8; border-radius: 12px;">
            <div class="modal-header" style="border-bottom: none;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('customer.account.update_picture') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body d-flex flex-column align-items-center justify-content-center" style="min-height: 220px;">
                    <input type="file" name="profile_picture" accept="image/*" id="profilePicInputModal" style="display:none;">
                    <label for="profilePicInputModal" id="uploadImageLabel" style="cursor:pointer;">
                        <div class="d-flex flex-column align-items-center justify-content-center" style="background: #eafbe6; border-radius: 8px; padding: 32px 48px; border: 2px dashed #7bb47b;">
                            <span style="font-size: 2rem; color: #7bb47b;">+</span>
                            <span style="font-size: 1.2rem; color: #7bb47b; font-weight: 600;">Upload image</span>
                        </div>
                    </label>
                    <img id="imagePreviewModal" src="" style="display:none; margin-top: 18px; max-width: 120px; border-radius: 50%;" />
                </div>
                <div class="modal-footer" style="border-top: none; justify-content: center;">
                    <button type="submit" class="btn btn-green" id="savePicBtnModal" style="width: 120px; display:none;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    // Modal image preview and save button logic for separate image modal (desktop)
    document.getElementById('profilePicInputModal')?.addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            const preview = document.getElementById('imagePreviewModal');
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
            document.getElementById('savePicBtnModal').style.display = 'inline-block';
        }
    });

    // Image preview logic for Edit Details Modal (mobile)
    document.getElementById('profilePicInputEditModal')?.addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            const preview = document.getElementById('imagePreviewEditModal');
            if (preview) {
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
            }
        }
    });
</script>
@endsection

@push('styles')
<style>
    body {
        background: #f4faf4 !important;
    }
    .sidebar-links .sidebar-link {
        display: block;
        padding: 8px 20px;
        border-radius: 4px;
        color: #222;
        font-weight: 400;
        text-decoration: none;
        margin-bottom: 2px;
        background: transparent;
        transition: background 0.2s, color 0.2s;
        font-size: 1.08rem;
    }
    .sidebar-links .sidebar-link.active-link {
        background: #cbe7cb;
        color: #222;
        font-weight: 600;
    }
    .sidebar-links .sidebar-link:hover {
        background: #e0f2e0;
        color: #222;
    }
    .sidebar-label {
        margin-bottom: 6px;
        letter-spacing: 0.5px;
    }
    .profile-label {
        font-weight: 500;
        color: #333;
        min-width: 140px;
        display: inline-block;
    }
    .profile-value {
        color: #222;
        font-style: normal;
    }
    .profile-card {
        box-shadow: none !important;
    }
    
    /* Edit Details Modal scrollbar styling */
    #editDetailsModal .modal-body::-webkit-scrollbar {
        width: 6px;
    }
    #editDetailsModal .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    #editDetailsModal .modal-body::-webkit-scrollbar-thumb {
        background: #7bb47b;
        border-radius: 3px;
    }
    #editDetailsModal .modal-body::-webkit-scrollbar-thumb:hover {
        background: #5aa65a;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prevent leading spaces and ensure proper case
    function preventLeadingSpaces(input) {
        input.addEventListener('input', function(e) {
            if (e.target.value.startsWith(' ')) {
                e.target.value = e.target.value.trim();
            }
        });
        
        input.addEventListener('keydown', function(e) {
            if (e.key === ' ' && e.target.selectionStart === 0) {
                e.preventDefault();
            }
        });
    }
    
    // Apply to all text inputs and textareas
    document.querySelectorAll('input[type="text"], textarea').forEach(preventLeadingSpaces);
});
</script>
@endpush
