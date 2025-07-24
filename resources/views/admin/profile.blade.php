@extends('layouts.admin_app')

@section('admin_content')
<style>
    .profile-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        padding: 32px 32px 24px 32px;
        max-width: 600px;
        margin: 40px auto 0 auto;
        position: relative;
    }
    .profile-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 8px;
        color: #444;
    }
    .profile-section {
        margin-bottom: 18px;
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
    .edit-details-btn {
        background: #4CAF50;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 8px 28px;
        font-weight: 600;
        transition: background 0.2s;
        display: block;
        margin: 0 auto;
    }
    .edit-details-btn:hover {
        background: #45a049;
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
</style>

<div class="container" style="max-width: 800px; margin-top: 40px;">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="profile-card d-flex align-items-start">
        <div class="me-4 d-flex flex-column align-items-center" style="min-width: 90px;">
            <img id="profileImagePreview" src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://via.placeholder.com/80' }}" class="profile-image mb-2" alt="Profile Picture">
            <button type="button" class="edit-image-btn" data-bs-toggle="modal" data-bs-target="#editImageModal">Edit Image</button>
        </div>
        <div class="flex-grow-1">
            <div class="profile-title mb-2">My Personal Info</div>
            <hr>
            <div class="profile-section">
                <span class="profile-label">Name:</span> 
                <span class="profile-value">{{ $user->name ?? 'N/A' }}</span>
            </div>
            <div class="profile-section">
                <span class="profile-label">Email:</span> 
                <span class="profile-value">{{ $user->email ?? 'N/A' }}</span>
            </div>
            <div class="profile-section">
                <span class="profile-label">Contact Number:</span> 
                <span class="profile-value">{{ $user->contact_number ?? 'N/A' }}</span>
            </div>
            <div class="profile-section">
                <span class="profile-label">Role:</span> 
                <span class="profile-value">{{ ucfirst($user->role) ?? 'N/A' }}</span>
            </div>
            <button class="edit-details-btn" data-bs-toggle="modal" data-bs-target="#editDetailsModal">EDIT DETAILS</button>
        </div>
    </div>

    <hr class="my-4">

    <!-- Change Password Section -->
    <div class="profile-card">
        <div class="profile-title mb-3">Change Password</div>
        <form action="{{ route('admin.profile.password') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="current_password" class="form-label">Current Password</label>
                <input type="password" name="current_password" id="current_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-warning">Change Password</button>
        </form>
    </div>
</div>

<!-- Edit Details Modal -->
<div class="modal fade" id="editDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Personal Info</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.profile.update') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number', $user->contact_number) }}" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Image Modal -->
<div class="modal fade" id="editImageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body d-flex flex-column align-items-center justify-content-center" style="min-height: 220px;">
                    <input type="file" name="profile_picture" accept="image/*" id="profilePicInputModal" style="display:none;">
                    <label for="profilePicInputModal" id="uploadImageLabel" style="cursor:pointer;">
                        <div class="d-flex flex-column align-items-center justify-content-center" style="background: #eafbe6; border-radius: 8px; padding: 32px 48px; border: 2px dashed #4CAF50;">
                            <span style="font-size: 2rem; color: #4CAF50;">+</span>
                            <span style="font-size: 1.2rem; color: #4CAF50; font-weight: 600;">Upload image</span>
                        </div>
                    </label>
                    <img id="imagePreviewModal" src="" style="display:none; margin-top: 18px; max-width: 120px; border-radius: 50%;" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="savePicBtnModal" style="display:none;">Save Picture</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
@endsection 