@extends('layouts.customer_app')

@section('content')
<style>
    .text-capitalize {
        text-transform: capitalize;
    }
</style>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-3 col-lg-3">
            @include('customer.sidebar')
        </div>
        <div class="col-md-9 col-lg-7">
            <div class="py-4 px-3">
                <!-- Location-Based Recommendations Box -->
                <div class="mb-4">
                    <div class="bg-white rounded-4 p-3 shadow-sm" id="locationCard" style="display: none;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-1 fw-bold text-success">
                                    <i class="fas fa-map-marker-alt me-2"></i>Location-Based Recommendations
                                </h6>
                                <small class="text-muted" id="locationText">Detecting your location...</small>
                            </div>
                            <button class="btn btn-sm btn-outline-success" onclick="updateLocation()">
                                <i class="fas fa-sync-alt me-1"></i>Update
                            </button>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">My Address Book</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                        <i class="fas fa-plus"></i> Add New Address
                    </button>
                </div>

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

                <div class="row">
                    @forelse($addresses as $address)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title mb-0">
                                            {{ $address->label ?? 'Address' }}
                                            @if($address->is_default)
                                                <span class="badge bg-primary ms-2">Default</span>
                                            @endif
                                        </h5>
                                        <div class="dropdown">
                                            <button class="btn btn-link text-dark p-0" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editAddressModal{{ $address->id }}">
                                                        <i class="fas fa-edit me-2"></i> Edit
                                                    </button>
                                                </li>
                                                @if(!$address->is_default)
                                                    <li>
                                                        <form action="{{ route('customer.address_book.set-default', $address) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fas fa-star me-2"></i> Set as Default
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                <li>
                                                    <form action="{{ route('customer.address_book.destroy', $address) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this address?')">
                                                            <i class="fas fa-trash me-2"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <p class="card-text mb-0">
                                        {{ $address->street_address }}<br>
                                        {{ $address->barangay }}<br>
                                        {{ $address->municipality }}<br>
                                        {{ $address->city }}<br>
                                        @if($address->province)
                                            {{ $address->province }}<br>
                                        @endif
                                        @if($address->zip_code)
                                            {{ $address->zip_code }}<br>
                                        @endif
                                        @if($address->landmark)
                                            <strong>Landmark:</strong> {{ $address->landmark }}<br>
                                        @endif
                                        @if($address->special_instructions)
                                            <strong>Instructions:</strong> {{ $address->special_instructions }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Address Modal -->
                        <div class="modal fade" id="editAddressModal{{ $address->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Address</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('customer.address_book.update', $address) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="label{{ $address->id }}" class="form-label">Label (Optional)</label>
                                                <input type="text" class="form-control text-capitalize" id="label{{ $address->id }}" name="label" value="{{ $address->label }}">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="first_name{{ $address->id }}" class="form-label">First Name</label>
                                                    <input type="text" class="form-control text-capitalize" id="first_name{{ $address->id }}" name="first_name" value="{{ $address->first_name }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="last_name{{ $address->id }}" class="form-label">Last Name</label>
                                                    <input type="text" class="form-control text-capitalize" id="last_name{{ $address->id }}" name="last_name" value="{{ $address->last_name }}" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="company{{ $address->id }}" class="form-label">Company (Optional)</label>
                                                <input type="text" class="form-control text-capitalize" id="company{{ $address->id }}" name="company" value="{{ $address->company }}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="street_address{{ $address->id }}" class="form-label">Street Address</label>
                                                <input type="text" class="form-control text-capitalize" id="street_address{{ $address->id }}" name="street_address" value="{{ $address->street_address }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="barangay{{ $address->id }}" class="form-label">Barangay</label>
                                                <input type="text" class="form-control text-capitalize" id="barangay{{ $address->id }}" name="barangay" value="{{ $address->barangay }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="municipality{{ $address->id }}" class="form-label">Municipality</label>
                                                <input type="text" class="form-control text-capitalize" id="municipality{{ $address->id }}" name="municipality" value="{{ $address->municipality }}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="city{{ $address->id }}" class="form-label">City</label>
                                                <input type="text" class="form-control text-capitalize" id="city{{ $address->id }}" name="city" value="{{ $address->city }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="province{{ $address->id }}" class="form-label">Province (Optional)</label>
                                                <input type="text" class="form-control text-capitalize" id="province{{ $address->id }}" name="province" value="{{ $address->province }}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="region{{ $address->id }}" class="form-label">Region</label>
                                                <input type="text" class="form-control text-capitalize" id="region{{ $address->id }}" name="region" value="{{ $address->region }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="zip_code{{ $address->id }}" class="form-label">ZIP Code</label>
                                                <input type="text" class="form-control text-capitalize" id="zip_code{{ $address->id }}" name="zip_code" value="{{ $address->zip_code }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="phone_number{{ $address->id }}" class="form-label">Phone Number</label>
                                                <input type="text" class="form-control text-capitalize" id="phone_number{{ $address->id }}" name="phone_number" value="{{ $address->phone_number }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="landmark{{ $address->id }}" class="form-label">Landmark (Optional)</label>
                                                <input type="text" class="form-control text-capitalize" id="landmark{{ $address->id }}" name="landmark" value="{{ $address->landmark }}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="special_instructions{{ $address->id }}" class="form-label">Special Instructions (Optional)</label>
                                                <textarea class="form-control text-capitalize" id="special_instructions{{ $address->id }}" name="special_instructions">{{ $address->special_instructions }}</textarea>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="is_default{{ $address->id }}" name="is_default" value="1" {{ $address->is_default ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_default{{ $address->id }}">Set as default address</label>
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
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                You haven't added any addresses yet. Click the "Add New Address" button to add your first address.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('customer.address_book.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="label" class="form-label">Label (Optional)</label>
                        <input type="text" class="form-control text-capitalize" id="label" name="label">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control text-capitalize" id="first_name" name="first_name" value="{{ Auth::user()->first_name }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control text-capitalize" id="last_name" name="last_name" value="{{ Auth::user()->last_name }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="company" class="form-label">Company (Optional)</label>
                        <input type="text" class="form-control text-capitalize" id="company" name="company">
                    </div>
                    <div class="mb-3">
                        <label for="street_address" class="form-label">Street Address</label>
                        <input type="text" class="form-control text-capitalize" id="street_address" name="street_address" required>
                    </div>
                    <div class="mb-3">
                        <label for="barangay" class="form-label">Barangay</label>
                        <input type="text" class="form-control text-capitalize" id="barangay" name="barangay" required>
                    </div>
                    <div class="mb-3">
                        <label for="municipality" class="form-label">Municipality</label>
                        <input type="text" class="form-control text-capitalize" id="municipality" name="municipality">
                    </div>
                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control text-capitalize" id="city" name="city" required>
                    </div>
                    <div class="mb-3">
                        <label for="province" class="form-label">Province (Optional)</label>
                        <input type="text" class="form-control text-capitalize" id="province" name="province">
                    </div>
                    <div class="mb-3">
                        <label for="region" class="form-label">Region</label>
                        <input type="text" class="form-control text-capitalize" id="region" name="region" value="Region VII" required>
                    </div>
                    <div class="mb-3">
                        <label for="zip_code" class="form-label">ZIP Code</label>
                        <input type="text" class="form-control text-capitalize" id="zip_code" name="zip_code" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control text-capitalize" id="phone_number" name="phone_number" value="{{ Auth::user()->contact_number }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="landmark" class="form-label">Landmark (Optional)</label>
                        <input type="text" class="form-control text-capitalize" id="landmark" name="landmark" value="{{ old('landmark') }}">
                    </div>
                    <div class="mb-3">
                        <label for="special_instructions" class="form-label">Special Instructions (Optional)</label>
                        <textarea class="form-control text-capitalize" id="special_instructions" name="special_instructions">{{ old('special_instructions') }}</textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_default" name="is_default" value="1">
                        <label class="form-check-label" for="is_default">Set as default address</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Address</button>
                </div>
            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize location detection
    initializeLocationDetection();
    
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

// Location Detection Functions
function initializeLocationDetection() {
    console.log('Initializing location detection for address book...');
    
    // Check if geolocation is supported
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                
                // Update location via API
                updateLocationData(latitude, longitude);
            },
            function(error) {
                console.log('Geolocation error:', error);
                // Fallback to default location (Cebu)
                updateLocationData(null, null, 'Cebu');
            }
        );
    } else {
        // Fallback to default location (Cebu)
        updateLocationData(null, null, 'Cebu');
    }
}

function updateLocationData(latitude, longitude, city) {
    console.log('Updating location data:', { latitude, longitude, city });
    
    fetch('{{ route("geo.location.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            latitude: latitude,
            longitude: longitude,
            city: city
        })
    })
    .then(response => {
        console.log('Location update response:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Location update data:', data);
        if (data.success) {
            showLocationCard(data.location);
        } else {
            console.error('Location update failed:', data.message);
        }
    })
    .catch(error => {
        console.error('Error updating location:', error);
        alert('Error updating location. Please try again.');
    })
    .finally(() => {
        // Reset button
        const updateBtn = document.querySelector('button[onclick="updateLocation()"]');
        if (updateBtn) {
            updateBtn.innerHTML = '<i class="fas fa-sync-alt me-1"></i>Update';
            updateBtn.disabled = false;
        }
    });
}

