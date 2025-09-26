<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Map Demo - J & J Flower Shop</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body style="background: #f4faf4;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-4">
                    <h2 class="text-success">
                        <i class="fas fa-map-marked-alt me-2"></i>
                        Delivery Map Demo
                    </h2>
                    <p class="text-muted">Test the new Leaflet.js + OSM + OSRM delivery distance calculation</p>
                </div>

                {{-- Include the delivery map component --}}
                <x-delivery-map />

                <div class="mt-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                How it works
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-center p-3">
                                        <i class="fas fa-search text-primary mb-2" style="font-size: 2rem;"></i>
                                        <h6>1. Geocoding</h6>
                                        <p class="text-muted small">Uses Nominatim (OpenStreetMap) to convert addresses to coordinates</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3">
                                        <i class="fas fa-route text-warning mb-2" style="font-size: 2rem;"></i>
                                        <h6>2. Routing</h6>
                                        <p class="text-muted small">Uses OSRM API to calculate driving distance and route</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3">
                                        <i class="fas fa-calculator text-success mb-2" style="font-size: 2rem;"></i>
                                        <h6>3. Pricing</h6>
                                        <p class="text-muted small">Automatically calculates shipping fee based on distance</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-code me-2"></i>
                                Technical Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i><strong>100% Free:</strong> No API keys required, no usage limits</li>
                                <li><i class="fas fa-check text-success me-2"></i><strong>OpenStreetMap:</strong> Community-driven map data</li>
                                <li><i class="fas fa-check text-success me-2"></i><strong>OSRM:</strong> Open Source Routing Machine for accurate driving distances</li>
                                <li><i class="fas fa-check text-success me-2"></i><strong>Leaflet.js:</strong> Lightweight, mobile-friendly map library</li>
                                <li><i class="fas fa-check text-success me-2"></i><strong>Real-time:</strong> Instant distance calculation and fee updates</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('customer.login') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Try in Checkout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
