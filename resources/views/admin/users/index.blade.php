@extends('layouts.admin_app')

@section('admin_content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manage Accounts</h1>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">Add new</button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="usersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Sex</th>
                            <th>Role</th>
                            <th>Contact Number</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->sex }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>{{ $user->contact_number }}</td>
                            
                            <td>
                                <button class="btn btn-sm btn-primary edit-user-btn" data-bs-toggle="modal" data-bs-target="#updateUserModal" data-user='{{ json_encode($user) }}'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No users found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add New User Modal (Register) -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addUserModalLabel">Register New Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full name</label>
                        <input type="text" class="form-control" id="full_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="sex" class="form-label">Sex</label>
                        <select class="form-select" id="sex" name="sex" required>
                            <option value="">Select Sex</option>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        
                        <label for="contact_number" class="form-label">Contact number</label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="clerk">Clerk</option>
                            <option value="driver">Driver</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update User Modal -->
<div class="modal fade" id="updateUserModal" tabindex="-1" aria-labelledby="updateUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="updateUserModalLabel">Update Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="update_full_name" class="form-label">Full name</label>
                        <input type="text" class="form-control" id="update_full_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="update_username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="update_username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_sex" class="form-label">Sex</label>
                        <select class="form-select" id="update_sex" name="sex" required>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="update_contact_number" class="form-label">Contact number</label>
                        <input type="text" class="form-control" id="update_contact_number" name="contact_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_role" class="form-label">Role</label>
                        <select class="form-select" id="update_role" name="role" required>
                            <option value="clerk">Clerk</option>
                            <option value="driver">Driver</option>
                        </select>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var updateUserModal = document.getElementById('updateUserModal');
        updateUserModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var user = JSON.parse(button.getAttribute('data-user'));

            var form = updateUserModal.querySelector('#updateUserForm');
            form.action = '/admin/users/' + user.id; // Set the form action dynamically

            updateUserModal.querySelector('#update_full_name').value = user.name;
            updateUserModal.querySelector('#update_email').value = user.email;
            updateUserModal.querySelector('#update_username').value = user.username;
            updateUserModal.querySelector('#update_sex').value = user.sex;
            updateUserModal.querySelector('#update_contact_number').value = user.contact_number;
            updateUserModal.querySelector('#update_role').value = user.role;
            updateUserModal.querySelector('#update_store_name').value = user.store_name;
        });
    });
</script>
@endpush

@push('styles')
<style>
    .bg-light-green {
        background-color: #f0f2ed !important;
    }
    .bg-success, .btn-success {
        background-color: #385E42 !important; /* Darker green from prototype */
        border-color: #385E42 !important;
    }
    .btn-success:hover {
        background-color: #284430 !important;
        border-color: #284430 !important;
    }
    .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(200%);
    }
    .table thead th {
        background-color: #385E42; /* Dark green for table header */
        color: white;
    }
</style>
@endpush

@if(session('success'))
    <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <script>
        setTimeout(function() {
            var alert = document.getElementById('success-alert');
            if(alert) {
                var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }
        }, 2000);
    </script>
@endif

@endsection