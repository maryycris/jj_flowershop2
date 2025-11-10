@props(['selectedAddress' => ''])

<div class="delivery-map-container">
    <!-- Pickup Location Section -->
    <div class="mb-3">
        <label class="form-label fw-semibold">
            <i class="fas fa-store me-2 text-primary"></i>Pickup Location
        </label>
        <div class="input-group position-relative">
            <input type="text" 
                   class="form-control" 
                   id="pickupAddressInput" 
                   placeholder="Where to pick up the item..."
                   value="J'J Flower Shop, Bangbang, Cordova, Cebu"
                   autocomplete="off"
                   readonly
                   style="border-radius: 8px 0 0 8px; background-color: #f8f9fa;">
            <button class="btn btn-outline-primary" 
                    type="button" 
                    id="pickupGeocodeBtn"
                    style="border-radius: 0 8px 8px 0;">
                <i class="fas fa-map-marker-alt"></i> SHOP
            </button>
        </div>
        <small class="text-muted">Our shop location (fixed)</small>
    </div>

    <!-- Drop-off Location Section -->
    <div class="mb-3">
        <label class="form-label fw-semibold">
            <i class="fas fa-flag-checkered me-2 text-success"></i>Drop-off Location
        </label>
        <div class="input-group position-relative">
            <input type="text" 
                   class="form-control" 
                   id="deliveryAddressInput" 
                   placeholder="Where to deliver the item..."
                   value="{{ $selectedAddress }}"
                   autocomplete="off"
                   style="border-radius: 8px 0 0 8px;">
            <button class="btn btn-outline-success" 
                    type="button" 
                    id="geocodeBtn"
                    style="border-radius: 0 8px 8px 0;">
                <i class="fas fa-search"></i> FIND
            </button>
            
            <!-- Address Autocomplete Dropdown -->
            <div id="addressAutocomplete" class="address-autocomplete" style="display: none;">
                <ul id="addressSuggestions" class="list-group"></ul>
            </div>
        </div>
        <small class="text-muted">Customer's delivery address</small>
    </div>

    <!-- Address Validation Alert -->
    <div class="mb-3" id="addressValidationAlert" style="display: none;">
        <div class="alert alert-warning" style="background-color: #fff3cd; border-color: #ffeaa7; color: #856404;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Invalid Address!</strong> <span id="validationMessage">Please enter a valid address with barangay, street, municipality, or landmark.</span>
        </div>
    </div>

    <!-- Route Information Section -->
    <div class="mb-3" id="routeInfo" style="display: none;">
        <div class="alert alert-info" style="background-color: #e3f2fd; border-color: #2196f3; color: #1565c0;">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-route me-2"></i>
                <strong>Delivery Route</strong>
            </div>
            <div class="row">
                <div class="col-4">
                    <small class="text-muted">Distance:</small><br>
                    <strong id="distanceDisplay">-</strong>
                </div>
                <div class="col-4">
                    <small class="text-muted">Duration:</small><br>
                    <strong id="durationDisplay">-</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <button class="btn btn-success" id="showMapBtn" style="display: inline-block;">
            <i class="fas fa-map"></i> SHOW MAP
        </button>
        <button class="btn btn-outline-secondary" id="hideMapBtn" style="display: none;">
            <i class="fas fa-eye-slash"></i> Hide Map
        </button>
    </div>

    <div id="mapContainer" style="height: 400px; border-radius: 8px; border: 1px solid #ddd; display: none;">
        <div id="map" style="height: 100%; width: 100%; border-radius: 8px;"></div>
    </div>

    <div id="routeInfo" class="mt-3" style="display: none;">
        <div class="alert alert-info">
            <div class="row">
                <div class="col-md-6">
                    <strong>Distance:</strong> <span id="routeDistance">-</span>
                </div>
                <div class="col-md-6">
                    <strong>Estimated Time:</strong> <span id="routeDuration">-</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
.delivery-map-container {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    background: #f8f9fa;
}

#map {
    border-radius: 8px;
}

.leaflet-popup-content {
    font-size: 14px;
}

/* Address Autocomplete Styles */
.address-autocomplete {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    max-height: 300px;
    overflow-y: auto;
}

.address-autocomplete .list-group {
    margin: 0;
    border: none;
}

