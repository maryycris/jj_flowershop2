
<div class="delivery-map-container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Delivery Location & Distance
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="toggleMapBtn">
                        <i class="fas fa-map me-1"></i>
                        <span id="toggleMapText">Show Map</span>
                    </button>
                </div>
                <div class="card-body">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Delivery Address</label>
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   id="deliveryAddressInput" 
                                   placeholder="Enter complete delivery address..."
                                   value="<?php echo e($selectedAddress ?? ''); ?>">
                            <button class="btn btn-outline-success" type="button" id="geocodeBtn">
                                <i class="fas fa-search me-1"></i>
                                Find
                            </button>
                        </div>
                        <small class="text-muted">Enter the complete address for accurate distance calculation</small>
                    </div>

                    
                    <div id="deliveryMap" style="height: 400px; display: none; border-radius: 8px; border: 1px solid #dee2e6;"></div>

                    
                    <div id="deliveryInfo" class="mt-3" style="display: none;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <i class="fas fa-route text-primary mb-2" style="font-size: 1.5rem;"></i>
                                    <div class="fw-semibold">Distance</div>
                                    <div id="distanceDisplay" class="text-muted">-</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <i class="fas fa-clock text-warning mb-2" style="font-size: 1.5rem;"></i>
                                    <div class="fw-semibold">Duration</div>
                                    <div id="durationDisplay" class="text-muted">-</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <i class="fas fa-truck text-success mb-2" style="font-size: 1.5rem;"></i>
                                    <div class="fw-semibold">Shipping Fee</div>
                                    <div id="shippingFeeInMap" class="fw-bold text-success">₱0.00</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div id="mapLoading" class="text-center mt-3" style="display: none;">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mt-2 text-muted">Calculating distance and shipping fee...</div>
                    </div>

                    
                </div>
            </div>
        </div>
    </div>
</div>


<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
.delivery-map-container .card {
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

#deliveryMap {
    position: relative;
    z-index: 1;
}

.leaflet-popup-content {
    font-size: 14px;
}

.leaflet-popup-content h6 {
    margin-bottom: 8px;
    color: #333;
}

.route-info {
    font-size: 12px;
    color: #666;
}

.shop-marker {
    background-color: #28a745;
    border: 2px solid #fff;
    border-radius: 50%;
    width: 20px;
    height: 20px;
}

