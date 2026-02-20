/**
 * Interactive Map Module
 * Handles map initialization and property markers
 */

let map;
let markers = [];
let currentSubdivisions = [];

// Initialize map (1.2 Interactive Map Preview)
function initializeMap(containerId = 'mapPreview') {
    if (!document.getElementById(containerId)) return;

    // Villa Purita Subdivision, Minglanilla, Cebu, Philippines
    const villapuritaCoords = [10.257609202484922, 123.80114720758648];
    
    map = L.map(containerId, {
        zoomControl: true,
        attributionControl: true,
        preferCanvas: true,
        renderer: L.canvas(),
        layers: [],
        minZoom: 12,
        maxZoom: 19
    }).setView(villapuritaCoords, 13);

    // Define Villa Purita subdivision bounds (showing entire area) - Precise coordinates
    const villaPuritaBounds = [
        [10.2550, 123.7970], // Southwest corner
        [10.2600, 123.8050]  // Northeast corner
    ];

    // Define multiple latest map tile layers
    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '© Esri, DigitalGlobe, Earthstar Geographics, CNES/Airbus DS, USDA, USGS',
        maxZoom: 19,
        minZoom: 1,
        crossOrigin: true
    });

    // Add street labels overlay on top of satellite
    const satLabels = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_only_labels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        minZoom: 1,
        subdomains: 'abcd',
        attribution: '© CartoDB',
        pane: 'shadowPane'
    });

    // Latest CartoDB Positron (modern clean style)
    const positronLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
        attribution: '© CartoDB',
        maxZoom: 19,
        minZoom: 1,
        subdomains: 'abcd'
    });

    // Latest Esri Streets
    const streetLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
        attribution: '© Esri',
        maxZoom: 19,
        minZoom: 1
    });

    // Latest OpenStreetMap
    const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
        minZoom: 1
    });

    // Set satellite as default active layer
    satelliteLayer.addTo(map);
    satLabels.addTo(map);

    // Layer control for easy switching
    const baseLayers = {
        'Satellite (Latest)': satelliteLayer,
        'Streets': streetLayer,
        'Light Map': positronLayer,
        'OpenStreetMap': osmLayer
    };

    L.control.layers(baseLayers, {}, {
        position: 'topleft',
        collapsed: true
    }).addTo(map);

    // Fit entire Villa Purita subdivision into view
    map.fitBounds(villaPuritaBounds, { padding: [50, 50] });

    // Load subdivisions and display on map
    loadSubdivisionsForMap();
    addHousesLayer();
}

// Load subdivisions and add markers to map (3.1, 3.2, 3.3 Interactive Map Management)
function loadSubdivisionsForMap() {
    fetch('../backend/api/subdivisions.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentSubdivisions = data.data;
                currentSubdivisions.forEach(subdivision => {
                    addSubdivisionMarker(subdivision);
                    loadPropertiesForSubdivision(subdivision.id);
                });
            }
        })
        .catch(error => console.error('Error loading subdivisions:', error));
}

// Add subdivision marker to map
function addSubdivisionMarker(subdivision) {
    if (!subdivision.latitude || !subdivision.longitude) return;

    const marker = L.circleMarker([subdivision.latitude, subdivision.longitude], {
        radius: 12,
        fillColor: '#d4af37',
        color: '#1a365d',
        weight: 3,
        opacity: 0.9,
        fillOpacity: 0.85
    }).addTo(map);

    const popup = `
        <div style="min-width: 220px; font-family: 'Poppins', sans-serif;">
            <h6 style="color: #1a365d; font-weight: 800; margin-bottom: 8px; font-size: 1.1rem;">${subdivision.name}</h6>
            <p class="small" style="color: #2c3e50; margin-bottom: 10px;"><i class="fas fa-map-marker"></i> ${subdivision.location}</p>
            <hr style="margin: 8px 0; border-color: #e0e0e0;">
            <p style="color: #2c3e50; font-size: 0.85rem; margin: 6px 0;"><strong>Total Units:</strong> ${subdivision.total_units || 'N/A'}</p>
            <p style="color: #2c3e50; font-size: 0.85rem; margin: 6px 0;"><strong style="color: #27ae60;">Available:</strong> ${subdivision.available_count || 0}</p>
        </div>
    `;

    marker.bindPopup(popup, {
        maxWidth: 250,
        closeButton: true
    });

    marker.on('mouseover', function() {
        this.openPopup();
        this.setStyle({ fillOpacity: 1, weight: 4 });
    });

    marker.on('mouseout', function() {
        this.closePopup();
        this.setStyle({ fillOpacity: 0.85, weight: 3 });
    });

    markers.push(marker);
}