function showLocationCard(location) {
    const locationCard = document.getElementById('locationCard');
    const locationText = document.getElementById('locationText');
    
    if (locationCard && locationText) {
        locationText.textContent = `Showing recommendations for ${location.city}`;
        locationCard.style.display = 'block';
    }
}

function updateLocation() {
    console.log('Update location clicked');
    
    // Show loading state
    const updateBtn = document.querySelector('button[onclick="updateLocation()"]');
    if (updateBtn) {
        updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
        updateBtn.disabled = true;
    }
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                console.log('Location obtained:', position.coords);
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                updateLocationData(latitude, longitude);
            },
            function(error) {
                console.error('Geolocation error:', error);
                let errorMessage = 'Unable to get your location. ';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += 'Please allow location access and try again.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += 'Location information is unavailable.';
                        break;
                    case error.TIMEOUT:
                        errorMessage += 'Location request timed out.';
                        break;
                    default:
                        errorMessage += 'Please try again.';
                        break;
                }
                alert(errorMessage);
                
                // Reset button
                if (updateBtn) {
                    updateBtn.innerHTML = '<i class="fas fa-sync-alt me-1"></i>Update';
                    updateBtn.disabled = false;
                }
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 300000
            }
        );
    } else {
        alert('Geolocation is not supported by this browser.');
        // Reset button
        if (updateBtn) {
            updateBtn.innerHTML = '<i class="fas fa-sync-alt me-1"></i>Update';
            updateBtn.disabled = false;
        }
    }
}
</script>
@endpush
@endsection 