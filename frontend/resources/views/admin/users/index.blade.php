@extends('layouts.admin_app')

@section('admin_content')
<div class="container-fluid pt-3">
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
                            
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-sm action-btn edit-btn" data-bs-toggle="modal" data-bs-target="#updateUserModal" data-user='{{ json_encode($user) }}' title="Edit User">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm action-btn delete-btn" title="Delete User">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
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
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
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
                        <select class="form-select" id="role" name="role" required onchange="toggleDriverFields()">
                            <option value="">Select Role</option>
                            <option value="clerk">Clerk</option>
                            <option value="driver">Driver</option>
                        </select>
                    </div>
                    
                    <!-- Driver-specific fields -->
                    <div id="driverFields" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Driver Information Required:</strong> Please provide vehicle and license details for driver accounts.
                        </div>
                        
                        <div class="mb-3">
                            <label for="license_number" class="form-label">License Number</label>
                            <input type="text" class="form-control" id="license_number" name="license_number" 
                                   placeholder="e.g., DL-123456789" maxlength="15" 
                                   pattern="[A-Z0-9\-]+" title="License number with dashes (max 15 characters)">
                            <small class="form-text text-muted">Format: Letters, numbers, and dashes (max 15 characters)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="vehicle_type" class="form-label">Vehicle Type</label>
                            <select class="form-select" id="vehicle_type" name="vehicle_type" onchange="updatePlateValidation()">
                                <option value="">Select Vehicle Type</option>
                                <option value="Motorcycle">Motorcycle</option>
                                <option value="Car">Car</option>
                                <option value="Van">Van</option>
                                <option value="Truck">Truck</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="vehicle_plate" class="form-label">Vehicle Plate Number</label>
                            <input type="text" class="form-control" id="vehicle_plate" name="vehicle_plate" 
                                   placeholder="e.g., ABC 1234" maxlength="8"
                                   pattern="[A-Z]{2,3} [0-9]{4,5}" title="Format: 2-3 letters, space, 4-5 digits">
                            <small class="form-text text-muted" id="plate_help">
                                Select vehicle type to see format requirements
                            </small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="work_start_time" class="form-label">Work Start Time</label>
                                    <input type="time" class="form-control" id="work_start_time" name="work_start_time" value="08:00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="work_end_time" class="form-label">Work End Time</label>
                                    <input type="time" class="form-control" id="work_end_time" name="work_end_time" value="17:00">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="max_deliveries_per_day" class="form-label">Max Deliveries Per Day</label>
                            <input type="number" class="form-control" id="max_deliveries_per_day" name="max_deliveries_per_day" value="15" min="1" max="50">
                        </div>
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
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
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
    // Toggle driver fields when role is selected
    function toggleDriverFields() {
        const roleSelect = document.getElementById('role');
        const driverFields = document.getElementById('driverFields');
        
        if (roleSelect.value === 'driver') {
            driverFields.style.display = 'block';
            // Make driver fields required
            const driverInputs = driverFields.querySelectorAll('input, select');
            driverInputs.forEach(input => {
                input.required = true;
            });
        } else {
            driverFields.style.display = 'none';
            // Remove required from driver fields
            const driverInputs = driverFields.querySelectorAll('input, select');
            driverInputs.forEach(input => {
                input.required = false;
                input.value = '';
            });
        }
    }

    // Update plate number validation based on vehicle type
    function updatePlateValidation() {
        const vehicleType = document.getElementById('vehicle_type').value;
        const plateInput = document.getElementById('vehicle_plate');
        const plateHelp = document.getElementById('plate_help');
        
        if (!vehicleType) {
            plateInput.maxLength = 8;
            plateInput.pattern = '';
            plateInput.placeholder = 'e.g., ABC 1234';
            plateHelp.textContent = 'Select vehicle type to see format requirements';
            plateHelp.className = 'form-text text-muted';
            return;
        }

        if (vehicleType === 'Car' || vehicleType === 'Van' || vehicleType === 'Truck') {
            // Car (new): 3 letters + space + 4 digits = 8 characters
            plateInput.maxLength = 8;
            plateInput.pattern = '[A-Z]{3} [0-9]{4}';
            plateInput.placeholder = 'e.g., ABC 1234';
            plateHelp.textContent = 'Format: 3 letters, space, 4 digits (e.g., ABC 1234)';
            plateHelp.className = 'form-text text-muted';
        } else if (vehicleType === 'Motorcycle') {
            // Motorcycle: Can be old (2 letters + 4 digits) or new (2 letters + 5 digits)
            plateInput.maxLength = 8;
            plateInput.pattern = '[A-Z]{2} [0-9]{4,5}';
            plateInput.placeholder = 'e.g., AB 1234 or AB 12345';
            plateHelp.textContent = 'Format: 2 letters, space, 4-5 digits (e.g., AB 1234 or AB 12345)';
            plateHelp.className = 'form-text text-muted';
        }

        // Real-time validation feedback
        plateInput.addEventListener('input', function() {
            validatePlateNumber();
        });
    }

    // Validate plate number format
    function validatePlateNumber() {
        const plateInput = document.getElementById('vehicle_plate');
        const vehicleType = document.getElementById('vehicle_type').value;
        const plateHelp = document.getElementById('plate_help');
        const value = plateInput.value.trim().toUpperCase();
        
        // Auto-format: convert to uppercase
        if (value !== plateInput.value) {
            plateInput.value = value;
        }

        if (!value) {
            plateHelp.className = 'form-text text-muted';
            plateInput.classList.remove('is-invalid', 'is-valid');
            return;
        }

        let isValid = false;
        if (vehicleType === 'Car' || vehicleType === 'Van' || vehicleType === 'Truck') {
            // Car: 3 letters + space + 4 digits
            isValid = /^[A-Z]{3} [0-9]{4}$/.test(value);
        } else if (vehicleType === 'Motorcycle') {
            // Motorcycle: 2 letters + space + 4 or 5 digits
            isValid = /^[A-Z]{2} [0-9]{4,5}$/.test(value);
        }

        if (isValid) {
            plateInput.classList.remove('is-invalid');
            plateInput.classList.add('is-valid');
            plateHelp.className = 'form-text text-success';
            plateHelp.textContent = '✓ Valid format';
        } else {
            plateInput.classList.remove('is-valid');
            plateInput.classList.add('is-invalid');
            plateHelp.className = 'form-text text-danger';
            if (vehicleType === 'Car' || vehicleType === 'Van' || vehicleType === 'Truck') {
                plateHelp.textContent = '✗ Invalid format. Use: 3 letters, space, 4 digits (e.g., ABC 1234)';
            } else if (vehicleType === 'Motorcycle') {
                plateHelp.textContent = '✗ Invalid format. Use: 2 letters, space, 4-5 digits (e.g., AB 1234 or AB 12345)';
            }
        }
    }

    // Validate license number format
    document.addEventListener('DOMContentLoaded', function() {
        const licenseInput = document.getElementById('license_number');
        if (licenseInput) {
            licenseInput.addEventListener('input', function() {
                const value = this.value.trim().toUpperCase();
                // Auto-format: convert to uppercase
                if (value !== this.value) {
                    this.value = value;
                }
                
                // Validate: letters, numbers, and dashes only, max 15 chars
                if (value.length > 15) {
                    this.value = value.substring(0, 15);
                }
            });
        }

        // Initialize plate validation on page load if vehicle type is selected
        const vehicleType = document.getElementById('vehicle_type');
        if (vehicleType && vehicleType.value) {
            updatePlateValidation();
        }
    });

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
    /* Register New Account Modal scrollbar styling */
    #addUserModal .modal-body::-webkit-scrollbar {
        width: 6px;
    }
    #addUserModal .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    #addUserModal .modal-body::-webkit-scrollbar-thumb {
        background: #7bb47b;
        border-radius: 3px;
    }
    #addUserModal .modal-body::-webkit-scrollbar-thumb:hover {
        background: #5aa65a;
    }

    /* Update Account Modal scrollbar styling */
    #updateUserModal .modal-body::-webkit-scrollbar {
        width: 6px;
    }
    #updateUserModal .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    #updateUserModal .modal-body::-webkit-scrollbar-thumb {
        background: #7bb47b;
        border-radius: 3px;
    }
    #updateUserModal .modal-body::-webkit-scrollbar-thumb:hover {
        background: #5aa65a;
    }

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

    /* Action Buttons Styling */
    .action-btn {
        width: 50px;
        height: 40px;
        border: none;
        background: transparent;
        color: #4CAF50;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        padding: 0;
        font-size: 16px;
        flex: 1;
        min-width: 50px;
        max-width: 50px;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .action-btn i {
        transition: color 0.3s ease;
    }

    /* Edit Button */
    .edit-btn:hover {
        background-color: #007bff;
        color: white;
    }

    .edit-btn:hover i {
        color: white;
    }

    /* Delete Button */
    .delete-btn:hover {
        background-color: #dc3545;
        color: white;
    }

    .delete-btn:hover i {
        color: white;
    }

    /* Ensure buttons are evenly spaced and fill the column */
    .d-flex.justify-content-center.gap-2 {
        width: 100%;
        max-width: 120px;
        margin: 0 auto;
        gap: 8px !important;
    }

    /* Make sure both buttons have exactly the same width */
    .edit-btn, .delete-btn {
        width: 50px !important;
        flex: 1 1 50px;
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