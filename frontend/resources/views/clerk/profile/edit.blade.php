@extends('layouts.clerk_app')
@section('content')
<style>
    /* Font and Icon Hierarchy - matching invoice page */
    .profile-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #385E42;
        margin-bottom: 0.75rem;
    }
    
    .section-header {
        font-size: 0.95rem;
        font-weight: 600;
        color: #385E42;
    }
    
    .profile-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #333;
        min-width: 140px;
        display: inline-block;
    }
    
    .profile-value {
        font-size: 0.85rem;
        font-weight: 400;
        color: #222;
    }
    
    .profile-section {
        margin-bottom: 0.75rem;
    }
    
    .edit-details-btn {
        font-size: 0.85rem;
        font-weight: 600;
        padding: 0.5rem 1.5rem;
    }
    
    .form-label {
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .form-control {
        font-size: 0.85rem;
    }
    
    .btn {
        font-size: 0.85rem;
    }
    
    .btn i, .icon-sm {
        font-size: 0.85rem;
    }
    
    .modal-title {
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    .profile-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        padding: 1.5rem;
        max-width: 600px;
        margin: 1.5rem auto 0 auto;
        position: relative;
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
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.25rem 0.75rem;
        margin-left: 10px;
        margin-top: 0.5rem;
        transition: background 0.2s;
    }
    
    .edit-image-btn:hover {
        background: #cfe3d8;
    }
    
    .edit-details-btn {
        background: #4CAF50;
        color: #fff;
        border: none;
        border-radius: 4px;
        transition: background 0.2s;
        display: block;
        margin: 1rem auto 0 auto;
    }
    
    .edit-details-btn:hover {
        background: #45a049;
    }
    
    hr {
        margin: 1rem 0;
        border-color: #e6f4ea;
    }
</style>

<div class="container" style="max-width: 800px; margin-top: -1.5rem; padding-top: 1rem;">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size: 0.85rem;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size: 0.85rem;">
            <ul class="mb-0" style="font-size: 0.85rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="profile-card d-flex align-items-start">
        <div class="me-4 d-flex flex-column align-items-center" style="min-width: 90px;">
            @php
                $pp = $user->profile_picture;
                $profileSrc = $pp ? (filter_var($pp, FILTER_VALIDATE_URL) ? $pp : asset('storage/' . $pp)) : 'https://via.placeholder.com/80';
            @endphp
            <img id="profileImagePreview" src="{{ $profileSrc }}" class="profile-image mb-2" alt="Profile Picture" onerror="this.onerror=null;this.src='https://via.placeholder.com/80';">
            <button type="button" class="edit-image-btn" data-bs-toggle="modal" data-bs-target="#editImageModal">
                <i class="bi bi-camera icon-sm me-1"></i>Edit Image
            </button>
        </div>
        <div class="flex-grow-1">
            <div class="profile-title">
                <i class="bi bi-person-circle me-2"></i>My Personal Info
            </div>
            <hr>
            <div class="profile-section">
                <span class="profile-label"><i class="bi bi-person me-1"></i>Name:</span> 
                <span class="profile-value">{{ $user->name ?? 'N/A' }}</span>
            </div>
            <div class="profile-section">
                <span class="profile-label"><i class="bi bi-envelope me-1"></i>Email:</span> 
                <span class="profile-value">{{ $user->email ?? 'N/A' }}</span>
            </div>
            <div class="profile-section">
                <span class="profile-label"><i class="bi bi-telephone me-1"></i>Contact Number:</span> 
                <span class="profile-value">{{ $user->contact_number ?? 'N/A' }}</span>
            </div>
            <div class="profile-section">
                <span class="profile-label"><i class="bi bi-shield-check me-1"></i>Role:</span> 
                <span class="profile-value">{{ ucfirst($user->role) ?? 'N/A' }}</span>
            </div>
            <button class="edit-details-btn" data-bs-toggle="modal" data-bs-target="#editDetailsModal">
                <i class="bi bi-pencil-square me-1"></i>EDIT DETAILS
            </button>
        </div>
    </div>

    <hr class="my-4">

    <!-- Change Password Section -->
    <div class="profile-card">
        <div class="profile-title">
            <i class="bi bi-key me-2"></i>Change Password
        </div>
        <hr>
        <form action="{{ route('clerk.profile.password') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="current_password" class="form-label">
                    <i class="bi bi-lock me-1"></i>Current Password
                </label>
                <input type="password" name="current_password" id="current_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">
                    <i class="bi bi-key me-1"></i>New Password
                </label>
                <input type="password" name="new_password" id="new_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="new_password_confirmation" class="form-label">
                    <i class="bi bi-key-fill me-1"></i>Confirm New Password
                </label>
                <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-lg me-1"></i>Change Password
            </button>
        </form>
    </div>
</div>

<!-- Edit Details Modal -->
<div class="modal fade" id="editDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: #e6f4ea; border-bottom: 1px solid #bbf7d0;">
                <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600; color: #385E42;">
                    <i class="bi bi-pencil-square me-2"></i>Edit Personal Info
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('clerk.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="bi bi-person me-1"></i>Name *
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact_number" class="form-label">
                            <i class="bi bi-telephone me-1"></i>Contact Number
                        </label>
                        <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number', $user->contact_number) }}" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Image Modal -->
<div class="modal fade" id="editImageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: #e6f4ea; border-bottom: 1px solid #bbf7d0;">
                <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600; color: #385E42;">
                    <i class="bi bi-camera me-2"></i>Update Profile Picture
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('clerk.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body d-flex flex-column align-items-center justify-content-center" style="min-height: 220px;">
                    <input type="file" name="profile_picture" accept="image/*" id="profilePicInputModal" style="display:none;">
                    <label for="profilePicInputModal" id="uploadImageLabel" style="cursor:pointer;">
                        <div class="d-flex flex-column align-items-center justify-content-center" style="background: #eafbe6; border-radius: 8px; padding: 32px 48px; border: 2px dashed #4CAF50;">
                            <i class="bi bi-cloud-upload" style="font-size: 2rem; color: #4CAF50;"></i>
                            <span style="font-size: 1rem; color: #4CAF50; font-weight: 600; margin-top: 0.5rem;">Upload image</span>
                        </div>
                    </label>
                    <img id="imagePreviewModal" src="" style="display:none; margin-top: 18px; max-width: 120px; border-radius: 50%;" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success" id="savePicBtnModal" style="display:none;">
                        <i class="bi bi-check-lg me-1"></i>Save Picture
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.clerk-sidebar-link { 
    color: #222; 
    font-weight: 500; 
    font-size: 1.08rem; 
    text-decoration: none; 
    transition: color 0.18s; 
    border-radius: 6px; 
    padding: 8px 12px; 
}
.clerk-sidebar-link.active, .clerk-sidebar-link:hover { 
    background: #e6f2e6; 
    color: #385E42 !important; 
}
</style>
@endpush

@push('scripts')
<script>
    // Modal image preview and save button logic
    document.getElementById('profilePicInputModal')?.addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            const preview = document.getElementById('imagePreviewModal');
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
            document.getElementById('savePicBtnModal').style.display = 'inline-block';
        }
    });
</script>
@endpush
@endsection
