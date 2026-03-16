
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
<script src="/js/nearest.js"></script>