.address-autocomplete .list-group-item {
    border: none;
    border-bottom: 1px solid #f0f0f0;
    padding: 12px 16px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.address-autocomplete .list-group-item:hover {
    background-color: #f8f9fa;
}

.address-autocomplete .list-group-item:last-child {
    border-bottom: none;
}

.address-suggestion {
    font-size: 14px;
    color: #333;
}

.address-suggestion .main-text {
    font-weight: 500;
    color: #2c3e50;
}

.address-suggestion .secondary-text {
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
}

.address-suggestion .icon {
    color: #8ACB88;
    margin-right: 8px;
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Delivery map component loaded at:', new Date().toISOString());
    
    // Clear any potential undefined variables
    if (typeof shippingEl !== 'undefined') {
        console.log('shippingEl was defined, clearing it');
        delete window.shippingEl;
    }

    let map = null;
    let marker = null;
    let shopMarker = null;
    let routeLayer = null;
    
    const mapContainer = document.getElementById('mapContainer');
    const pickupInput = document.getElementById('pickupAddressInput');
    const deliveryInput = document.getElementById('deliveryAddressInput');
    const geocodeBtn = document.getElementById('geocodeBtn');
    const pickupGeocodeBtn = document.getElementById('pickupGeocodeBtn');
    const showMapBtn = document.getElementById('showMapBtn');
    const hideMapBtn = document.getElementById('hideMapBtn');
    const routeInfo = document.getElementById('routeInfo');
    const addressAutocomplete = document.getElementById('addressAutocomplete');
    const addressSuggestions = document.getElementById('addressSuggestions');
    
    console.log('Elements found:', {
        mapContainer: !!mapContainer,
        deliveryInput: !!deliveryInput,
        geocodeBtn: !!geocodeBtn,
        showMapBtn: !!showMapBtn,
        hideMapBtn: !!hideMapBtn
    });
    
    // Address Autocomplete Variables
    let autocompleteTimeout = null;
    let selectedSuggestionIndex = -1;

    // Initialize map
    function initMap() {
        if (map) return;

        map = L.map('map', {
            preferCanvas: false,
            zoomControl: true,
            attributionControl: true
        }).setView([10.2588, 123.9445], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(map);

        // Add shop marker - Bangbang, Cordova (blue marker)
        const shopIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div style='background-color:blue;width:24px;height:24px;border-radius:50%;border:4px solid white;box-shadow:0 3px 6px rgba(0,0,0,0.4);z-index:1000;'></div>",
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });
        shopMarker = L.marker([10.2588, 123.9445], {icon: shopIcon, zIndexOffset: 1000}).addTo(map);
        shopMarker.bindPopup('<b>🏪 J&J Flower Shop</b><br>📍 Bangbang, Cordova, Cebu').openPopup();
        
        // Ensure map fits properly in container
        setTimeout(() => {
            map.invalidateSize();
        }, 100);
    }
    
    // Clear validation errors when user types in address field
    deliveryInput.addEventListener('input', function() {
        hideValidationError();
        
        // Trigger autocomplete search
        const query = this.value.trim();
        clearTimeout(autocompleteTimeout);
        autocompleteTimeout = setTimeout(() => {
            searchAddresses(query);
        }, 300); // 300ms delay to avoid too many API calls
    });
    
    // Handle keyboard navigation in autocomplete
    deliveryInput.addEventListener('keydown', function(e) {
        if (addressAutocomplete.style.display === 'none') return;
        
        const items = addressSuggestions.querySelectorAll('.list-group-item');
        
        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                selectedSuggestionIndex = Math.min(selectedSuggestionIndex + 1, items.length - 1);
                updateSelectedSuggestion();
                break;
            case 'ArrowUp':
                e.preventDefault();
                selectedSuggestionIndex = Math.max(selectedSuggestionIndex - 1, 0);
                updateSelectedSuggestion();
                break;
            case 'Enter':
                e.preventDefault();
                if (selectedSuggestionIndex >= 0 && items[selectedSuggestionIndex]) {
                    items[selectedSuggestionIndex].click();
                }
                break;
            case 'Escape':
                hideAutocomplete();
                break;
        }
    });
    
    // Hide autocomplete when clicking outside
    document.addEventListener('click', function(e) {
        if (!deliveryInput.contains(e.target) && !addressAutocomplete.contains(e.target)) {
            hideAutocomplete();
        }
    });
    
    // Address Autocomplete Functions
    function searchAddresses(query) {
        if (query.length < 3) {
            hideAutocomplete();
            return;
        }
        
        // Use Nominatim API for address suggestions
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=ph&limit=5&addressdetails=1`)
            .then(response => response.json())
            .then(data => {
                displaySuggestions(data);
            })
            .catch(error => {
                console.error('Address search error:', error);
                hideAutocomplete();
            });
    }
    
    function displaySuggestions(suggestions) {
        if (suggestions.length === 0) {
            hideAutocomplete();
            return;
        }
        
        addressSuggestions.innerHTML = '';
        
        suggestions.forEach((suggestion, index) => {
            const li = document.createElement('li');
            li.className = 'list-group-item';
            li.innerHTML = `
                <div class="address-suggestion">
                    <div class="main-text">
                        <i class="fas fa-map-marker-alt icon"></i>
                        ${suggestion.display_name.split(',')[0]}
                    </div>
                    <div class="secondary-text">
                        ${suggestion.display_name.split(',').slice(1).join(',').trim()}
                    </div>
                </div>
            `;
            
            li.addEventListener('click', () => {
                selectAddress(suggestion);
            });
            
            li.addEventListener('mouseenter', () => {
                selectedSuggestionIndex = index;
                updateSelectedSuggestion();
            });
            
            addressSuggestions.appendChild(li);
        });
        
        addressAutocomplete.style.display = 'block';
        selectedSuggestionIndex = 0;
        updateSelectedSuggestion();
    }
    
    function selectAddress(suggestion) {
        deliveryInput.value = suggestion.display_name;
        hideAutocomplete();
        
        // Automatically geocode the selected address
        geocodeAddress(suggestion.display_name);
    }
    
    function hideAutocomplete() {
        addressAutocomplete.style.display = 'none';
        selectedSuggestionIndex = -1;
    }
    
    function updateSelectedSuggestion() {
        const items = addressSuggestions.querySelectorAll('.list-group-item');
        items.forEach((item, index) => {
            if (index === selectedSuggestionIndex) {
                item.style.backgroundColor = '#e3f2fd';
            } else {
                item.style.backgroundColor = '';
            }
        });
    }
    
    function geocodeAddress(address) {
        // Use the existing geocoding logic
        geocodeBtn.click();
    }

    // Show map button click
    showMapBtn.addEventListener('click', function() {
        initMap();
        mapContainer.style.display = 'block';
        showMapBtn.style.display = 'none';
        hideMapBtn.style.display = 'inline-block';
            
        // Ensure map resizes properly when shown
        setTimeout(() => {
            if (map) {
                map.invalidateSize();
            }
        }, 200);
        
        // Make sure shop marker is visible
        if (shopMarker) {
            shopMarker.openPopup();
        }
    });
    
    // Hide map button click
    hideMapBtn.addEventListener('click', function() {
        mapContainer.style.display = 'none';
        showMapBtn.style.display = 'inline-block';
        hideMapBtn.style.display = 'none';
    });
    
    // Address validation function - requires exact location details
    function validateAddress(address) {
        const lowerAddress = address.toLowerCase();
        
        // Check for specific barangay names (exact matches)
        const hasSpecificBarangay = /\b(barangay\s+\w+|brgy\s+\w+|purok\s+\w+|sitio\s+\w+|village\s+\w+|subdivision\s+\w+)\b/.test(lowerAddress);
        
        // Check for specific street names (exact matches) - improved regex
        const hasSpecificStreet = /\b(street\s+\w+|st\s+\w+|road\s+\w+|rd\s+\w+|avenue\s+\w+|ave\s+\w+|boulevard\s+\w+|blvd\s+\w+|drive\s+\w+|dr\s+\w+|lane\s+\w+|ln\s+\w+|way\s+\w+|highway\s+\w+|hwy\s+\w+)\b/.test(lowerAddress);
        
        // Check for specific municipality/city - improved to include compound names
        const hasSpecificMunicipality = /\b(cordova|cebu\s+city|mandaue\s+city|mandaue|lapu-lapu\s+city|lapulapu\s+city|lapu\s+lapu|talisay\s+city|minglanilla|kalawisan|sambag\s+[12]|mactan|central\s+visayas)\b/.test(lowerAddress);
        
        // Check for specific landmarks
        const hasSpecificLandmark = /\b(sm\s+mall|ayala\s+center|robinsons|gaisano|colon\s+street|fuente\s+osmeña|carbon\s+market|cebu\s+doctors|chong\s+hua|perpetual\s+succour|abing\s+compound)\b/.test(lowerAddress);
        
        // Check for common typos that should be rejected
        const hasTypo = /\b(corfova|corfava|cordava|sumbag)\b/.test(lowerAddress);
        
        // Check if address contains complete location info (street + city/municipality)
        const hasCompleteAddress = /\b\w+\s+(road|street|avenue|boulevard|drive|lane|way|highway)\b.*\b(cebu|mandaue|lapu-lapu|lapulapu|lapu\s+lapu|mactan|cordova|talisay|minglanilla)\b/i.test(address);
        
        // Must have specific location details and no typos, OR complete address format
        return ((hasSpecificBarangay || hasSpecificStreet || hasSpecificMunicipality || hasSpecificLandmark) && !hasTypo) || (hasCompleteAddress && !hasTypo);
    }

    // Show validation error
    function showValidationError(message) {
        const alert = document.getElementById('addressValidationAlert');
        const messageSpan = document.getElementById('validationMessage');
        if (alert && messageSpan) {
            messageSpan.textContent = message;
            alert.style.display = 'block';
        }
        
        // Hide shipping info
        const shippingInfo = document.getElementById('shippingInfo');
        if (shippingInfo) {
            shippingInfo.style.display = 'none';
        }
        
        // Reset shipping fee
        if (typeof updateShippingFeeDisplay === 'function') {
            updateShippingFeeDisplay(0);
        }
    }

    // Hide validation error
    function hideValidationError() {
        const alert = document.getElementById('addressValidationAlert');
        if (alert) {
            alert.style.display = 'none';
        }
    }

    // Pickup location button click
    pickupGeocodeBtn.addEventListener('click', function() {
        // Show shop location on map
        initMap();
        if (map) {
            map.setView([10.2588, 123.9445], 15);
            if (shopMarker) {
                shopMarker.openPopup();
            }
        }
    });

    // Geocode address
    geocodeBtn.addEventListener('click', function() {
        console.log('FIND button clicked');
        const address = deliveryInput.value.trim();
        console.log('Address to geocode:', address);
        
        if (!address) {
            showValidationError('Please enter an address');
            return;
        }
        
        // Validate address format
        if (!validateAddress(address)) {
            const lowerAddress = address.toLowerCase();
            if (/\b(corfova|corfava|cordava)\b/.test(lowerAddress)) {
                showValidationError('Address contains a typo. Please check your spelling - did you mean "Cordova"? (e.g., "Purok 1, Barangay Bangbang, Cordova, Cebu")');
            } else if (/\b(sumbag)\b/.test(lowerAddress)) {
                showValidationError('Address contains a typo. Please check your spelling - did you mean "Sambag"? (e.g., "Sambag 1, Cebu City" or "Sambag 2, Cebu City")');
            } else {
                showValidationError('Please enter a complete address with exact location details that can be found on Google Maps. Include specific barangay, street name, or landmark (e.g., "Purok 1, Barangay Bangbang, Cordova, Cebu" or "Colon Street, Cebu City" or "SM Mall, Cebu City")');
            }
            return;
        }
        
        // Hide any previous validation errors
        hideValidationError();
        
        geocodeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Finding...';
        geocodeBtn.disabled = true;
        
        // Show immediate loading state for distance and shipping
        const shippingInfo = document.getElementById('shippingInfo');
        if (shippingInfo) {
            shippingInfo.style.display = 'block';
            document.getElementById('distanceDisplay').textContent = 'Calculating...';
            // Shipping display removed
        }
        // Also show new route info panel immediately
        if (routeInfo) {
            routeInfo.style.display = 'block';
            const distanceEl = document.getElementById('distanceDisplay');
            // Shipping display removed
            if (distanceEl) distanceEl.textContent = 'Calculating...';
            // Shipping display removed
        }
        
        // Set a timeout to reset the button if it gets stuck
        const timeoutId = setTimeout(() => {
            geocodeBtn.innerHTML = '<i class="fas fa-search"></i> FIND';
            geocodeBtn.disabled = false;
            console.log('Geocoding timeout - button reset');
            showValidationError('Request timed out. Please try again with a more specific address.');
        }, 5000); // 5 second timeout (reduced since we have 3s fallback)
        
        console.log('Making geocoding request to /api/map/geocode');
        console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
        console.log('Address being sent:', address);
        
        // Try API first, with fallback
        const apiPromise = fetch('/api/map/geocode', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({ address: address })
        });
        
        // Fallback promise that resolves after 3 seconds
        const fallbackPromise = new Promise((resolve) => {
            setTimeout(() => {
                console.log('Using fallback geocoding for:', address);
                const lowerAddress = address.toLowerCase();
                
                // Simple fallback coordinates for common Cebu locations
                let coords = null;
                if (lowerAddress.includes('cebu city') || lowerAddress.includes('cebu')) {
                    coords = { lat: 10.2588, lng: 123.9445 };
                } else if (lowerAddress.includes('mandaue')) {
                    coords = { lat: 10.3333, lng: 123.9333 };
                } else if (lowerAddress.includes('lapu-lapu') || lowerAddress.includes('lapulapu')) {
                    coords = { lat: 10.3103, lng: 123.9494 };
                } else if (lowerAddress.includes('talisay')) {
                    coords = { lat: 10.2447, lng: 123.8425 };
                } else {
                    // Default to Cebu City
                    coords = { lat: 10.2588, lng: 123.9445 };
                }
                
                resolve({
                    ok: true,
                    json: () => Promise.resolve({
                        success: true,
                        latitude: coords.lat,
                        longitude: coords.lng,
                        address: address
                    })
                });
            }, 3000);
        });
        
        // Race between API and fallback
        Promise.race([apiPromise, fallbackPromise])
        .then(r => {
            console.log('Response status:', r.status);
            console.log('Response headers:', r.headers);
            return r.json();
        })
        .then(data => {
            console.log('Response data:', data);
            // Always clear timeout and reset button first
            clearTimeout(timeoutId);
            geocodeBtn.innerHTML = '<i class="fas fa-search"></i> FIND';
            geocodeBtn.disabled = false;
            
            if (data && data.success) {
                hideValidationError();
                // Make sure map is visible
                showMapBtn.click();
                addMarkerToMap(data.latitude, data.longitude, address);
                calculateRoute(data.latitude, data.longitude);
                // Show route info and set initial values
                if (routeInfo) {
                    routeInfo.style.display = 'block';
                }
                const shippingInfo = document.getElementById('shippingInfo');
                if (shippingInfo) {
                    shippingInfo.style.display = 'block';
                }
                // Only calculate shipping for verified addresses
                calculateShipping(address);
            } else {
                showMapBtn.style.display = 'inline-block';
                console.log('Address not found:', data ? data.message : 'No response data');
                showValidationError('Address not found on Google Maps. Please enter an exact location with specific barangay, street name, or landmark that can be verified on the map. (e.g., "Purok 1, Barangay Bangbang, Cordova, Cebu" or "Colon Street, Cebu City")');
                // Hide shipping/route info for invalid addresses
                const shippingInfo = document.getElementById('shippingInfo');
                if (shippingInfo) {
                    shippingInfo.style.display = 'none';
                }
                if (routeInfo) {
                    routeInfo.style.display = 'none';
                }
                // Clear any existing shipping calculations
                const distanceEl = document.getElementById('distanceDisplay');
                if (distanceEl) distanceEl.textContent = '-';
            }
        })
        .catch(error => {
            // Always clear timeout and reset button on error
            clearTimeout(timeoutId);
            geocodeBtn.innerHTML = '<i class="fas fa-search"></i> FIND';
            geocodeBtn.disabled = false;
            console.error('Geocoding error:', error);
            showMapBtn.style.display = 'inline-block';
            
            // Show user-friendly error message
            showValidationError('Unable to process address. Please check your internet connection and try again with a more specific address.');
            
            // Hide shipping info for errors
            const shippingInfo = document.getElementById('shippingInfo');
            if (shippingInfo) {
                shippingInfo.style.display = 'none';
            }
            // Clear any existing shipping calculations
            const distanceEl = document.getElementById('distanceDisplay');
            if (distanceEl) distanceEl.textContent = '-';
        })
        .finally(() => {
            clearTimeout(timeoutId);
            geocodeBtn.innerHTML = '<i class="fas fa-search"></i> FIND';
            geocodeBtn.disabled = false;
        });
    });
    
    // Add marker to map
    function addMarkerToMap(lat, lng, address) {
        initMap();
        
        // Ensure shop marker exists
        if (!shopMarker) {
            console.log('Creating shop marker...');
            const shopIcon = L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color:blue;width:24px;height:24px;border-radius:50%;border:4px solid white;box-shadow:0 3px 6px rgba(0,0,0,0.4);z-index:1000;'></div>",
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });
            shopMarker = L.marker([10.2588, 123.9445], {icon: shopIcon, zIndexOffset: 1000}).addTo(map);
            shopMarker.bindPopup('<b>🏪 J&J Flower Shop</b><br>📍 Bangbang, Cordova, Cebu');
            console.log('Shop marker created:', shopMarker);
        } else {
            console.log('Shop marker already exists:', shopMarker);
        }
        
        // Remove existing delivery marker
        if (marker) {
            map.removeLayer(marker);
        }
        
        // Add new delivery marker (red marker)
        console.log('Creating delivery marker at:', lat, lng, 'for address:', address);
        
        // If delivery coordinates are the same as shop coordinates, offset slightly
        let deliveryLat = lat;
        let deliveryLng = lng;
        if (Math.abs(lat - 10.2588) < 0.001 && Math.abs(lng - 123.9445) < 0.001) {
            deliveryLat = lat + 0.001; // Offset by ~100 meters
            deliveryLng = lng + 0.001;
            console.log('Offsetting delivery marker to:', deliveryLat, deliveryLng);
        }
        
        const deliveryIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div style='background-color:red;width:20px;height:20px;border-radius:50%;border:3px solid white;box-shadow:0 2px 4px rgba(0,0,0,0.3);'></div>",
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });
        marker = L.marker([deliveryLat, deliveryLng], {icon: deliveryIcon}).addTo(map);
        marker.bindPopup(`<b>🚚 Delivery Address</b><br>📍 ${address}`).openPopup();
        console.log('Delivery marker created:', marker);
        
        // Ensure map resizes properly after adding markers
        setTimeout(() => {
            if (map) {
                map.invalidateSize();
            }
        }, 100);
    }
    
    // Calculate route
    function calculateRoute(destLat, destLng) {
        // Pickup: J'J Flower Shop, Bangbang, Cordova, Cebu
        const pickupLat = 10.2588;
        const pickupLng = 123.9445;
        
        fetch('/api/map/route', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                origin_lat: pickupLat,
                origin_lng: pickupLng,
                dest_lat: destLat,
                dest_lng: destLng
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayRouteInfo(data.distance, data.duration);
                drawRoute(data.geometry);
            }
        })
        .catch(error => {
            console.error('Routing error:', error);
        });
    }
    
    // Display route information
    function displayRouteInfo(distance, duration) {
        const distanceKm = (distance / 1000).toFixed(1);
        const durationMin = Math.round(duration / 60);
        
        document.getElementById('distanceDisplay').textContent = distanceKm + ' km';
        document.getElementById('durationDisplay').textContent = durationMin + ' min';
        routeInfo.style.display = 'block';
    }
    
    // Draw route on map
    function drawRoute(geometry) {
        if (routeLayer) {
            map.removeLayer(routeLayer);
        }
        
        routeLayer = L.geoJSON(geometry, {
            style: {
                color: '#007bff',
                weight: 4,
                opacity: 0.7
            }
        }).addTo(map);
    }
    
    // Calculate shipping fee
    function calculateShipping(address) {
        console.log('Calculating shipping for:', address);
        
        // Only calculate shipping for validated addresses
        if (!validateAddress(address)) {
            console.log('Address validation failed, skipping shipping calculation');
            return;
        }
        
        // Add timeout for shipping calculation
        const shippingTimeout = setTimeout(() => {
            console.log('Shipping calculation timeout');
            document.getElementById('distanceDisplay').textContent = 'Calculating...';
            // Shipping display removed
        }, 8000); // 8 second timeout for shipping calculation
        
        fetch('/api/map/shipping-calculate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                origin_address: 'Bangbang, Cordova, Cebu',
                destination_address: address
            })
        })
        .then(response => response.json())
        .then(data => {
            clearTimeout(shippingTimeout); // Clear the timeout
            console.log('Shipping calculation response:', data);
            if (data.success) {
                // Show shipping info section
                const shippingInfo = document.getElementById('shippingInfo');
                if (shippingInfo) {
                    shippingInfo.style.display = 'block';
                }
                
                // Update distance display
                const distanceDisplay = document.getElementById('distanceDisplay');
                if (distanceDisplay && data.distance) {
                    distanceDisplay.textContent = data.distance + ' km';
                } else if (distanceDisplay) {
                    distanceDisplay.textContent = 'Estimated distance';
                }
                
                // Update shipping display in the info box
                // Shipping display removed
                        const hidden = document.getElementById('shipping_fee');
                        if (hidden) { hidden.value = data.shipping_fee; }
                        const delHidden = document.getElementById('deliveryAddressHidden');
                        const invHidden = document.getElementById('invoiceAddressInput');
                        const deliveryInput = document.getElementById('deliveryAddressInput');
                        if (deliveryInput) {
                            if (delHidden) delHidden.value = deliveryInput.value.trim();
                            if (invHidden) invHidden.value = deliveryInput.value.trim();
                        }
                
                // Update the shipping fee display in checkout summary
                const checkoutShippingDisplay = document.getElementById('shippingFeeDisplay');
                if (checkoutShippingDisplay) {
                    checkoutShippingDisplay.textContent = data.shipping_fee.toFixed(2);
                    console.log('Updated shipping fee display to:', data.shipping_fee.toFixed(2));
                }
                
                // Update clerk order summary shipping fee (if present on page)
                const clerkShippingFee = document.getElementById('shippingFee');
                console.log('Looking for clerk shipping fee element:', clerkShippingFee);
                if (clerkShippingFee) {
                    clerkShippingFee.textContent = data.shipping_fee.toFixed(2);
                    console.log('Updated clerk shipping fee to:', data.shipping_fee.toFixed(2));
                    
                    // Trigger recalculation
                    const recalcEvent = new Event('input');
                    const quantityInput = document.getElementById('quantityInput');
                    if (quantityInput) {
                        quantityInput.dispatchEvent(recalcEvent);
                    }
                } else {
                    console.log('Clerk shipping fee element not found!');
                }
                
                // Update hidden inputs for form submission
                const shippingInput = document.getElementById('shippingFeeInput');
                if (shippingInput) {
                    shippingInput.value = data.shipping_fee;
                    console.log('Updated shippingFeeInput to:', data.shipping_fee);
                }
                const shippingInputAlt = document.getElementById('shipping_fee');
                if (shippingInputAlt) {
                    shippingInputAlt.value = data.shipping_fee;
                    console.log('Updated shipping_fee to:', data.shipping_fee);
                }
                
                // Update the shipping fee in the order summary
                updateOrderSummaryShippingFee(data.shipping_fee);
                
                // Also update the total
                updateOrderTotal(data.shipping_fee);
            } else {
                console.error('Shipping calculation failed:', data.message);
                showValidationError('Unable to calculate shipping for this address. Please verify the address is correct.');
            }
        })
        .catch(error => {
            clearTimeout(shippingTimeout); // Clear the timeout
            console.error('Shipping calculation error:', error);
            // Don't show error message for network issues - just continue with fallback
            // Fallback: set a default shipping fee based on address
            let fallbackFee = 30.00; // Base fee for Cordova
            let estimatedDistance = 0;
            
            // Check if address is outside Cordova (including typo "corfova")
            const address = deliveryInput.value.trim().toLowerCase();
            if (!address.includes('cordova') && !address.includes('corfova')) {
                // Estimate additional fee for areas outside Cordova
                if (address.includes('minglanilla')) {
                    fallbackFee = 30 + (28 * 10); // P310.00 for Minglanilla
                    estimatedDistance = 28;
                } else if (address.includes('kalawisan')) {
                    fallbackFee = 30 + (13 * 10); // P160.00 for Kalawisan
                    estimatedDistance = 13;
                } else if (address.includes('cebu city') || address.includes('cebu')) {
                    fallbackFee = 30 + (18 * 10); // P210.00 for Cebu City
                    estimatedDistance = 18;
                } else if (address.includes('mandaue')) {
                    fallbackFee = 30 + (14 * 10); // P170.00 for Mandaue
                    estimatedDistance = 14;
                } else if (address.includes('lapu-lapu') || address.includes('lapulapu')) {
                    fallbackFee = 30 + (10 * 10); // P130.00 for Lapu-Lapu
                    estimatedDistance = 10;
                } else if (address.includes('talisay')) {
                    fallbackFee = 30 + (22 * 10); // P250.00 for Talisay
                    estimatedDistance = 22;
                } else {
                    fallbackFee = 30 + (25 * 10); // P280.00 for other areas
                    estimatedDistance = 25;
                }
            }
            
            console.log('Using fallback shipping fee:', fallbackFee, 'Distance:', estimatedDistance);
            
            // Add delivery marker with fallback coordinates
            let fallbackLat, fallbackLng;
            if (address.includes('cordova') || address.includes('bangbang')) {
                fallbackLat = 10.2588; fallbackLng = 123.9445; // Cordova/Bangbang coordinates (same as shop)
            } else if (address.includes('minglanilla')) {
                fallbackLat = 10.2333; fallbackLng = 123.7833; // Minglanilla coordinates
            } else if (address.includes('kalawisan')) {
                fallbackLat = 10.3103; fallbackLng = 123.9494; // Kalawisan coordinates (Lapu-Lapu area)
            } else if (address.includes('cebu city') || address.includes('cebu')) {
                fallbackLat = 10.3157; fallbackLng = 123.8854; // Cebu City coordinates (approx)
            } else if (address.includes('mandaue')) {
                fallbackLat = 10.3333; fallbackLng = 123.9333; // Mandaue coordinates
            } else if (address.includes('lapu-lapu') || address.includes('lapulapu')) {
                fallbackLat = 10.3103; fallbackLng = 123.9494; // Lapu-Lapu coordinates
            } else if (address.includes('talisay')) {
                fallbackLat = 10.2442; fallbackLng = 123.8422; // Talisay coordinates
            } else {
                fallbackLat = 10.2588; fallbackLng = 123.9445; // Default Cordova coordinates
            }
            
            // Add delivery marker to map
            addMarkerToMap(fallbackLat, fallbackLng, address);
            
            // Show shipping info section
            const shippingInfo = document.getElementById('shippingInfo');
            if (shippingInfo) {
                shippingInfo.style.display = 'block';
            }
            
            // Update distance display
            const distanceDisplay = document.getElementById('distanceDisplay');
            if (distanceDisplay) {
                distanceDisplay.textContent = estimatedDistance + ' km (estimated)';
            }
            
            // Update shipping display in the info box
            // Shipping display removed
            
            // Update the shipping fee display in checkout summary
            const checkoutShippingDisplay = document.getElementById('shippingFeeDisplay');
            if (checkoutShippingDisplay) {
                checkoutShippingDisplay.textContent = fallbackFee.toFixed(2);
            }
            
            // Update clerk order summary shipping fee (if present)
            const clerkShippingFee = document.getElementById('shippingFee');
            console.log('Looking for clerk shipping fee element (fallback):', clerkShippingFee);
            if (clerkShippingFee) {
                clerkShippingFee.textContent = fallbackFee.toFixed(2);
                console.log('Updated clerk shipping fee (fallback) to:', fallbackFee.toFixed(2));
                
                // Trigger recalculation
                const recalcEvent = new Event('input');
                const quantityInput = document.getElementById('quantityInput');
                if (quantityInput) {
                    quantityInput.dispatchEvent(recalcEvent);
                }
            } else {
                console.log('Clerk shipping fee element not found (fallback)!');
            }
            
            // Update the hidden inputs
            const shippingInput = document.getElementById('shippingFeeInput');
            if (shippingInput) {
                shippingInput.value = fallbackFee;
            }
            const shippingInputAlt = document.getElementById('shipping_fee');
            if (shippingInputAlt) {
                shippingInputAlt.value = fallbackFee;
            }
            
            // Update the order summary
            updateOrderSummaryShippingFee(fallbackFee);
            updateOrderTotal(fallbackFee);
        });
    }
    
    // Update order summary shipping fee
    function updateOrderSummaryShippingFee(shippingFee) {
        console.log('Updating shipping fee display:', shippingFee);
        
        const shippingDisplay = document.getElementById('shippingFeeDisplay');
        if (shippingDisplay) {
            shippingDisplay.textContent = shippingFee.toFixed(2);
            console.log('Updated shipping fee to:', shippingFee.toFixed(2));
        } else {
            console.error('shippingFeeDisplay element not found');
        }
    }
    
    // Update order total
    function updateOrderTotal(shippingFee) {
        console.log('Updating order total with shipping fee:', shippingFee);
        
        // Use specific element IDs for more reliable updates
        const subtotalElement = document.getElementById('cartSubtotal');
        const totalElement = document.getElementById('cartTotalFinal');
        
        if (subtotalElement && totalElement) {
            const subtotal = parseFloat(subtotalElement.textContent.replace(/,/g, '')) || 0;
            const newTotal = subtotal + shippingFee;
            
            // Update the total display
            totalElement.textContent = newTotal.toFixed(2);
            console.log('Updated total from', subtotal, 'to', newTotal);
        } else {
            console.error('Could not find subtotal or total elements');
        }
    }
    
    // Show map if address is pre-filled
    if (deliveryInput && deliveryInput.value.trim()) {
        console.log('Address pre-filled, auto-clicking geocode button');
        geocodeBtn.click();
    }
    
    // Add click listener to show map button for debugging
    if (showMapBtn) {
        showMapBtn.addEventListener('click', function() {
            console.log('Show Map button clicked');
        });
    }
});
</script>
@endpush
