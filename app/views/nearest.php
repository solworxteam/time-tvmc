
<div class="pt-inner-header">
    <div class="container">
        <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
            <div>
                <h1>Find Nearest Mosque</h1>
                <p>Share your location to find the closest mosques and jump straight to prayer times or directions.</p>
            </div>
            <div class="text-white-50 fw-semibold">Live location lookup</div>
        </div>
    </div>
</div>

<div class="container pb-4">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="pt-panel p-4 p-lg-5 mb-4">
                <p class="lead mb-4">Click the button below to share your location and find the nearest mosque.</p>
                <button id="locateBtn" class="pt-btn-primary">
                    <i data-lucide="locate-fixed" class="pt-inline-icon"></i>
                    <span>Use My Location</span>
                </button>
                <div id="status" class="mt-3"></div>
            </div>

            <div id="results" style="display:none;">
                <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-3">
                    <h2 class="h4 mb-0">Nearest Mosques</h2>
                    <span class="text-muted small">Sorted by distance from your current location</span>
                </div>
                <div id="mosqueList"></div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="pt-panel p-4 h-100">
                <h2 class="h4 mb-3">How It Works</h2>
                <div class="pt-soft-panel p-4">
                    <ol class="mb-0 ps-3">
                        <li class="mb-2">Click the location button.</li>
                        <li class="mb-2">Allow access to your location.</li>
                        <li class="mb-2">We calculate the nearest mosques.</li>
                        <li>Open details, prayer times, or directions.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function fetchNearestEndpoint(lat, lon) {
    const query = 'lat=' + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lon);
    const primaryUrl = '/api/mosques-with-distance?' + query;
    const fallbackUrl = '/api/mosques-with-distance.php?' + query;

    const primaryResponse = await fetch(primaryUrl);
    if (primaryResponse.status === 404) {
        return fetch(fallbackUrl);
    }

    return primaryResponse;
}

document.getElementById('locateBtn').addEventListener('click', function() {
    if (!navigator.geolocation) {
        document.getElementById('status').innerHTML = '<div class="pt-soft-panel p-3" style="border-color:#fca5a5;background:#fef2f2;color:#991b1b;">Geolocation is not supported by your browser.</div>';
        return;
    }

    this.disabled = true;
    this.innerHTML = '<i data-lucide="loader-circle" class="pt-inline-icon"></i><span>Getting your location...</span>';
    document.getElementById('status').innerHTML = '<div class="pt-soft-panel p-3" style="border-color:#86efac;background:#f0fdf4;color:#166534;">Detecting your location...</div>';
    if (window.lucide) { lucide.createIcons(); }

    navigator.geolocation.getCurrentPosition(function(position) {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        
        // Fetch mosques and find nearest
        fetchNearestEndpoint(lat, lon)
            .then(async (response) => {
                const contentType = response.headers.get('content-type') || '';
                const rawBody = await response.text();

                if (!response.ok) {
                    throw new Error('Nearest service failed (HTTP ' + response.status + ').');
                }

                if (!contentType.toLowerCase().includes('application/json')) {
                    throw new Error('Nearest service returned an invalid response format.');
                }

                try {
                    return JSON.parse(rawBody);
                } catch (parseError) {
                    throw new Error('Nearest service returned unreadable data.');
                }
            })
            .then(data => {
                if (!Array.isArray(data)) {
                    throw new Error('Nearest service returned unexpected data.');
                }
                showResults(data, lat, lon);
            })
            .catch(err => {
                document.getElementById('status').innerHTML = '<div class="pt-soft-panel p-3" style="border-color:#fca5a5;background:#fef2f2;color:#991b1b;">Error: ' + err.message + '</div>';
                document.getElementById('locateBtn').disabled = false;
                document.getElementById('locateBtn').innerHTML = '<i data-lucide="locate-fixed" class="pt-inline-icon"></i><span>Use My Location</span>';
                if (window.lucide) { lucide.createIcons(); }
            });
    }, function(error) {
        let msg = 'Location access denied: ' + error.message;
        if (error.code === 1) msg = 'Permission denied. Please enable location access.';
        document.getElementById('status').innerHTML = '<div class="pt-soft-panel p-3" style="border-color:#fca5a5;background:#fef2f2;color:#991b1b;">' + msg + '</div>';
        document.getElementById('locateBtn').disabled = false;
        document.getElementById('locateBtn').innerHTML = '<i data-lucide="locate-fixed" class="pt-inline-icon"></i><span>Use My Location</span>';
        if (window.lucide) { lucide.createIcons(); }
    });
});

