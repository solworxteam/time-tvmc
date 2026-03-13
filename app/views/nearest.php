
<div class="container mt-5">
    <h1>Find Nearest Mosque</h1>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <p class="lead">Click the button below to share your location and find the nearest mosque.</p>
                    <button id="locateBtn" class="btn btn-lg btn-success">📍 Use My Location</button>
                    <div id="status" class="mt-3"></div>
                </div>
            </div>

            <div id="results" style="display: none;">
                <h3>Nearest Mosques</h3>
                <div id="mosqueList"></div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">How It Works</h5>
                    <ol>
                        <li>Click the location button</li>
                        <li>Allow access to your location</li>
                        <li>We'll find the nearest mosques</li>
                        <li>View details and prayer times</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('locateBtn').addEventListener('click', function() {
    if (!navigator.geolocation) {
        document.getElementById('status').innerHTML = '<div class="alert alert-danger">Geolocation is not supported by your browser.</div>';
        return;
    }

    this.disabled = true;
    this.textContent = '⏳ Getting your location...';
    document.getElementById('status').innerHTML = '<div class="alert alert-info">Detecting your location...</div>';

    navigator.geolocation.getCurrentPosition(function(position) {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        
        // Fetch mosques and find nearest
        fetch('/api/mosques-with-distance?lat=' + lat + '&lon=' + lon)
            .then(r => r.json())
            .then(data => {
                showResults(data, lat, lon);
            })
            .catch(err => {
                document.getElementById('status').innerHTML = '<div class="alert alert-danger">Error: ' + err.message + '</div>';
            });
    }, function(error) {
        let msg = 'Location access denied: ' + error.message;
        if (error.code === 1) msg = 'Permission denied. Please enable location access.';
        document.getElementById('status').innerHTML = '<div class="alert alert-danger">' + msg + '</div>';
        document.getElementById('locateBtn').disabled = false;
        document.getElementById('locateBtn').textContent = '📍 Use My Location';
    });
});

function showResults(mosques, userLat, userLon) {
    if (mosques.length === 0) {
        document.getElementById('status').innerHTML = '<div class="alert alert-warning">No mosques found.</div>';
        return;
    }

    const closest = mosques[0];
    let html = '';
    
    // Show closest mosque prominently
    html += '<div class="row mb-4">';
    html += '  <div class="col-md-12">';
    html += '    <div class="card border-success shadow-lg" style="border-width: 3px;">';
    html += '      <div class="card-header bg-success text-white">';
    html += '        <h5 class="mb-0">🎯 Your Closest Mosque</h5>';
    html += '      </div>';
    html += '      <div class="card-body">';
    html += '        <h3 class="card-title text-success">' + closest.name + '</h3>';
    html += '        <p class="card-text"><strong style="font-size: 1.3em; color: #28a745;">' + closest.distance.toFixed(2) + ' km away</strong></p>';
    html += '        <p class="card-text"><strong>Address:</strong> ' + (closest.address || 'N/A') + '</p>';
    html += '        <p class="card-text"><strong>Postcode:</strong> ' + closest.postcode + '</p>';
    html += '        <a href="/mosque.php?id=' + closest.id + '" class="btn btn-lg btn-success">View Details & Prayer Times →</a>';
    html += '      </div>';
    html += '    </div>';
    html += '  </div>';
    html += '</div>';

    // Show other nearby mosques
    if (mosques.length > 1) {
        html += '<hr>';
        html += '<h4 class="mt-4 mb-3">Other Nearby Mosques</h4>';
        html += '<div class="row">';
        
        mosques.slice(1, 6).forEach(mosque => {
            html += '<div class="col-md-6 mb-3">';
            html += '  <div class="card h-100">';
            html += '    <div class="card-body">';
            html += '      <h5 class="card-title">' + mosque.name + '</h5>';
            html += '      <p class="card-text"><strong class="text-info">' + mosque.distance.toFixed(2) + ' km away</strong></p>';
            html += '      <p class="card-text small text-muted">' + (mosque.address || 'N/A') + '</p>';
            html += '      <p class="card-text small text-muted"><strong>Postcode:</strong> ' + mosque.postcode + '</p>';
            html += '      <a href="/mosque.php?id=' + mosque.id + '" class="btn btn-sm btn-primary">View Details</a>';
            html += '    </div>';
            html += '  </div>';
            html += '</div>';
        });
        
        html += '</div>';
    }

    document.getElementById('mosqueList').innerHTML = html;
    document.getElementById('status').innerHTML = '<div class="alert alert-success"><strong>✓ Found ' + mosques.length + ' mosque(s) near you!</strong></div>';
    document.getElementById('results').style.display = 'block';
    document.getElementById('locateBtn').disabled = false;
    document.getElementById('locateBtn').textContent = '📍 Use My Location';
}
</script>
