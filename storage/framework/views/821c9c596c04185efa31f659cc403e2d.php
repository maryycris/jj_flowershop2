<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['selectedAddress' => '']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['selectedAddress' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div class="delivery-map-container">
                    <div class="mb-3">
        <label class="form-label fw-semibold">
            <i class="fas fa-map-marker-alt me-2 text-success"></i>Delivery Location
        </label>
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   id="deliveryAddressInput" 
                   placeholder="Enter delivery address"
                   value="<?php echo e($selectedAddress); ?>"
                   style="border-radius: 8px 0 0 8px;">
            <button class="btn btn-outline-success" 
                    type="button" 
                    id="geocodeBtn"
                    style="border-radius: 0 8px 8px 0;">
                <i class="fas fa-search"></i> FIND
                            </button>
                        </div>
                    </div>

    <!-- Distance and Shipping Information -->
    <div class="mb-3" id="shippingInfo" style="display: none;">
        <div class="alert alert-info" style="background-color: #e8f5e8; border-color: #8ACB88; color: #2d5a2d;">
                        <div class="row">
                <div class="col-6">
                    <small class="text-muted">Distance:</small><br>
                    <strong id="distanceDisplay">-</strong>
                                </div>
                <div class="col-6">
                    <small class="text-muted">Shipping Fee:</small><br>
                    <strong id="shippingDisplay">P-</strong>
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

    <div id="shippingInfo" class="mt-3" style="display: none;">
        <div class="alert alert-success">
            <strong>Shipping Fee:</strong> ₱<span id="shippingFee">0.00</span>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
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
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Delivery map component loaded');

    let map = null;
    let marker = null;
    let shopMarker = null;
    let routeLayer = null;
    
    const mapContainer = document.getElementById('mapContainer');
    const deliveryInput = document.getElementById('deliveryAddressInput');
    const geocodeBtn = document.getElementById('geocodeBtn');
    const showMapBtn = document.getElementById('showMapBtn');
    const hideMapBtn = document.getElementById('hideMapBtn');
    const routeInfo = document.getElementById('routeInfo');
    const shippingInfo = document.getElementById('shippingInfo');
    
    console.log('Elements found:', {
        mapContainer: !!mapContainer,
        deliveryInput: !!deliveryInput,
        geocodeBtn: !!geocodeBtn,
        showMapBtn: !!showMapBtn,
        hideMapBtn: !!hideMapBtn
    });

    // Initialize map
    function initMap() {
        if (map) return;

        map = L.map('map', {
            preferCanvas: false,
            zoomControl: true,
            attributionControl: true
        }).setView([10.3157, 123.8854], 13);
        
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
        shopMarker = L.marker([10.3157, 123.8854], {icon: shopIcon, zIndexOffset: 1000}).addTo(map);
        shopMarker.bindPopup('<b>🏪 J&J Flower Shop</b><br>📍 Bangbang, Cordova, Cebu').openPopup();
        
        // Ensure map fits properly in container
        setTimeout(() => {
            map.invalidateSize();
        }, 100);
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
    
    // Geocode address
    geocodeBtn.addEventListener('click', function() {
        console.log('FIND button clicked');
        const address = deliveryInput.value.trim();
        console.log('Address to geocode:', address);
        if (!address) {
            alert('Please enter an address');
            return;
        }
        
        geocodeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Finding...';
        geocodeBtn.disabled = true;
        
        // Set a timeout to reset the button if it gets stuck
        const timeoutId = setTimeout(() => {
            geocodeBtn.innerHTML = '<i class="fas fa-search"></i> FIND';
            geocodeBtn.disabled = false;
            console.log('Geocoding timeout - button reset');
            // Still show the map button even if geocoding times out
            showMapBtn.style.display = 'inline-block';
        }, 15000); // 15 second timeout
        
        // Calculate shipping fee based on address
        calculateShipping(address);
        
        console.log('Making geocoding request to /api/map/geocode');
        console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
        
        fetch('/api/map/geocode', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ address: address })
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.text();
        })
        .then(text => {
            console.log('Raw response:', text);
            try {
                const data = JSON.parse(text);
                console.log('Parsed data:', data);
                if (data.success) {
                    showMapBtn.style.display = 'inline-block';
                    addMarkerToMap(data.latitude, data.longitude, address);
                    calculateRoute(data.latitude, data.longitude);
                    console.log('Geocoding successful for:', address);
                } else {
                    console.error('Geocoding failed:', data.message);
                    // Still show the map button even if geocoding fails
                    showMapBtn.style.display = 'inline-block';
                    // Don't show alert, just log to console
                    console.log('Address not found: ' + data.message + '. You can still view the map manually.');
                }
            } catch (e) {
                console.error('JSON parse error:', e);
                console.error('Response was not valid JSON:', text);
                showMapBtn.style.display = 'inline-block';
                console.log('Server error, but showing map anyway');
            }
        })
        .catch(error => {
            console.error('Geocoding error:', error);
            // Still show the map button even if there's an error
            showMapBtn.style.display = 'inline-block';
            // Don't show alert, just log to console
            console.log('Error geocoding address. You can still view the map manually.');
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
            shopMarker = L.marker([10.3157, 123.8854], {icon: shopIcon, zIndexOffset: 1000}).addTo(map);
            shopMarker.bindPopup('<b>🏪 J&J Flower Shop</b><br>📍 Bangbang, Cordova, Cebu');
            console.log('Shop marker created:', shopMarker);
        } else {
            console.log('Shop marker already exists:', shopMarker);
        }
        
        // Remove existing delivery marker
        if (marker) {
            map.removeLayer(marker);
        }
        
        // Add new delivery marker (blue marker)
        console.log('Creating delivery marker at:', lat, lng, 'for address:', address);
        
        // If delivery coordinates are the same as shop coordinates, offset slightly
        let deliveryLat = lat;
        let deliveryLng = lng;
        if (Math.abs(lat - 10.3157) < 0.001 && Math.abs(lng - 123.8854) < 0.001) {
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
        
        // Don't auto-fit map bounds - let user control zoom
        
        // Ensure map resizes properly after adding markers
        setTimeout(() => {
            if (map) {
                map.invalidateSize();
            }
        }, 100);
    }
    
    // Calculate route
    function calculateRoute(destLat, destLng) {
        // Origin: Bangbang, Cordova, Cebu
        const originLat = 10.3157;
        const originLng = 123.8854;
        
        fetch('/api/map/route', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                origin_lat: originLat,
                origin_lng: originLng,
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
        
        document.getElementById('routeDistance').textContent = distanceKm + ' km';
        document.getElementById('routeDuration').textContent = durationMin + ' minutes';
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
        
        fetch('/api/map/shipping-calculate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                origin_address: 'Bangbang, Cordova, Cebu',
                destination_address: address
            })
        })
        .then(response => response.json())
        .then(data => {
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
                const shippingDisplay = document.getElementById('shippingDisplay');
                if (shippingDisplay) {
                    shippingDisplay.textContent = 'P' + data.shipping_fee.toFixed(2);
                }
                
                // Update the shipping fee display in checkout summary
        const checkoutShippingDisplay = document.getElementById('shippingFeeDisplay');
        if (checkoutShippingDisplay) {
                    checkoutShippingDisplay.textContent = data.shipping_fee.toFixed(2);
                    console.log('Updated shipping fee display to:', data.shipping_fee.toFixed(2));
                }
                
                // Update the hidden input for form submission
                const shippingInput = document.getElementById('shippingFeeInput');
                if (shippingInput) {
                    shippingInput.value = data.shipping_fee;
                    console.log('Updated shipping fee input to:', data.shipping_fee);
                }
                
                // Update the shipping fee in the order summary
                updateOrderSummaryShippingFee(data.shipping_fee);
                
                // Also update the total
                updateOrderTotal(data.shipping_fee);
        } else {
                console.error('Shipping calculation failed:', data.message);
            }
        })
        .catch(error => {
            console.error('Shipping calculation error:', error);
            // Fallback: set a default shipping fee based on address
            let fallbackFee = 30.00; // Base fee for Cordova
            let estimatedDistance = 0;
            
            // Check if address is outside Cordova
            const address = deliveryInput.value.trim().toLowerCase();
            if (!address.includes('cordova')) {
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
                fallbackLat = 10.3157; fallbackLng = 123.8854; // Cordova/Bangbang coordinates (same as shop)
            } else if (address.includes('minglanilla')) {
                fallbackLat = 10.2333; fallbackLng = 123.7833; // Minglanilla coordinates
            } else if (address.includes('kalawisan')) {
                fallbackLat = 10.3103; fallbackLng = 123.9494; // Kalawisan coordinates (Lapu-Lapu area)
            } else if (address.includes('cebu city') || address.includes('cebu')) {
                fallbackLat = 10.3157; fallbackLng = 123.8854; // Cebu City coordinates
            } else if (address.includes('mandaue')) {
                fallbackLat = 10.3333; fallbackLng = 123.9333; // Mandaue coordinates
            } else if (address.includes('lapu-lapu') || address.includes('lapulapu')) {
                fallbackLat = 10.3103; fallbackLng = 123.9494; // Lapu-Lapu coordinates
            } else if (address.includes('talisay')) {
                fallbackLat = 10.2442; fallbackLng = 123.8422; // Talisay coordinates
            } else {
                fallbackLat = 10.3157; fallbackLng = 123.8854; // Default Cebu coordinates
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
            const shippingDisplay = document.getElementById('shippingDisplay');
            if (shippingDisplay) {
                shippingDisplay.textContent = 'P' + fallbackFee.toFixed(2);
            }
            
            // Update the shipping fee display in checkout summary
        const checkoutShippingDisplay = document.getElementById('shippingFeeDisplay');
        if (checkoutShippingDisplay) {
                checkoutShippingDisplay.textContent = fallbackFee.toFixed(2);
            }
            
            // Update the hidden input
            const shippingInput = document.getElementById('shippingFeeInput');
            if (shippingInput) {
                shippingInput.value = fallbackFee;
            }
            
            // Update the order summary
            updateOrderSummaryShippingFee(fallbackFee);
            updateOrderTotal(fallbackFee);
        });
    }
    
    // Update order summary shipping fee
    function updateOrderSummaryShippingFee(shippingFee) {
        console.log('Updating shipping fee to:', `P${shippingFee.toFixed(2)}`);
        
        // Look for text containing "Shipping Fee" and update the next element
        const allElements = document.querySelectorAll('*');
        let updated = false;
        
        allElements.forEach(element => {
            if (element.textContent && element.textContent.includes('Shipping Fee')) {
                // Look for the next sibling or parent's next sibling
                let targetElement = element.nextElementSibling;
                if (!targetElement) {
                    targetElement = element.parentElement.nextElementSibling;
                }
                if (targetElement && (targetElement.textContent.includes('P-') || targetElement.textContent.includes('P0') || targetElement.textContent.trim() === '')) {
                    targetElement.textContent = `P${shippingFee.toFixed(2)}`;
                    console.log('Updated shipping fee to:', `P${shippingFee.toFixed(2)}`);
                    updated = true;
                }
            }
        });
        
        // Also try to find elements with P- or P0 and update them
        if (!updated) {
            allElements.forEach(element => {
                if (element.textContent && (element.textContent.includes('P-') || element.textContent.includes('P0'))) {
                    element.textContent = `P${shippingFee.toFixed(2)}`;
                    console.log('Updated shipping fee element to:', `P${shippingFee.toFixed(2)}`);
                    updated = true;
                }
            });
        }
        
        // If still not found, try to find by looking for empty shipping fee
        if (!updated) {
            allElements.forEach(element => {
                if (element.textContent && element.textContent.includes('Shipping Fee') && element.textContent.includes('P-')) {
                    element.textContent = element.textContent.replace('P-', `P${shippingFee.toFixed(2)}`);
                    console.log('Updated shipping fee in text to:', `P${shippingFee.toFixed(2)}`);
                    updated = true;
                }
            });
        }
    }
    
    // Update order total
    function updateOrderTotal(shippingFee) {
        console.log('Updating order total with shipping fee:', shippingFee);
        
        // Find the subtotal and total elements more specifically
        const allElements = document.querySelectorAll('*');
        let subtotal = 0;
        let totalElement = null;
        
        allElements.forEach(element => {
            if (element.textContent && element.textContent.includes('Subtotal') && element.textContent.includes('P')) {
                // Extract the subtotal amount
                const match = element.textContent.match(/P([\d,]+\.?\d*)/);
                if (match) {
                    subtotal = parseFloat(match[1].replace(',', ''));
                    console.log('Found subtotal:', subtotal);
                }
            }
            if (element.textContent && element.textContent.includes('Total') && element.textContent.includes('P') && !element.textContent.includes('Subtotal')) {
                totalElement = element;
                console.log('Found total element:', element.textContent);
            }
        });
        
        if (subtotal > 0 && totalElement) {
            const newTotal = subtotal + shippingFee;
            totalElement.textContent = `Total P${newTotal.toFixed(2)}`;
            console.log('Updated total to:', `P${newTotal.toFixed(2)}`);
        } else {
            console.log('Could not find subtotal or total element');
            console.log('Subtotal found:', subtotal);
            console.log('Total element found:', totalElement);
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
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/components/delivery-map.blade.php ENDPATH**/ ?>