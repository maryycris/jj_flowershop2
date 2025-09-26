# Delivery Map Implementation - J & J Flower Shop

## Overview
This implementation replaces Google Maps API with free alternatives (Leaflet.js + OpenStreetMap + OSRM) for delivery distance calculation and shipping fee computation.

## Features
- ✅ **100% Free** - No API keys or usage limits
- ✅ **Real-time distance calculation** using OSRM (Open Source Routing Machine)
- ✅ **Interactive map** with Leaflet.js and OpenStreetMap
- ✅ **Automatic shipping fee calculation** based on driving distance
- ✅ **Address geocoding** using Nominatim (OpenStreetMap)
- ✅ **Visual route display** on the map
- ✅ **Mobile-friendly** responsive design

## Technical Stack

### Frontend
- **Leaflet.js** - Lightweight map library
- **OpenStreetMap** - Free map tiles
- **Bootstrap 5** - UI framework
- **Font Awesome** - Icons

### Backend
- **OSRM API** - Open Source Routing Machine for distance calculation
- **Nominatim API** - OpenStreetMap geocoding service
- **Laravel** - PHP framework

## Files Created/Modified

### New Files
1. `app/Http/Controllers/MapController.php` - API endpoints for map functionality
2. `resources/views/components/delivery-map.blade.php` - Reusable map component
3. `resources/views/map-demo.blade.php` - Demo page for testing
4. `DELIVERY_MAP_IMPLEMENTATION.md` - This documentation

### Modified Files
1. `app/Helpers/ShippingFeeHelper.php` - Updated to use OSRM instead of Google Maps
2. `routes/api.php` - Added new API endpoints
3. `routes/web.php` - Added demo route
4. `resources/views/customer/checkout/index.blade.php` - Integrated map component

## API Endpoints

### New Endpoints
- `POST /api/map/geocode` - Convert address to coordinates
- `POST /api/map/route` - Get route between two points
- `POST /api/map/shipping-calculate` - Calculate shipping fee with distance

### Existing Endpoints (Updated)
- `POST /api/calculate-shipping-fee` - Now uses OSRM instead of Google Maps

## Usage

### 1. Basic Integration
```php
// Include the map component in any Blade template
<x-delivery-map :selectedAddress="'Your address here'" />
```

### 2. API Usage
```javascript
// Geocode an address
const response = await fetch('/api/map/geocode', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({ address: 'Cebu City, Philippines' })
});

const data = await response.json();
console.log(data.coordinates); // { lat: 10.3157, lon: 123.8854 }
```

### 3. Calculate Shipping Fee
```javascript
// Calculate shipping fee with distance
const response = await fetch('/api/map/shipping-calculate', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        origin_lat: 10.3157,
        origin_lon: 123.8854,
        dest_lat: 10.3236,
        dest_lon: 123.9221
    })
});

const data = await response.json();
console.log(data.shipping_fee); // 42.00
```

## Configuration

### Shop Coordinates
The shop coordinates are set in the map component:
```javascript
const shopCoords = {
    lat: 10.3157,  // J & J Flower Shop - Cordova, Cebu
    lng: 123.8854
};
```

### Shipping Fee Formula
```php
$baseFee = 30; // Base fee for local delivery
$additionalRatePerKm = 12; // Rate per km outside base area

if ($distanceKm > 2) {
    $shippingFee = $baseFee + (($distanceKm - 2) * $additionalRatePerKm);
} else {
    $shippingFee = $baseFee;
}
```

## Testing

### Demo Page
Visit `/map-demo` to test the map functionality:
- Enter any address in the Philippines
- Click "Find" to geocode and calculate distance
- Click "Show Map" to see the interactive map
- View real-time shipping fee calculation

### Test Addresses
- "Mandaue City, Cebu" - ~15km from shop
- "Lapu-Lapu City, Cebu" - ~20km from shop
- "Cebu City, Cebu" - ~25km from shop
- "Talisay City, Cebu" - ~30km from shop

## Benefits Over Google Maps

### Cost
- **Google Maps**: $7 per 1000 requests (after free tier)
- **This Solution**: 100% Free, no limits

### Reliability
- **Google Maps**: Requires API key, billing setup
- **This Solution**: No API keys, no billing required

### Data Source
- **Google Maps**: Proprietary data
- **This Solution**: OpenStreetMap community data

### Customization
- **Google Maps**: Limited customization options
- **This Solution**: Full control over styling and functionality

## Troubleshooting

### Common Issues

1. **Map not loading**
   - Check internet connection
   - Verify Leaflet.js CDN is accessible

2. **Geocoding fails**
   - Ensure address includes "Philippines"
   - Try more specific address format

3. **Route calculation fails**
   - Check if coordinates are valid
   - Verify OSRM API is accessible

### Debug Mode
Enable console logging by adding this to the map component:
```javascript
console.log('Geocoding response:', geocodeData);
console.log('Route response:', routeData);
```

## Future Enhancements

1. **Caching** - Cache geocoded addresses and routes
2. **Offline Support** - Store map tiles locally
3. **Multiple Delivery Zones** - Different rates for different areas
4. **Delivery Time Estimation** - Include traffic data
5. **Address Validation** - Validate addresses before geocoding

## Support

For issues or questions:
1. Check the browser console for errors
2. Verify API endpoints are working
3. Test with the demo page first
4. Check Laravel logs for backend errors

## License

This implementation uses open-source libraries:
- Leaflet.js (BSD-2-Clause)
- OpenStreetMap (ODbL)
- OSRM (BSD-2-Clause)
- Nominatim (GPL-2.0)