// Load properties for a subdivision (3.2 House or lot visualization)
function loadPropertiesForSubdivision(subdivisionId) {
    fetch(`../backend/api/properties.php?subdivision_id=${subdivisionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                data.data.forEach(property => {
                    addPropertyMarker(property);
                });
            }
        })
        .catch(error => console.error('Error loading properties:', error));
}

// Add property marker with color coding (3.3, 3.4, 3.5)
function addPropertyMarker(property) {
    if (!property.latitude || !property.longitude) return;

    const markerColor = getMarkerColor(property.status);
    const markerGradient = getMarkerGradient(property.status);
    
    // Create SVG marker with gradient and shadow for 3D effect
    const markerSvg = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 50" width="40" height="50">
            <defs>
                <linearGradient id="${property.id}-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:${markerGradient.start};stop-opacity:1" />
                    <stop offset="100%" style="stop-color:${markerGradient.end};stop-opacity:1" />
                </linearGradient>
                <filter id="shadow">
                    <feDropShadow dx="0" dy="4" stdDeviation="3" flood-opacity="0.4"/>
                </filter>
            </defs>
            <path fill="url(#${property.id}-gradient)" stroke="white" stroke-width="2" d="M20 2c9.4 0 14 8 14 14 0 12-14 30-14 30S6 28 6 16c0-6 4.6-14 14-14z" filter="url(#shadow)"/>
            <circle cx="20" cy="16" r="5" fill="white" opacity="0.9"/>
        </svg>
    `;

    const markerUrl = 'data:image/svg+xml;base64,' + btoa(markerSvg);
    const markerIcon = L.icon({
        iconUrl: markerUrl,
        iconSize: [40, 50],
        iconAnchor: [20, 50],
        popupAnchor: [0, -50],
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
        shadowSize: [41, 41],
        shadowAnchor: [12, 41]
    });

    const marker = L.marker([property.latitude, property.longitude], {
        icon: markerIcon,
        riseOnHover: true
    }).addTo(map);

    // Enhanced popup content with professional styling
    const statusBadgeColor = getStatusBadgeColor(property.status);
    const popup = `
        <div class="property-popup" style="min-width: 250px; font-family: 'Poppins', sans-serif;">
            <h6 style="color: #1a365d; margin-bottom: 10px; font-weight: 800;">${property.unit_number}</h6>
            <div style="margin-bottom: 10px;">
                <span class="badge" style="background: ${statusBadgeColor}; padding: 8px 12px; border-radius: 6px; color: white; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; font-size: 11px;">
                    ${property.status.toUpperCase().replace(/_/g, ' ')}
                </span>
            </div>
            ${property.price ? `<p style="color: #2c3e50; font-size: 0.9rem; margin: 8px 0;"><strong>Price:</strong> <span style="color: #d4af37; font-weight: 800;">₱${formatPrice(property.price)}</span></p>` : ''}
            ${property.bedrooms ? `<p style="color: #2c3e50; font-size: 0.9rem; margin: 8px 0;"><strong>Beds:</strong> ${property.bedrooms} | <strong>Baths:</strong> ${property.bathrooms || 'N/A'}</p>` : ''}
            ${property.area_sqm ? `<p style="color: #2c3e50; font-size: 0.9rem; margin: 8px 0;"><strong>Area:</strong> ${property.area_sqm} sqm</p>` : ''}
            <button class="btn btn-sm" onclick="showPropertyDetails(${property.id})" style="margin-top: 10px; background: linear-gradient(135deg, #1a365d 0%, #2d5a8c 100%); border: none; color: white; font-weight: 700; padding: 8px 16px; border-radius: 6px; cursor: pointer; transition: all 0.3s;">
                View Details
            </button>
        </div>
    `;

    marker.bindPopup(popup, {
        maxWidth: 300,
        closeButton: true,
        className: 'property-popup-wrapper'
    });

    marker.on('mouseover', function() {
        this.openPopup();
    });

    markers.push(marker);
}

// Get marker color based on status (3.5 Color-coded availability indicators)
function getMarkerColor(status) {
    const colors = {
        'occupied': '#7f8c8d',      // Gray
        'vacant': '#27ae60',        // Green
        'for_sale': '#3498db'       // Blue
    };
    return colors[status] || '#7f8c8d';
}

// Get gradient colors for markers
function getMarkerGradient(status) {
    const gradients = {
        'occupied': { start: '#7f8c8d', end: '#5d6d7b' },
        'vacant': { start: '#27ae60', end: '#229954' },
        'for_sale': { start: '#3498db', end: '#2980b9' }
    };
    return gradients[status] || { start: '#7f8c8d', end: '#5d6d7b' };
}

// Get badge color for status
function getStatusBadgeColor(status) {
    const badgeColors = {
        'occupied': '#7f8c8d',
        'vacant': '#27ae60',
        'for_sale': '#3498db'
    };
    return badgeColors[status] || '#7f8c8d';
}

// Format price
function formatPrice(price) {
    return parseFloat(price).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Show property details modal
function showPropertyDetails(propertyId) {
    fetch(`../backend/api/properties.php?id=${propertyId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Open modal with details
                console.log('Property details:', data.data);
                // Implementation for details modal
            }
        })
        .catch(error => console.error('Error loading property details:', error));
}

// Clear markers from map
function clearMarkers() {
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
}

// Update map based on filters
function filterMapByStatus(status) {
    clearMarkers();
    if (status === 'all') {
        loadSubdivisionsForMap();
    } else {
        currentSubdivisions.forEach(subdivision => {
            loadPropertiesForSubdivision(subdivision.id, status);
        });
    }
}

// Export for use in other scripts
window.mapModule = {
    init: initializeMap,
    loadSubdivisions: loadSubdivisionsForMap,
    filterByStatus: filterMapByStatus,
    clearMarkers: clearMarkers
};

const housesGeoJSON = {
    "type": "FeatureCollection",
    "features": [
        {
            "type": "Feature",
            "properties": {
                "status": "vacant",
                "name": "House 101",
                "block": "Block 3",
                "lot": "Lot 12",
                
                "images": ["house2.jpg", "purita1.jpg", "purita1.jpg", "purita1.jpg"]
            },
            "geometry": {
                "type": "Point",
                "coordinates": [123.802, 10.260]
            }
        },
        {
            "type": "Feature",
            "properties": {
                "status": "occupied",
                "name": "House 102",
                "block": "Block 2",
                "lot": "Lot 5",
                "images": []
            },
            "geometry": {
                "type": "Point",
              "coordinates": [123.802, 10.258]

            }
        }
    ]
};

function addHousesLayer() {
    L.geoJSON(housesGeoJSON, {

        pointToLayer: function(feature, latlng) {

            const colorMap = {
                'vacant': '#2ecc71',
                'for_sale': '#3498db',
                'occupied': '#7f8c8d'
            };

            return L.circleMarker(latlng, {
                radius: 8,
                fillColor: colorMap[feature.properties.status] || '#ccc',
                color: '#fff',
                weight: 1,
                fillOpacity: 0.9
            });
        },

        onEachFeature: function(feature, layer) {

            const status = feature.properties.status;

            layer.on('click', function() {

                // 🚫 Block occupied houses
                if (status === 'occupied') return;

                openPanel({
                    title: feature.properties.name,
                    status: status,
                    block: feature.properties.block,
                    lot: feature.properties.lot,
                    images: feature.properties.images
                });

            });

        }

    }).addTo(map);
}


// Call after map initialization

function openPanel(data) {
    console.log('Opening panel with data:', data);
    
    document.getElementById("panelTitle").innerText = data.title;
    document.getElementById("panelStatus").innerText = data.status;
    document.getElementById("panelBlock").innerText = data.block;
    document.getElementById("panelLot").innerText = data.lot;

    // Populate additional fields
    try {
        document.getElementById("panelPrice").innerText = data.price ? '₱' + formatPrice(data.price) : '$1000000';
        document.getElementById("panelPropertyType").innerText = data.property_type || data.type || 'Single Family';
        document.getElementById("panelYearBuilt").innerText = data.year_built || '2020';
        document.getElementById("panelTotalArea").innerText = data.area_sqm ? data.area_sqm + ' sqm' : (data.area ? data.area : 'hmm');
        document.getElementById("panelLotSize").innerText = data.lot_size ? data.lot_size + (data.lot_size_unit ? (' ' + data.lot_size_unit) : ' sqm') : 'N/A';
        document.getElementById("panelBedrooms").innerText = data.bedrooms || '3';
        document.getElementById("panelBathrooms").innerText = data.bathrooms || '2';
        document.getElementById("panelGarage").innerText = data.garage_spaces || 'N/A';
        document.getElementById("panelStories").innerText = data.stories || data.floor_level || '2';
    } catch (e) {
        console.warn('Some panel fields missing in DOM:', e);
    }

    const panelImage = document.getElementById("panelImage");
    
    // Get the first image
    const imageUrl = (data.images && data.images.length > 0) 
        ? data.images[0] 
        : "purita1.jpg";
    
    console.log('Setting image URL to:', imageUrl);
    panelImage.src = imageUrl;
    
    // Debug: log when image loads or fails
    panelImage.onload = function() {
        console.log('Image loaded successfully:', imageUrl);
    };
    
    panelImage.onerror = function() {
        console.error('Image failed to load:', imageUrl);
        this.src = "purita1.jpg";
    };

    document.getElementById("propertyPanel").classList.add("active");
    console.log('Panel opened');
}



function closePanel() {
    document.getElementById("propertyPanel").classList.remove("active");
}
// Call after map initialization
initializeMap();

