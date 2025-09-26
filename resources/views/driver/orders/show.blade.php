@extends('layouts.driver_mobile')

@section('content')
<div class="d-flex align-items-center mb-3">
    <a href="{{ route('driver.orders.index') }}" class="btn btn-outline-secondary me-2">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="fw-bold mb-0">Order #{{ $delivery->order->id }}</h4>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-map me-2"></i>Delivery Map & Route</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12">
                <div id="driverDeliveryMap" style="height: 300px; width: 100%; border-radius: 8px; overflow: hidden; background: #f8f9fa;"></div>
            </div>
            <div class="col-12 d-flex justify-content-between">
                <div>
                    <small class="text-muted">Distance</small><br>
                    <strong id="driverDistanceDisplay">Calculating…</strong>
                </div>
                <div>
                    <small class="text-muted">ETA</small><br>
                    <strong id="driverEtaDisplay">—</strong>
                </div>
            </div>
        </div>
    </div>
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
    (function() {
        const shopCoords = { lat: 10.3157, lng: 123.8854 }; // Bangbang, Cordova
        const address = @json($delivery->delivery_address ?? '');

        // Simple area-based fallback (same assumptions as checkout map)
        function fallbackMatch(addressText) {
            const normalized = (addressText || '').toLowerCase();
            const areaData = {
                // Cordova (0km)
                'cordova': { lat: 10.3157, lng: 123.8854, distance: 0 },
                'bang-bang': { lat: 10.3157, lng: 123.8854, distance: 0 },
                'poblacion': { lat: 10.3157, lng: 123.8854, distance: 0 },
                'catarman': { lat: 10.3157, lng: 123.8854, distance: 0 },
                'gabi': { lat: 10.3157, lng: 123.8854, distance: 0 },
                'pilipog': { lat: 10.3157, lng: 123.8854, distance: 0 },
                'day-as': { lat: 10.3157, lng: 123.8854, distance: 0 },
                'buagsong': { lat: 10.3157, lng: 123.8854, distance: 0 },
                'san miguel': { lat: 10.3157, lng: 123.8854, distance: 0 },

                // Lapu-Lapu (12km approx)
                'lapu-lapu': { lat: 10.3103, lng: 123.9494, distance: 12 },
                'mactan': { lat: 10.3103, lng: 123.9494, distance: 12 },
                'basak': { lat: 10.3103, lng: 123.9494, distance: 12 },
                'agus': { lat: 10.3103, lng: 123.9494, distance: 12 },
                'maribago': { lat: 10.3103, lng: 123.9494, distance: 12 },
                'marigondon': { lat: 10.3103, lng: 123.9494, distance: 12 },
                'pajac': { lat: 10.3103, lng: 123.9494, distance: 12 },
                'pajo': { lat: 10.3103, lng: 123.9494, distance: 12 },
                'pusok': { lat: 10.3103, lng: 123.9494, distance: 12 },

                // Mandaue (20km approx)
                'mandaue': { lat: 10.3236, lng: 123.9221, distance: 20 },
                'subangdaku': { lat: 10.3236, lng: 123.9221, distance: 20 },
                'tipolo': { lat: 10.3236, lng: 123.9221, distance: 20 },

                // Cebu City (25km approx)
                'cebu city': { lat: 10.3157, lng: 123.8854, distance: 25 },
                'lahug': { lat: 10.3320, lng: 123.8980, distance: 25 },
                'it park': { lat: 10.3290, lng: 123.9050, distance: 25 },

                // Talisay (30km approx)
                'talisay': { lat: 10.2447, lng: 123.9633, distance: 30 },

                // Consolacion (22km approx)
                'consolacion': { lat: 10.3766, lng: 123.9573, distance: 22 }
            };
            for (const [key, data] of Object.entries(areaData)) {
                if (normalized.includes(key)) return data;
            }
            return { lat: 10.3157, lng: 123.8854, distance: 20 }; // default
        }

        function initDriverMap() {
            const mapEl = document.getElementById('driverDeliveryMap');
            if (!mapEl) return;

            const map = L.map('driverDeliveryMap');
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const dest = fallbackMatch(address);

            // Markers
            const shopMarker = L.marker([shopCoords.lat, shopCoords.lng]).addTo(map);
            shopMarker.bindPopup('J & J Flower Shop').openPopup();

            const destMarker = L.marker([dest.lat, dest.lng]).addTo(map);
            destMarker.bindPopup('Delivery Destination');

            // Fit bounds
            const bounds = L.latLngBounds([
                [shopCoords.lat, shopCoords.lng],
                [dest.lat, dest.lng]
            ]);
            map.fitBounds(bounds, { padding: [20, 20] });

            // Draw straight polyline as visual (fallback; routing server optional)
            L.polyline([
                [shopCoords.lat, shopCoords.lng],
                [dest.lat, dest.lng]
            ], { color: '#198754', weight: 4, opacity: 0.8 }).addTo(map);

            // Display distance and ETA (2 mins/km approx)
            const distanceKm = dest.distance;
            const etaMinutes = Math.round(distanceKm * 2);
            document.getElementById('driverDistanceDisplay').textContent = distanceKm + ' km';
            document.getElementById('driverEtaDisplay').textContent = etaMinutes + ' min';
        }

        // Initialize on load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initDriverMap);
        } else {
            initDriverMap();
        }
    })();
    </script>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Order Information</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-6">
                <small class="text-muted">Order Date:</small><br>
                <strong>{{ $delivery->order->created_at->format('M d, Y g:i A') }}</strong>
            </div>
            <div class="col-6">
                <small class="text-muted">Order Status:</small><br>
                <span class="badge bg-{{ $delivery->order->status === 'completed' ? 'success' : ($delivery->order->status === 'processing' ? 'warning' : 'secondary') }}">
                    {{ ucfirst($delivery->order->status) }}
                </span>
            </div>
        </div>
        
        <div class="mb-3">
            <small class="text-muted">Order Type:</small><br>
            <strong>{{ ucfirst($delivery->order->type ?? 'Standard') }}</strong>
        </div>
        
        <div class="mb-3">
            <small class="text-muted">Total Amount:</small><br>
            <strong class="text-success">₱{{ number_format($delivery->order->total_amount, 2) }}</strong>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-person me-2"></i>Customer Information</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-6">
                <small class="text-muted">Name:</small><br>
                <strong>{{ $delivery->order->user->name }}</strong>
            </div>
            <div class="col-6">
                <small class="text-muted">Contact:</small><br>
                <strong>{{ $delivery->order->user->contact_number }}</strong>
            </div>
        </div>
        
        <div class="mb-3">
            <small class="text-muted">Email:</small><br>
            <strong>{{ $delivery->order->user->email }}</strong>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Delivery Information</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-6">
                <small class="text-muted">Delivery Date:</small><br>
                <strong>{{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}</strong>
            </div>
            <div class="col-6">
                <small class="text-muted">Delivery Time:</small><br>
                <strong>{{ $delivery->delivery_time ?? 'Not specified' }}</strong>
            </div>
        </div>
        
        <div class="mb-3">
            <small class="text-muted">Delivery Address:</small><br>
            <strong>{{ $delivery->delivery_address ?? 'Address not specified' }}</strong>
        </div>
        @if($delivery->order && $delivery->order->address)
            @if($delivery->order->address->landmark)
                <div class="mb-2">
                    <strong>Landmark:</strong> {{ $delivery->order->address->landmark }}
                </div>
            @endif
            @if($delivery->order->address->special_instructions)
                <div class="mb-2">
                    <strong>Special Instructions:</strong> {{ $delivery->order->address->special_instructions }}
                </div>
            @endif
        @endif
        
        <div class="row mb-3">
            <div class="col-6">
                <small class="text-muted">Recipient Name:</small><br>
                <strong>{{ $delivery->recipient_name ?? 'Same as customer' }}</strong>
            </div>
            <div class="col-6">
                <small class="text-muted">Recipient Phone:</small><br>
                <strong>{{ $delivery->recipient_phone ?? 'Same as customer' }}</strong>
            </div>
        </div>
        
        <div class="mb-3">
            <small class="text-muted">Current Status:</small><br>
            <span class="badge bg-{{ $delivery->status === 'completed' ? 'success' : ($delivery->status === 'in_progress' ? 'warning' : 'secondary') }} fs-6">
                {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
            </span>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-box me-2"></i>Order Items</h5>
    </div>
    <div class="card-body">
        @if($delivery->order->products)
            @foreach($delivery->order->products as $product)
            <div class="d-flex align-items-center mb-2">
                <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/default-product.png') }}" 
                     alt="{{ $product->name }}" 
                     class="rounded me-2" 
                     style="width: 50px; height: 50px; object-fit: cover;">
                <div class="flex-fill">
                    <strong>{{ $product->name }}</strong><br>
                    <small class="text-muted">Quantity: {{ $product->pivot->quantity ?? 1 }}</small>
                </div>
                <div class="text-end">
                    <strong>₱{{ number_format($product->price, 2) }}</strong>
                </div>
            </div>
            @endforeach
        @else
            <p class="text-muted mb-0">No product details available</p>
        @endif
    </div>
</div>

@if($delivery->status !== 'completed')
<div class="card shadow-sm mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Update Status</h5>
    </div>
    <div class="card-body">
        <div class="d-flex gap-2">
            @if($delivery->status === 'pending')
            <button class="btn btn-warning flex-fill" onclick="updateStatus('in_progress')">
                <i class="bi bi-play me-1"></i>Start Delivery
            </button>
            @elseif($delivery->status === 'in_progress')
            <button class="btn btn-success flex-fill" onclick="updateStatus('completed')">
                <i class="bi bi-check-circle me-1"></i>Mark as Completed
            </button>
            @endif
        </div>
    </div>
</div>
@endif

<script>
function updateStatus(status) {
    if (confirm('Are you sure you want to update this delivery status?')) {
        fetch(`/driver/deliveries/{{ $delivery->id }}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating status: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error updating status');
        });
    }
}
</script>
@endsection 