function showResults(mosques, userLat, userLon) {
    if (mosques.length === 0) {
        document.getElementById('status').innerHTML = '<div class="pt-soft-panel p-3" style="border-color:#fde68a;background:#fefce8;color:#854d0e;">No mosques found.</div>';
        return;
    }

    const closest = mosques[0];
    let html = '';
    
    html += '<div class="pt-panel p-4 p-lg-5 mb-4">';
    html += '  <div class="d-flex flex-column flex-lg-row justify-content-between gap-4 align-items-lg-center">';
    html += '    <div>';
    html += '      <div class="badge rounded-pill text-bg-success mb-3">Your Closest Mosque</div>';
    html += '      <h3 class="mb-2" style="color:#166534;">' + closest.name + '</h3>';
    html += '      <p class="mb-2"><strong style="font-size:1.15rem;color:#16a34a;">' + closest.distance.toFixed(2) + ' km away</strong></p>';
    html += '      <p class="mb-1 text-muted"><strong>Address:</strong> ' + (closest.address || 'N/A') + '</p>';
    html += '      <p class="mb-0 text-muted"><strong>Postcode:</strong> ' + closest.postcode + '</p>';
    html += '    </div>';
    html += '    <div class="d-flex flex-column flex-sm-row gap-2">';
    html += '      <a href="/mosque.php?id=' + closest.id + '" class="pt-btn-primary"><i data-lucide="calendar-days" class="pt-inline-icon"></i><span>View Prayer Times</span></a>';
    html += '      <a href="https://maps.google.com/maps?daddr=' + encodeURIComponent((closest.address || '') + ' ' + (closest.postcode || '')) + '" target="_blank" rel="noopener noreferrer" class="pt-btn-secondary"><i data-lucide="navigation" class="pt-inline-icon"></i><span>Directions</span></a>';
    html += '    </div>';
    html += '  </div>';
    html += '</div>';

    if (mosques.length > 1) {
        html += '<h4 class="mt-4 mb-3">Other Nearby Mosques</h4>';
        html += '<div class="row g-4">';
        
        mosques.slice(1, 6).forEach(mosque => {
            html += '<div class="col-lg-6">';
            html += '  <div class="pt-panel h-100 p-4">';
            html += '    <h5 class="mb-2" style="color:#166534;">' + mosque.name + '</h5>';
            html += '    <p class="mb-2"><strong style="color:#0f766e;">' + mosque.distance.toFixed(2) + ' km away</strong></p>';
            html += '    <p class="small text-muted mb-1">' + (mosque.address || 'N/A') + '</p>';
            html += '    <p class="small text-muted mb-3"><strong>Postcode:</strong> ' + mosque.postcode + '</p>';
            html += '    <div class="d-flex flex-column flex-sm-row gap-2">';
            html += '      <a href="/mosque.php?id=' + mosque.id + '" class="pt-btn-primary flex-grow-1"><i data-lucide="calendar-days" class="pt-inline-icon"></i><span>View Details</span></a>';
            html += '      <a href="https://maps.google.com/maps?daddr=' + encodeURIComponent((mosque.address || '') + ' ' + (mosque.postcode || '')) + '" target="_blank" rel="noopener noreferrer" class="pt-btn-secondary flex-grow-1"><i data-lucide="navigation" class="pt-inline-icon"></i><span>Directions</span></a>';
            html += '    </div>';
            html += '  </div>';
            html += '</div>';
        });
        
        html += '</div>';
    }

    document.getElementById('mosqueList').innerHTML = html;
    document.getElementById('status').innerHTML = '<div class="pt-soft-panel p-3" style="border-color:#86efac;background:#f0fdf4;color:#166534;"><strong>Found ' + mosques.length + ' mosque(s) near you.</strong></div>';
    document.getElementById('results').style.display = 'block';
    document.getElementById('locateBtn').disabled = false;
    document.getElementById('locateBtn').innerHTML = '<i data-lucide="locate-fixed" class="pt-inline-icon"></i><span>Use My Location</span>';
    if (window.lucide) { lucide.createIcons(); }
}
</script>
