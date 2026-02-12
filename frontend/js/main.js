/**
 * Main JavaScript Module
 * Handles public landing page functionality
 */

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    mapModule.init();
    loadSubdivisions();
});

// Load subdivisions and display overview cards (1.1 Subdivision overview information)
function loadSubdivisions() {
    fetch('../backend/api/subdivisions.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displaySubdivisionCards(data.data);
            } else {
                console.error('Failed to load subdivisions:', data.error);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Display subdivision overview cards
function displaySubdivisionCards(subdivisions) {
    const container = document.getElementById('subdivisionsList');
    container.innerHTML = '';

    subdivisions.forEach(subdivision => {
        const card = document.createElement('div');
        card.className = 'col-md-6 col-lg-4';
        const statusPercentage = subdivision.property_count ? Math.round((subdivision.available_count / subdivision.property_count) * 100) : 0;
        
        card.innerHTML = `
            <div class="subdivision-card">
                <div class="subdivision-card-header">
                    <h5>${subdivision.name}</h5>
                </div>
                <div class="card-body">
                    <p class="card-text text-muted small mb-3"><i class="fas fa-map-marker"></i> ${subdivision.location || 'Premium Location'}</p>
                    <p class="card-text" style="color: #2c3e50; line-height: 1.6;">${subdivision.description || 'Premium residential subdivision with modern amenities and world-class facilities.'}</p>
                    ${subdivision.developer_name ? `<p class="small" style="color: #7f8c8d;"><strong>Developer:</strong> ${subdivision.developer_name}</p>` : ''}
                </div>
                <div class="subdivision-stats">
                    <div class="stat-item">
                        <div class="stat-number">${subdivision.property_count || 0}</div>
                        <div class="stat-label">Total Units</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" style="color: #27ae60; text-shadow: 0 2px 8px rgba(39, 174, 96, 0.3);">${subdivision.available_count || 0}</div>
                        <div class="stat-label">Available</div>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(card);
    });
}

// Search functionality - Villa Purita Only
document.getElementById('searchBtn')?.addEventListener('click', function() {
    const status = document.getElementById('searchStatus')?.value;

    let url = '../backend/api/properties.php?subdivision_id=1'; // Villa Purita ID is 1
    
    if (status) url += `&status=${status}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Search results:', data.data);
                displaySearchResults(data.data);
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('searchModal')).hide();
            }
        })
        .catch(error => console.error('Search error:', error));
});

// Display search results
function displaySearchResults(properties) {
    const alert = document.createElement('div');
    alert.className = 'alert alert-info alert-dismissible fade show mt-4';
    alert.role = 'alert';
    alert.innerHTML = `
        <strong>Search Results:</strong> Found ${properties.length} property(ies)
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.insertBefore(alert, document.querySelector('footer'));

    // Scroll to subdivisions section
    document.querySelector('#subdivisions').scrollIntoView({ behavior: 'smooth' });
}
