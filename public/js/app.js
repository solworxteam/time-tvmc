/* ── app.js — Prayer Times Figma UI interactivity ───────────── */

(function () {
  'use strict';

  /* ── Utilities ─────────────────────────────────────────────── */

  function haversine(lat1, lon1, lat2, lon2) {
    var R = 6371;
    var dLat = (lat2 - lat1) * Math.PI / 180;
    var dLon = (lon2 - lon1) * Math.PI / 180;
    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  }

  function fmt24(t) { return t ? t.substring(0, 5) : '-'; }

  function fmt12(t) {
    if (!t) return '-';
    var parts = t.split(':');
    var h = parseInt(parts[0], 10);
    var m = parseInt(parts[1], 10);
    var period = h >= 12 ? 'PM' : 'AM';
    var h12 = h % 12 || 12;
    return h12 + ':' + (m < 10 ? '0' + m : m) + ' ' + period;
  }

  function timeToMinutes(t) {
    if (!t) return -1;
    var parts = t.split(':');
    return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
  }

  function minutesUntil(targetMinutes) {
    var now = new Date();
    var nowMin = now.getHours() * 60 + now.getMinutes();
    var diff = targetMinutes - nowMin;
    if (diff <= 0) diff += 1440;
    return diff;
  }

  function formatCountdown(minutes) {
    if (minutes >= 60) {
      var h = Math.floor(minutes / 60);
      var m = minutes % 60;
      return h + 'h ' + m + 'm';
    }
    return minutes + 'm';
  }

  function debounce(fn, delay) {
    var t;
    return function () {
      var args = arguments;
      var ctx = this;
      clearTimeout(t);
      t = setTimeout(function () { fn.apply(ctx, args); }, delay);
    };
  }

  /* ── State ──────────────────────────────────────────────────── */

  var is24Hour  = true;
  var darkMode  = localStorage.getItem('pt-dark') === '1';
  var favorites = new Set(JSON.parse(localStorage.getItem('pt-favs') || '[]'));
  var userLat   = null;
  var userLon   = null;
  var countdownInterval = null;

  var PRAYER_ORDER = ['fajr', 'zuhr', 'asr', 'maghrib', 'isha'];

  function getPrayerLabels() {
    return {
      fajr: 'Fajr',
      zuhr: (window.ZUHR_LABEL || 'Dhuhr'),
      asr: 'Asr',
      maghrib: 'Maghrib',
      isha: 'Isha'
    };
  }

  /* ── DOM helpers ────────────────────────────────────────────── */

  function $id(id) { return document.getElementById(id); }
  function $all(sel, ctx) { return (ctx || document).querySelectorAll(sel); }

  /* ── Dark mode ──────────────────────────────────────────────── */

  function applyDark(on) {
    var html = document.getElementById('html-root') || document.documentElement;
    if (on) { html.classList.add('dark'); } else { html.classList.remove('dark'); }
    var icon = $id('pt-dark-icon');
    if (icon) {
      icon.setAttribute('data-lucide', on ? 'sun' : 'moon');
      if (window.lucide) { lucide.createIcons(); }
    }
  }

  function initDark() {
    applyDark(darkMode);
    var btn = $id('pt-dark-btn');
    if (!btn) return;
    btn.addEventListener('click', function () {
      darkMode = !darkMode;
      localStorage.setItem('pt-dark', darkMode ? '1' : '0');
      applyDark(darkMode);
    });
  }

  /* ── Favourites ─────────────────────────────────────────────── */

  function saveFavs() {
    localStorage.setItem('pt-favs', JSON.stringify(Array.from(favorites)));
  }

  function updateFavStar(mosqueId) {
    $all('.pt-fav[data-mosque-id="' + mosqueId + '"]').forEach(function (btn) {
      btn.textContent = favorites.has(mosqueId) ? '\u2605' : '\u2606';
    });
  }

  function updateFavCount() {
    var el  = $id('pt-fav-count');
    var txt = $id('pt-fav-count-text');
    if (!el || !txt) return;
    if (favorites.size > 0) {
      var n = favorites.size;
      txt.textContent = n + ' favourite mosque' + (n !== 1 ? 's' : '');
      el.style.display = 'block';
    } else {
      el.style.display = 'none';
    }
  }

  function initFavourites() {
    favorites.forEach(updateFavStar);
    updateFavCount();
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.pt-fav');
      if (!btn) return;
      var id = btn.dataset.mosqueId;
      if (!id) return;
      if (favorites.has(id)) { favorites.delete(id); } else { favorites.add(id); }
      saveFavs();
      updateFavStar(id);
      updateFavCount();
      sortAndRenderCards();
    });
  }

  /* ── Geolocation ────────────────────────────────────────────── */

  function getNextPrayerForMosque(mosqueId) {
    if (!window.MOSQUE_DATA) return null;
    var m = null;
    for (var i = 0; i < MOSQUE_DATA.length; i++) {
      if (MOSQUE_DATA[i].id === mosqueId) { m = MOSQUE_DATA[i]; break; }
    }
    if (!m) return null;
    var nowMin = new Date().getHours() * 60 + new Date().getMinutes();
    var labels = getPrayerLabels();
    for (var k = 0; k < PRAYER_ORDER.length; k++) {
      var key = PRAYER_ORDER[k];
      var pr  = m.prayers[key];
      var t   = pr ? (pr.jamaat || pr.start) : null;
      if (!t) continue;
      var parts = t.split(':');
      var tMin  = parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
      if (tMin > nowMin) return { name: labels[key], time: t };
    }
    var fajr = m.prayers.fajr;
    var ft = fajr ? (fajr.jamaat || fajr.start) : null;
    return ft ? { name: labels['fajr'], time: ft } : null;
  }

  function updateCountdown() {
    var container = $id('pt-countdown');
    var nameEl    = $id('pt-cd-name');
    var timeEl    = $id('pt-cd-time');
    if (!container || !nameEl || !timeEl) return;

    var firstCard = document.querySelector('#pt-cards .pt-card');
    // find first visible
    var cards = $all('#pt-cards .pt-card');
    firstCard = null;
    for (var i = 0; i < cards.length; i++) {
      if (cards[i].style.display !== 'none') { firstCard = cards[i]; break; }
    }
    if (!firstCard) { container.style.display = 'none'; return; }

    var info = getNextPrayerForMosque(firstCard.dataset.mosqueId);
    if (!info) { container.style.display = 'none'; return; }

    var mins = minutesUntil(timeToMinutes(info.time));
    nameEl.textContent = info.name;
    timeEl.textContent = formatCountdown(mins);
    container.style.display = 'block';
  }

  function startCountdown() {
    updateCountdown();
    if (countdownInterval) clearInterval(countdownInterval);
    countdownInterval = setInterval(updateCountdown, 60000);
  }

  function updateDistancesAndSort(lat, lon) {
    userLat = lat;
    userLon = lon;
    $all('.pt-card').forEach(function (card) {
      var cLat = parseFloat(card.dataset.lat);
      var cLon = parseFloat(card.dataset.lon);
      if (!cLat || !cLon) return;
      var d = haversine(lat, lon, cLat, cLon);
      card.dataset.distance = d;
      var kmEl = card.querySelector('.pt-dist-km');
      if (kmEl) kmEl.textContent = d.toFixed(1);
    });
    sortAndRenderCards();
    updateCountdown();
  }

  function initGeolocation() {
    var geoLoading = $id('pt-geo-loading');
    var geoError   = $id('pt-geo-error');
    if (!geoLoading || !geoError) return;
    if (!navigator.geolocation) { geoError.style.display = 'flex'; return; }

    if (navigator.permissions && navigator.permissions.query) {
      navigator.permissions.query({ name: 'geolocation' }).then(function (res) {
        if (res.state === 'denied') { geoError.style.display = 'flex'; return; }
        requestGeo(geoLoading, geoError);
      }).catch(function () { requestGeo(geoLoading, geoError); });
    } else {
      requestGeo(geoLoading, geoError);
    }
  }

  function requestGeo(geoLoading, geoError) {
    geoLoading.style.display = 'block';
    navigator.geolocation.getCurrentPosition(
      function (pos) {
        geoLoading.style.display = 'none';
        updateDistancesAndSort(pos.coords.latitude, pos.coords.longitude);
        markNearest();
      },
      function () {
        geoLoading.style.display = 'none';
        geoError.style.display   = 'flex';
      },
      { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
    );
  }

  /* ── Nearest badge ──────────────────────────────────────────── */

  function markNearest() {
    $all('.pt-card').forEach(function (c) {
      c.classList.remove('is-nearest');
      var badge = c.querySelector('.pt-nearest-badge');
      if (badge) badge.style.display = 'none';
    });
    if (userLat === null) return;
    var cards = [];
    $all('#pt-cards .pt-card').forEach(function (c) {
      if (c.style.display !== 'none') cards.push(c);
    });
    if (!cards.length) return;
    var first = cards[0];
    first.classList.add('is-nearest');
    var badge = first.querySelector('.pt-nearest-badge');
    if (badge) badge.style.display = 'inline-block';
  }

  /* ── Prayer highlight re-evaluation ────────────────────────── */

  function recomputePrayerHighlights(card) {
    var mosqueId = card.dataset.mosqueId;
    if (!window.MOSQUE_DATA) return;
    var m = null;
    for (var i = 0; i < MOSQUE_DATA.length; i++) {
      if (MOSQUE_DATA[i].id === mosqueId) { m = MOSQUE_DATA[i]; break; }
    }
    if (!m) return;

    var nowMin = new Date().getHours() * 60 + new Date().getMinutes();
    var nextKey = null, currentKey = null;

    for (var k = 0; k < PRAYER_ORDER.length; k++) {
      var key = PRAYER_ORDER[k];
      var pr  = m.prayers[key];
      var compareTime = pr ? (pr.jamaat || pr.start) : null;
      if (!compareTime) continue;
      var parts = compareTime.split(':');
      var tMin  = parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
      if (tMin > nowMin) { if (nextKey === null) nextKey = key; }
      else { currentKey = key; }
    }
    if (!nextKey) nextKey = 'fajr';

    $all('.pt-pcell', card).forEach(function (cell) {
      var prayer = cell.dataset.prayer;
      cell.classList.remove('is-next', 'is-current');
      var nTag = cell.querySelector('.pt-next-tag');
      var cTag = cell.querySelector('.pt-current-tag');
      if (nTag) nTag.remove();
      if (cTag) cTag.remove();

      if (prayer === nextKey) {
        cell.classList.add('is-next');
        var tag = document.createElement('div');
        tag.className = 'pt-next-tag'; tag.textContent = 'Next';
        cell.appendChild(tag);
      } else if (prayer === currentKey) {
        cell.classList.add('is-current');
        var tag2 = document.createElement('div');
        tag2.className = 'pt-current-tag'; tag2.textContent = 'Current';
        cell.appendChild(tag2);
      }
    });
  }

  /* ── Time format ────────────────────────────────────────────── */

  function applyTimeFormat() {
    if (!window.MOSQUE_DATA) return;
    var fmtFn = is24Hour ? fmt24 : fmt12;

    $all('.pt-card').forEach(function (card) {
      var mosqueId = card.dataset.mosqueId;
      var m = null;
      for (var i = 0; i < MOSQUE_DATA.length; i++) {
        if (MOSQUE_DATA[i].id === mosqueId) { m = MOSQUE_DATA[i]; break; }
      }
      if (!m) return;

      $all('.pt-pcell', card).forEach(function (cell) {
        var prayer = cell.dataset.prayer;
        var pr = m.prayers[prayer];
        if (!pr) return;
        var start  = pr.start  || '';
        var jamaat = pr.jamaat || '';
        var display = jamaat || start;
        var startEl  = cell.querySelector('.pt-start');
        var jamaatEl = cell.querySelector('.pt-jamaat');
        if (startEl)  { startEl.textContent  = start  ? fmtFn(start)   : ''; }
        if (jamaatEl) { jamaatEl.textContent  = display ? fmtFn(display) : chr(8211); }
        if (startEl) {
          startEl.style.display = (start && jamaat && start !== jamaat) ? '' : 'none';
        }
      });
    });

    var icon = $id('pt-fmt-icon');
    if (icon) {
      icon.setAttribute('data-lucide', is24Hour ? 'clock' : 'clock-12');
      if (window.lucide) { lucide.createIcons(); }
    }
  }

  function chr(code) { return String.fromCharCode(code); }

  function initTimeFormat() {
    var btn = $id('pt-fmt-btn');
    if (!btn) return;
    btn.addEventListener('click', function () {
      is24Hour = !is24Hour;
      applyTimeFormat();
    });
  }

  /* ── Filter & sort ──────────────────────────────────────────── */

  function getFilterValues() {
    var searchEl = $id('pt-search');
    var areaEl   = $id('pt-area');
    var sortEl   = $id('pt-sort');
    return {
      search: (searchEl ? searchEl.value : '').toLowerCase().trim(),
      area:   areaEl  ? areaEl.value  : 'all',
      sort:   sortEl  ? sortEl.value  : 'distance'
    };
  }

  function sortAndRenderCards() {
    var f         = getFilterValues();
    var container = $id('pt-cards');
    var noResults = $id('pt-no-results');
    if (!container) return;

    var cards = [];
    $all('.pt-card', container).forEach(function (c) { cards.push(c); });

    var visible = 0;
    cards.forEach(function (card) {
      var name     = card.dataset.name     || '';
      var address  = card.dataset.address  || '';
      var postcode = card.dataset.postcode || '';
      var cardArea = card.dataset.area     || '';
      var matchSearch = !f.search || name.includes(f.search) || address.includes(f.search) || postcode.includes(f.search);
      var matchArea   = f.area === 'all' || cardArea === f.area;
      if (matchSearch && matchArea) { card.style.display = ''; visible++; }
      else { card.style.display = 'none'; }
    });

    if (noResults) noResults.style.display = visible === 0 ? 'block' : 'none';

    var visibleCards = cards.filter(function (c) { return c.style.display !== 'none'; });
    visibleCards.sort(function (a, b) {
      var aFav = favorites.has(a.dataset.mosqueId);
      var bFav = favorites.has(b.dataset.mosqueId);
      if (aFav && !bFav) return -1;
      if (!aFav && bFav) return 1;
      if (f.sort === 'name') {
        return (a.dataset.name || '').localeCompare(b.dataset.name || '');
      }
      return parseFloat(a.dataset.distance || 99999) - parseFloat(b.dataset.distance || 99999);
    });
    visibleCards.forEach(function (c) { container.appendChild(c); });

    markNearest();
    updateCountdown();
  }

  function initFilters() {
    var search = $id('pt-search');
    var area   = $id('pt-area');
    var sort   = $id('pt-sort');
    if (search) search.addEventListener('input', debounce(sortAndRenderCards, 200));
    if (area)   area.addEventListener('change', sortAndRenderCards);
    if (sort)   sort.addEventListener('change', sortAndRenderCards);
  }

  /* ── Directions ─────────────────────────────────────────────── */

  function initDirections() {
    var modalEl  = document.getElementById('mapChoiceModal');
    var appleBtn = document.getElementById('openAppleMapsBtn');
    var googleBtn= document.getElementById('openGoogleMapsBtn');

    function openMap(provider, lat, lon) {
      var url = provider === 'apple'
        ? 'https://maps.apple.com/?daddr=' + lat + ',' + lon + '&dirflg=d'
        : 'https://maps.google.com/maps?daddr=' + lat + ',' + lon;
      window.open(url, '_blank', 'noopener,noreferrer');
    }

    if (appleBtn && modalEl) {
      appleBtn.addEventListener('click', function () {
        openMap('apple', modalEl.dataset.lat, modalEl.dataset.lon);
        if (window.bootstrap) bootstrap.Modal.getOrCreateInstance(modalEl).hide();
      });
    }
    if (googleBtn && modalEl) {
      googleBtn.addEventListener('click', function () {
        openMap('google', modalEl.dataset.lat, modalEl.dataset.lon);
        if (window.bootstrap) bootstrap.Modal.getOrCreateInstance(modalEl).hide();
      });
    }

    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.directions-btn');
      if (!btn) return;
      var lat = btn.dataset.lat;
      var lon = btn.dataset.lon;
      if (!lat || !lon || lat === '0' || lon === '0') {
        alert('Location data not available for this mosque.');
        return;
      }
      var isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
      if (isIOS && modalEl && window.bootstrap) {
        modalEl.dataset.lat = lat;
        modalEl.dataset.lon = lon;
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
      } else {
        openMap('google', lat, lon);
      }
    });
  }

  /* ── Bootstrap ──────────────────────────────────────────────── */

  function init() {
    if (!document.getElementById('pt-home')) return;

    if (window.lucide) { lucide.createIcons(); }

    initDark();
    initFavourites();
    initTimeFormat();
    initFilters();
    initDirections();
    initGeolocation();
    startCountdown();

    setInterval(function () {
      $all('.pt-card').forEach(recomputePrayerHighlights);
      updateCountdown();
    }, 60000);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();