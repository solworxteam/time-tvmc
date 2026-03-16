(function () {
  'use strict';

  function escapeHtml(value) {
    if (value === null || value === undefined) return '';
    return String(value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/\"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

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

  function showStatus(html) {
    const status = document.getElementById('status');
    if (status) status.innerHTML = html;
  }

  function resetLocateButton() {
    const locateBtn = document.getElementById('locateBtn');
    if (!locateBtn) return;
    locateBtn.disabled = false;
    locateBtn.innerHTML = '<i data-lucide="locate-fixed" class="pt-inline-icon"></i><span>Use My Location</span>';
    if (window.lucide) {
      lucide.createIcons();
    }
  }

  function showResults(mosques) {
    if (mosques.length === 0) {
      showStatus('<div class="pt-soft-panel p-3" style="border-color:#fde68a;background:#fefce8;color:#854d0e;">No mosques found.</div>');
      return;
    }

    const closest = mosques[0];
    const closestName = escapeHtml(closest.name || 'Unknown Mosque');
    const closestAddress = escapeHtml(closest.address || 'N/A');
    const closestPostcode = escapeHtml(closest.postcode || 'N/A');
    let html = '';

    html += '<div class="pt-panel p-4 p-lg-5 mb-4">';
    html += '  <div class="d-flex flex-column flex-lg-row justify-content-between gap-4 align-items-lg-center">';
    html += '    <div>';
    html += '      <div class="badge rounded-pill text-bg-success mb-3">Your Closest Mosque</div>';
    html += '      <h3 class="mb-2" style="color:#166534;">' + closestName + '</h3>';
    html += '      <p class="mb-2"><strong style="font-size:1.15rem;color:#16a34a;">' + closest.distance.toFixed(2) + ' km away</strong></p>';
    html += '      <p class="mb-1 text-muted"><strong>Address:</strong> ' + closestAddress + '</p>';
    html += '      <p class="mb-0 text-muted"><strong>Postcode:</strong> ' + closestPostcode + '</p>';
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

      mosques.slice(1, 6).forEach(function (mosque) {
        const mosqueName = escapeHtml(mosque.name || 'Unknown Mosque');
        const mosqueAddress = escapeHtml(mosque.address || 'N/A');
        const mosquePostcode = escapeHtml(mosque.postcode || 'N/A');
        html += '<div class="col-lg-6">';
        html += '  <div class="pt-panel h-100 p-4">';
        html += '    <h5 class="mb-2" style="color:#166534;">' + mosqueName + '</h5>';
        html += '    <p class="mb-2"><strong style="color:#0f766e;">' + mosque.distance.toFixed(2) + ' km away</strong></p>';
        html += '    <p class="small text-muted mb-1">' + mosqueAddress + '</p>';
        html += '    <p class="small text-muted mb-3"><strong>Postcode:</strong> ' + mosquePostcode + '</p>';
        html += '    <div class="d-flex flex-column flex-sm-row gap-2">';
        html += '      <a href="/mosque.php?id=' + mosque.id + '" class="pt-btn-primary flex-grow-1"><i data-lucide="calendar-days" class="pt-inline-icon"></i><span>View Details</span></a>';
        html += '      <a href="https://maps.google.com/maps?daddr=' + encodeURIComponent((mosque.address || '') + ' ' + (mosque.postcode || '')) + '" target="_blank" rel="noopener noreferrer" class="pt-btn-secondary flex-grow-1"><i data-lucide="navigation" class="pt-inline-icon"></i><span>Directions</span></a>';
        html += '    </div>';
        html += '  </div>';
        html += '</div>';
      });

      html += '</div>';
    }

    const mosqueList = document.getElementById('mosqueList');
    if (mosqueList) {
      mosqueList.innerHTML = html;
    }

    showStatus('<div class="pt-soft-panel p-3" style="border-color:#86efac;background:#f0fdf4;color:#166534;"><strong>Found ' + mosques.length + ' mosque(s) near you.</strong></div>');

    const results = document.getElementById('results');
    if (results) {
      results.style.display = 'block';
    }

    resetLocateButton();
  }

  function initNearestPage() {
    const locateBtn = document.getElementById('locateBtn');
    if (!locateBtn) return;

    locateBtn.addEventListener('click', function () {
      if (!navigator.geolocation) {
        showStatus('<div class="pt-soft-panel p-3" style="border-color:#fca5a5;background:#fef2f2;color:#991b1b;">Geolocation is not supported by your browser.</div>');
        return;
      }

      locateBtn.disabled = true;
      locateBtn.innerHTML = '<i data-lucide="loader-circle" class="pt-inline-icon"></i><span>Getting your location...</span>';
      showStatus('<div class="pt-soft-panel p-3" style="border-color:#86efac;background:#f0fdf4;color:#166534;">Detecting your location...</div>');

      if (window.lucide) {
        lucide.createIcons();
      }

      navigator.geolocation.getCurrentPosition(
        function (position) {
          const lat = position.coords.latitude;
          const lon = position.coords.longitude;

          fetchNearestEndpoint(lat, lon)
            .then(async function (response) {
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
            .then(function (data) {
              if (!Array.isArray(data)) {
                throw new Error('Nearest service returned unexpected data.');
              }
              showResults(data);
            })
            .catch(function (err) {
              showStatus('<div class="pt-soft-panel p-3" style="border-color:#fca5a5;background:#fef2f2;color:#991b1b;">Error: ' + escapeHtml(err.message) + '</div>');
              resetLocateButton();
            });
        },
        function (error) {
          let msg = 'Location access denied: ' + error.message;
          if (error.code === 1) msg = 'Permission denied. Please enable location access.';
          showStatus('<div class="pt-soft-panel p-3" style="border-color:#fca5a5;background:#fef2f2;color:#991b1b;">' + escapeHtml(msg) + '</div>');
          resetLocateButton();
        }
      );
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNearestPage);
  } else {
    initNearestPage();
  }
})();