.delivery-marker {
    background-color: #dc3545;
    border: 2px solid #fff;
    border-radius: 50%;
    width: 20px;
    height: 20px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Shop coordinates (J & J Flower Shop - Cordova, Cebu)
    const shopCoords = {
        // Bangbang, Cordova, Cebu (approximate center along main road)
        lat: 10.2503,
        lng: 123.9488
    };

    let map = null;
    let routeLayer = null;
    let shopMarker = null;
    let deliveryMarker = null;
    let isMapVisible = false;
    // Serviceable areas - expanded to include puroks, barangays, and streets
    const serviceableAreas = [
        // Cordova areas
        'cordova', 'bang-bang', 'poblacion', 'catarman', 'gabi', 'pilipog', 'day-as', 'buagsong', 'san miguel',
        
        // Cebu City areas
        'cebu city', 'downtown', 'colon', 'ayala', 'it park', 'as fortuna', 'banilad', 'lahug', 'capitol', 
        'jones', 'fuente', 'basak', 'mabolo', 'kalubihan', 'sambag', 'tejero', 't. padilla', 'carreta',
        'ermita', 'san nicolas', 'parian', 'sto. niño', 'san roque', 'sawang calero', 'suba', 'pasil',
        'tisa', 'labangon', 'punta princesa', 'guadalupe', 'kalunasan', 'busay', 'adlaon', 'sirao',
        'pamutan', 'budlaan', 'tabunan', 'pung-ol', 'sapangdaku', 'talamban', 'pit-os', 'banilad',
        'apas', 'luz', 'cambaro', 'hipodromo', 'camputhaw', 'cogon ramos', 'cogon pardo', 'bulacao',
        'inayawan', 'poblacion pardo', 'quiot', 'kinasang-an', 'san jose', 'basak pardo', 'mambaling',
        'punta', 'sawang calero', 'suba', 'pasil', 'tisa', 'labangon', 'punta princesa', 'guadalupe',
        
        // Lapu-Lapu City areas
        'lapu-lapu', 'mactan', 'basak', 'poblacion', 'agus', 'babag', 'buaya', 'calawisan', 'canjulao',
        'gun-ob', 'ibabao', 'looc', 'maribago', 'marigondon', 'pajac', 'pajo', 'poblacion', 'punta engano',
        'pusok', 'subabasbas', 'tigbao', 'tungasan', 'ibabao', 'buaya', 'calawisan', 'canjulao',
        'gun-ob', 'looc', 'maribago', 'marigondon', 'pajac', 'pajo', 'punta engano', 'pusok',
        'subabasbas', 'tigbao', 'tungasan',
        
        // Mandaue City areas
        'mandaue', 'basak', 'banilad', 'canduman', 'casili', 'casuntingan', 'centro', 'cubacub',
        'guizo', 'ibabao', 'jagobiao', 'labogon', 'looc', 'maguikay', 'mantuyong', 'paknaan',
        'pagsabungan', 'subangdaku', 'tabok', 'tawason', 'tingub', 'tipolo', 'ubajo', 'umapad',
        
        // Talisay City areas
        'talisay', 'biasong', 'bulacao', 'cansojong', 'camp 4', 'candulawan', 'carmen', 'dumlog',
        'jaclupan', 'lagtang', 'lawaan', 'linao', 'maghaway', 'manunggal', 'mohon', 'poblacion',
        'pooc', 'san isidro', 'san roque', 'santander', 'tangke', 'tapul', 'tinaan', 'tomog',
        
        // Consolacion areas
        'consolacion', 'cabuyao', 'canduman', 'casili', 'garing', 'jagobiao', 'poblacion', 'pitogo',
        'polo', 'pulangbato', 'tayud', 'tilhaong', 'tugbongan', 'panoypoy', 'poblacion', 'pitogo',
        'polo', 'pulangbato', 'tayud', 'tilhaong', 'tugbongan', 'panoypoy'
    ];

    function isServiceableAddress(address) {
        const normalized = (address || '').toLowerCase();
        return serviceableAreas.some(area => normalized.includes(area));
    }

    function showOutOfAreaAlert() {
        const msg = 'Sorry, we are unable to cater delivery to your selected location at this time. Please choose a different address within our service area.';
        if (window.Swal && typeof window.Swal.fire === 'function') {
            Swal.fire({ icon: 'warning', title: 'Out of delivery area', text: msg });
        } else {
            alert(msg);
        }
    }

    // Initialize map
    function initMap() {
        if (map) return;

        map = L.map('deliveryMap').setView([shopCoords.lat, shopCoords.lng], 12);
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Add shop marker
        shopMarker = L.marker([shopCoords.lat, shopCoords.lng], {
            icon: L.divIcon({
                className: 'shop-marker',
                html: '<i class="fas fa-store" style="color: white; font-size: 10px; line-height: 16px; text-align: center; width: 16px;"></i>',
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            })
        }).addTo(map);

        shopMarker.bindPopup(`
            <h6><i class="fas fa-store me-1"></i>J & J Flower Shop</h6>
            <div class="route-info">
                <div><strong>Address:</strong> Bang-bang Cordova, Cebu</div>
                <div><strong>Phone:</strong> 09674184857</div>
            </div>
        `).openPopup();
    }

    // Toggle map visibility
    document.getElementById('toggleMapBtn').addEventListener('click', function() {
        const mapContainer = document.getElementById('deliveryMap');
        const toggleText = document.getElementById('toggleMapText');
        
        if (isMapVisible) {
            mapContainer.style.display = 'none';
            toggleText.textContent = 'Show Map';
            isMapVisible = false;
        } else {
            mapContainer.style.display = 'block';
            toggleText.textContent = 'Hide Map';
            isMapVisible = true;
            
            // Initialize map if not already done
            setTimeout(() => {
                initMap();
            }, 100);
        }
    });

    // Geocode address and calculate route
    document.getElementById('geocodeBtn').addEventListener('click', function() {
        const address = document.getElementById('deliveryAddressInput').value.trim();
        if (!address) {
            alert('Please enter a delivery address');
            return;
        }
        if (!isServiceableAddress(address)) {
            showOutOfAreaAlert();
            return;
        }
        calculateRoute(address);
    });

    // Do not auto-calculate; only calculate when user clicks Find
    let geocodeTimeout;
    document.getElementById('deliveryAddressInput').addEventListener('input', function() {
        // Intentionally no auto-calc to avoid adding fee before user confirms
    });

    // Calculate route and shipping fee
    async function calculateRoute(address) {
        const loadingDiv = document.getElementById('mapLoading');
        const infoDiv = document.getElementById('deliveryInfo');
        
        loadingDiv.style.display = 'block';
        infoDiv.style.display = 'none';

        try {
            // Use fallback calculation for now
            const result = calculateFallbackDistance(address);
            
            if (result) {
                // Update UI with results
                updateDeliveryInfo(result, address);
                
                // Update map if visible
                if (isMapVisible) {
                    updateMap(result.coordinates, null);
                }
            } else {
                throw new Error('Unable to calculate distance for this address');
            }

        } catch (error) {
            console.error('Route calculation error:', error);
            
            // More user-friendly error message
            let errorMessage = 'Error calculating route. ';
            if (error.message.includes('Unexpected token')) {
                errorMessage += 'The server returned an unexpected response. Please try again.';
            } else if (error.message.includes('Failed to fetch')) {
                errorMessage += 'Unable to connect to the server. Please check your internet connection.';
            } else {
                errorMessage += error.message;
            }
            
            alert(errorMessage);
        } finally {
            loadingDiv.style.display = 'none';
        }
    }

    // Enhanced distance calculation with specific area coordinates
    function calculateFallbackDistance(address) {
        const normalized = address.toLowerCase();
        
        // More detailed area data with specific coordinates and distances
        const areaData = {
            // Cordova areas (0km - within service area)
            'cordova': { lat: 10.3157, lng: 123.8854, distance: 0 },
            'bang-bang': { lat: 10.3157, lng: 123.8854, distance: 0 },
            'poblacion': { lat: 10.3157, lng: 123.8854, distance: 0 },
            'catarman': { lat: 10.3157, lng: 123.8854, distance: 0 },
            'gabi': { lat: 10.3157, lng: 123.8854, distance: 0 },
            'pilipog': { lat: 10.3157, lng: 123.8854, distance: 0 },
            'day-as': { lat: 10.3157, lng: 123.8854, distance: 0 },
            'buagsong': { lat: 10.3157, lng: 123.8854, distance: 0 },
            'san miguel': { lat: 10.3157, lng: 123.8854, distance: 0 },
            
            // Lapu-Lapu City areas (8-15km from Cordova)
            'lapu-lapu': { lat: 10.3103, lng: 123.9494, distance: 12 },
            'mactan': { lat: 10.3103, lng: 123.9494, distance: 12 },
            'basak': { lat: 10.3103, lng: 123.9494, distance: 12 },
            'poblacion': { lat: 10.3103, lng: 123.9494, distance: 12 },
            'agus': { lat: 10.3103, lng: 123.9494, distance: 12 },
            'babag': { lat: 10.3103, lng: 123.9494, distance: 12 },
            'buaya': { lat: 10.3103, lng: 123.9494, distance: 12 },
            'calawisan': { lat: 10.3103, lng: 123.9494, distance: 12 },
            'canjulao': { lat: 10.3103, lng: 123.9494, distance: 12 },
            'gun-ob': { lat: 10.3103, lng: 123.9494, distance: 12 },
            'ibabao': { lat: 10.3103, lng: 123.9494, distance: 12 },
            'looc': { lat: 10.3103, lng: 123.9494, distance: 12 },
            'maribago': { lat: 10.3103, lng: 123.9494, distance: 12 },
            'marigondon': { lat: 10.3103, lng: 123.9494, distance: 12 },
            'pajac': { lat: 10.3103, lng: 123.9494, distance: 12 },
            'pajo': { lat: 10.3103, lng: 123.9494, distance: 12 },
            'punta engano': { lat: 10.3103, lng: 123.9494, distance: 12 },
            'pusok': { lat: 10.3103, lng: 123.9494, distance: 12 },
            'subabasbas': { lat: 10.3103, lng: 123.9494, distance: 12 },
            'tigbao': { lat: 10.3103, lng: 123.9494, distance: 12 },
            'tungasan': { lat: 10.3103, lng: 123.9494, distance: 12 },
            
            // Mandaue City areas (15-25km from Cordova)
            'mandaue': { lat: 10.3236, lng: 123.9221, distance: 20 },
            'canduman': { lat: 10.3236, lng: 123.9221, distance: 20 },
            'casili': { lat: 10.3236, lng: 123.9221, distance: 20 },
            'casuntingan': { lat: 10.3236, lng: 123.9221, distance: 20 },
            'centro': { lat: 10.3236, lng: 123.9221, distance: 20 },
            'cubacub': { lat: 10.3236, lng: 123.9221, distance: 20 },
            'guizo': { lat: 10.3236, lng: 123.9221, distance: 20 },
            'jagobiao': { lat: 10.3236, lng: 123.9221, distance: 20 },
            'labogon': { lat: 10.3236, lng: 123.9221, distance: 20 },
            'maguikay': { lat: 10.3236, lng: 123.9221, distance: 20 },
            'mantuyong': { lat: 10.3236, lng: 123.9221, distance: 20 },
            'paknaan': { lat: 10.3236, lng: 123.9221, distance: 20 },
            'pagsabungan': { lat: 10.3236, lng: 123.9221, distance: 20 },
            'subangdaku': { lat: 10.3236, lng: 123.9221, distance: 20 },
            'tabok': { lat: 10.3236, lng: 123.9221, distance: 20 },
            'tawason': { lat: 10.3236, lng: 123.9221, distance: 20 },
            'tingub': { lat: 10.3236, lng: 123.9221, distance: 20 },
            'tipolo': { lat: 10.3236, lng: 123.9221, distance: 20 },
            'ubajo': { lat: 10.3236, lng: 123.9221, distance: 20 },
            'umapad': { lat: 10.3236, lng: 123.9221, distance: 20 },
            
            // Cebu City areas (20-30km from Cordova)
            'cebu city': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'downtown': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'colon': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'ayala': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'it park': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'as fortuna': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'banilad': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'lahug': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'capitol': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'jones': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'fuente': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'basak': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'mabolo': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'kalubihan': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'sambag': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'tejero': { lat: 10.3157, lng: 123.8854, distance: 25 },
            't. padilla': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'carreta': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'ermita': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'san nicolas': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'parian': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'sto. niño': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'san roque': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'sawang calero': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'suba': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'pasil': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'tisa': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'labangon': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'punta princesa': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'guadalupe': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'kalunasan': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'busay': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'adlaon': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'sirao': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'pamutan': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'budlaan': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'tabunan': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'pung-ol': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'sapangdaku': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'talamban': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'pit-os': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'apas': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'luz': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'cambaro': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'hipodromo': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'camputhaw': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'cogon ramos': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'cogon pardo': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'bulacao': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'inayawan': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'poblacion pardo': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'quiot': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'kinasang-an': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'san jose': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'basak pardo': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'mambaling': { lat: 10.3157, lng: 123.8854, distance: 25 },
            'punta': { lat: 10.3157, lng: 123.8854, distance: 25 },
            
            // Talisay City areas (25-35km from Cordova)
            'talisay': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'biasong': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'bulacao': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'cansojong': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'camp 4': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'candulawan': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'carmen': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'dumlog': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'jaclupan': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'lagtang': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'lawaan': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'linao': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'maghaway': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'manunggal': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'mohon': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'pooc': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'san isidro': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'san roque': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'santander': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'tangke': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'tapul': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'tinaan': { lat: 10.2447, lng: 123.9633, distance: 30 },
            'tomog': { lat: 10.2447, lng: 123.9633, distance: 30 },
            
            // Consolacion areas (18-25km from Cordova)
            'consolacion': { lat: 10.3766, lng: 123.9573, distance: 22 },
            'cabuyao': { lat: 10.3766, lng: 123.9573, distance: 22 },
            'garing': { lat: 10.3766, lng: 123.9573, distance: 22 },
            'pitogo': { lat: 10.3766, lng: 123.9573, distance: 22 },
            'polo': { lat: 10.3766, lng: 123.9573, distance: 22 },
            'pulangbato': { lat: 10.3766, lng: 123.9573, distance: 22 },
            'tayud': { lat: 10.3766, lng: 123.9573, distance: 22 },
            'tilhaong': { lat: 10.3766, lng: 123.9573, distance: 22 },
            'tugbongan': { lat: 10.3766, lng: 123.9573, distance: 22 },
            'panoypoy': { lat: 10.3766, lng: 123.9573, distance: 22 }
        };
        
        // Find matching area
        for (const [area, data] of Object.entries(areaData)) {
            if (normalized.includes(area)) {
                const baseFee = 30;
                const additionalRatePerKm = 5; // ₱5 per km outside Cordova
                let shippingFee = baseFee;
                
                if (data.distance > 0) {
                    // ₱5 every 2 km (rounded up)
                    const blocks = Math.ceil(data.distance / 2);
                    shippingFee += blocks * additionalRatePerKm;
                }
                
                return {
                    coordinates: { lat: data.lat, lng: data.lng ?? data.lon },
                    distance_km: data.distance,
                    duration_minutes: Math.round(data.distance * 2), // Approximate 2 minutes per km
                    shipping_fee: shippingFee,
                    geometry: null
                };
            }
        }
        
        // Default fallback for unknown addresses within service area
        const defaultDistance = 20;
        const defaultBlocks = Math.ceil(defaultDistance / 2);
        return {
            coordinates: { lat: 10.3157, lng: 123.8854 },
            distance_km: defaultDistance,
            duration_minutes: 40,
            shipping_fee: 30 + (defaultBlocks * 5), // Base + 20km in 2km blocks * ₱5
            geometry: null
        };
    }

    // Update delivery information display
    function updateDeliveryInfo(routeData, addressName) {
        document.getElementById('distanceDisplay').textContent = routeData.distance_km + ' km';
        document.getElementById('durationDisplay').textContent = routeData.duration_minutes + ' min';
        document.getElementById('shippingFeeInMap').textContent = '₱' + routeData.shipping_fee.toFixed(2);
        
        document.getElementById('deliveryInfo').style.display = 'block';

        // Update hidden shipping fee input for form submission
        const shippingFeeInput = document.querySelector('input[name="shipping_fee"]');
        if (shippingFeeInput) {
            shippingFeeInput.value = routeData.shipping_fee;
        }

        // Update the checkout page's shipping fee display
        const checkoutShippingDisplay = document.getElementById('shippingFeeDisplay');
        if (checkoutShippingDisplay) {
            console.log('Updating checkout shipping display to:', routeData.shipping_fee);
            checkoutShippingDisplay.textContent = routeData.shipping_fee.toFixed(2);
        } else {
            console.log('shippingFeeDisplay element not found!');
            // Try alternative selectors
            const altDisplay = document.querySelector('span[id="shippingFeeDisplay"]');
            if (altDisplay) {
                console.log('Found alternative shipping display element');
                altDisplay.textContent = routeData.shipping_fee.toFixed(2);
            }
        }

        // Update total calculation using global function
        if (typeof updateShippingFeeDisplay === 'function') {
            console.log('Calling updateShippingFeeDisplay with:', routeData.shipping_fee);
            updateShippingFeeDisplay(routeData.shipping_fee);
        } else {
            console.log('updateShippingFeeDisplay function not found, using updateTotalPrice');
            updateTotalPrice(routeData.shipping_fee);
        }
    }

    // Update map with route
    function updateMap(destCoords, routeGeometry) {
        if (!map) return;

        // Remove existing route and delivery marker
        if (routeLayer) {
            map.removeLayer(routeLayer);
        }
        if (deliveryMarker) {
            map.removeLayer(deliveryMarker);
        }

        // Add delivery marker
        deliveryMarker = L.marker([destCoords.lat, destCoords.lng], {
            icon: L.divIcon({
                className: 'delivery-marker',
                html: '<i class="fas fa-map-marker-alt" style="color: white; font-size: 10px; line-height: 16px; text-align: center; width: 16px;"></i>',
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            })
        }).addTo(map);

        deliveryMarker.bindPopup(`
            <h6><i class="fas fa-map-marker-alt me-1"></i>Delivery Address</h6>
            <div class="route-info">${addressName || 'Selected address'}</div>
        `);

        // Add route line if available
        if (routeGeometry && routeGeometry.coordinates) {
            const routeLine = L.polyline(routeGeometry.coordinates, {
                color: '#007bff',
                weight: 4,
                opacity: 0.8
            }).addTo(map);

            routeLayer = routeLine;

            // Fit map to show both markers and route
            const group = new L.featureGroup([shopMarker, deliveryMarker, routeLine]);
            map.fitBounds(group.getBounds().pad(0.1));
        } else {
            // Just show both markers without route line
            const group = new L.featureGroup([shopMarker, deliveryMarker]);
            map.fitBounds(group.getBounds().pad(0.1));
        }
    }

    // Update total price calculation
    function updateTotalPrice(shippingFee) {
        const subtotalElement = document.getElementById('cartSubtotal');
        const totalElement = document.getElementById('cartTotalFinal');
        
        if (subtotalElement && totalElement) {
            const subtotal = parseFloat(subtotalElement.textContent.replace(/,/g, ''));
            const total = subtotal + shippingFee;
            totalElement.textContent = total.toFixed(2);
        }
    }

    // Disable all auto-initialization to prevent pre-filling shipping fee
    const savedAddress = document.getElementById('deliveryAddressInput').value;
    const addressSelect = document.getElementById('addressSelect');
    // No auto-calc here by design
});

// Debug helpers removed
</script>
<?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/components/delivery-map.blade.php ENDPATH**/ ?>