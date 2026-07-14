@extends('layouts.app')

@section('title', 'Pengaturan Kantor')
@section('page-title', 'Pengaturan Lokasi & Jadwal')
@section('page-subtitle', 'Atur koordinat dan jadwal operasional kantor')

@section('content')
<div style="max-width: 600px;">
    <form action="{{ route('admin.settings.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
        @csrf
        @method('PUT')

        <!-- Location Settings -->
        <div class="card" style="padding: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid var(--slate-100);">
                <span class="material-symbols-outlined" style="font-size: 24px; color: var(--primary);">location_on</span>
                <h3 style="font-size: 1rem; font-weight: 700;">Lokasi Kantor</h3>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Nama Instansi</label>
                    <input type="text" name="nama_instansi" value="{{ old('nama_instansi', $settings->nama_instansi) }}"
                           class="form-input" style="padding: 0.75rem;"
                           placeholder="PT. Anchor Precision Indonesia">
                </div>

                <!-- Map location picker -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Cari & Pilih Lokasi di Peta</label>
                    <div style="position: relative;">
                        <div style="position: relative;">
                            <span class="material-symbols-outlined" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); font-size: 20px; color: var(--slate-400); pointer-events: none;">search</span>
                            <input type="text" id="mapSearch" autocomplete="off"
                                   class="form-input" style="padding: 0.75rem 0.75rem 0.75rem 2.5rem;"
                                   placeholder="Cari alamat atau nama tempat...">
                        </div>
                        <div id="mapSearchResults" style="display: none; position: absolute; z-index: 1000; left: 0; right: 0; top: calc(100% + 0.25rem); background: var(--surface); border: 1px solid var(--slate-100); border-radius: var(--radius-md); box-shadow: var(--shadow-lg); max-height: 240px; overflow-y: auto;"></div>
                    </div>
                    <div id="map" style="height: 320px; margin-top: 0.5rem; border-radius: var(--radius-lg); border: 1px solid var(--slate-100); overflow: hidden; z-index: 1;"></div>
                    <p style="font-size: 0.75rem; color: var(--slate-400); margin-top: 0.25rem;">
                        Geser penanda atau klik pada peta untuk menentukan titik koordinat kantor.
                    </p>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Latitude</label>
                        <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $settings->latitude) }}"
                               class="form-input" style="padding: 0.75rem; font-family: monospace;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Longitude</label>
                        <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $settings->longitude) }}"
                               class="form-input" style="padding: 0.75rem; font-family: monospace;">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">
                        Radius Maksimum
                        <span style="font-weight: 400; color: var(--slate-400);">(meter)</span>
                    </label>
                    <input type="number" id="radius_meters" name="radius_meters" value="{{ old('radius_meters', $settings->radius_meters) }}"
                           class="form-input" style="padding: 0.75rem; max-width: 150px;">
                    <p style="font-size: 0.75rem; color: var(--slate-400); margin-top: 0.25rem;">
                        Jarak maksimal peserta dari titik koordinat kantor
                    </p>
                </div>
            </div>
        </div>

        <!-- Schedule Settings -->
        <div class="card" style="padding: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid var(--slate-100);">
                <span class="material-symbols-outlined" style="font-size: 24px; color: var(--primary);">schedule</span>
                <h3 style="font-size: 1rem; font-weight: 700;">Jadwal Operasional</h3>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Waktu Masuk</label>
                    <input type="time" name="waktu_masuk"
                           value="{{ old('waktu_masuk', $settings->waktu_masuk) }}"
                           class="form-input" style="padding: 0.75rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">Waktu Pulang</label>
                    <input type="time" name="waktu_pulang"
                           value="{{ old('waktu_pulang', $settings->waktu_pulang) }}"
                           class="form-input" style="padding: 0.75rem;">
                </div>
            </div>

            <div style="margin-top: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.375rem;">
                    Durasi QR Code <span style="font-weight: 400; color: var(--slate-400);">(menit)</span>
                </label>
                <input type="number" name="qr_expiry_minutes"
                       value="{{ old('qr_expiry_minutes', $settings->qr_expiry_minutes) }}"
                       class="form-input" style="padding: 0.75rem; max-width: 100px;">
                <p style="font-size: 0.75rem; color: var(--slate-400); margin-top: 0.25rem;">
                    QR Code otomatis berganti setiap {{ $settings->qr_expiry_minutes }} menit
                </p>
            </div>
        </div>

        <!-- Actions -->
        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost" style="text-decoration: none;">
                Batal
            </a>
            <button type="submit" class="btn btn-primary" style="min-width: 150px;">
                <span class="material-symbols-outlined" style="font-size: 18px;">save</span>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    // Ensure default marker icons load from the CDN (avoids missing-icon issue).
    delete L.Icon.Default.prototype._getIconUrl;
    L.Icon.Default.mergeOptions({
        iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
        iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
        shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
    });

    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const radiusInput = document.getElementById('radius_meters');
    const searchInput = document.getElementById('mapSearch');
    const resultsBox = document.getElementById('mapSearchResults');

    const primary = (getComputedStyle(document.documentElement).getPropertyValue('--primary') || '').trim() || '#2563eb';
    const defaultCenter = [-2.5489, 118.0149]; // Indonesia
    const savedLat = parseFloat(latInput.value);
    const savedLng = parseFloat(lngInput.value);
    const hasSaved = !isNaN(savedLat) && !isNaN(savedLng);
    const startLatLng = hasSaved ? [savedLat, savedLng] : defaultCenter;

    const map = L.map('map').setView(startLatLng, hasSaved ? 16 : 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    function currentRadius() {
        const r = parseFloat(radiusInput.value);
        return (isNaN(r) || r <= 0) ? 100 : r;
    }

    const marker = L.marker(startLatLng, { draggable: true }).addTo(map);
    const circle = L.circle(startLatLng, {
        radius: currentRadius(),
        color: primary,
        weight: 2,
        fillColor: primary,
        fillOpacity: 0.12,
    }).addTo(map);

    // Fix rendering when the container is sized after initial layout.
    setTimeout(() => map.invalidateSize(), 150);

    function writeInputs(lat, lng) {
        latInput.value = lat.toFixed(6);
        lngInput.value = lng.toFixed(6);
    }

    function moveTo(lat, lng, recenter) {
        marker.setLatLng([lat, lng]);
        circle.setLatLng([lat, lng]);
        writeInputs(lat, lng);
        if (recenter) {
            map.setView([lat, lng], Math.max(map.getZoom(), 16));
        }
    }

    marker.on('drag', function () {
        const p = marker.getLatLng();
        circle.setLatLng(p);
        writeInputs(p.lat, p.lng);
    });

    map.on('click', function (e) {
        moveTo(e.latlng.lat, e.latlng.lng, false);
    });

    function onManualInput() {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);
        if (!isNaN(lat) && !isNaN(lng)) {
            marker.setLatLng([lat, lng]);
            circle.setLatLng([lat, lng]);
            map.setView([lat, lng], Math.max(map.getZoom(), 16));
        }
    }
    latInput.addEventListener('change', onManualInput);
    lngInput.addEventListener('change', onManualInput);

    radiusInput.addEventListener('input', function () {
        circle.setRadius(currentRadius());
    });

    // --- Address search (Nominatim / OpenStreetMap) ---
    let searchTimer = null;

    function clearResults() {
        resultsBox.style.display = 'none';
        resultsBox.innerHTML = '';
    }

    function renderResults(items) {
        resultsBox.innerHTML = '';
        if (!items.length) {
            const empty = document.createElement('div');
            empty.style.cssText = 'padding: 0.75rem; font-size: 0.8125rem; color: var(--slate-400);';
            empty.textContent = 'Lokasi tidak ditemukan';
            resultsBox.appendChild(empty);
            resultsBox.style.display = 'block';
            return;
        }
        items.forEach(function (item) {
            const el = document.createElement('div');
            el.style.cssText = 'padding: 0.625rem 0.75rem; font-size: 0.8125rem; color: var(--slate-700); cursor: pointer; border-bottom: 1px solid var(--slate-100); line-height: 1.35;';
            el.textContent = item.display_name;
            el.addEventListener('mouseenter', function () { el.style.background = 'var(--slate-100)'; });
            el.addEventListener('mouseleave', function () { el.style.background = 'transparent'; });
            el.addEventListener('click', function () {
                const lat = parseFloat(item.lat);
                const lng = parseFloat(item.lon);
                moveTo(lat, lng, true);
                searchInput.value = item.display_name;
                clearResults();
            });
            resultsBox.appendChild(el);
        });
        resultsBox.style.display = 'block';
    }

    async function doSearch(q) {
        try {
            const url = 'https://nominatim.openstreetmap.org/search?format=json&limit=5&countrycodes=id&addressdetails=1&q=' + encodeURIComponent(q);
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) { throw new Error('Request failed'); }
            renderResults(await res.json());
        } catch (err) {
            renderResults([]);
        }
    }

    searchInput.addEventListener('input', function () {
        const q = searchInput.value.trim();
        clearTimeout(searchTimer);
        if (q.length < 3) { clearResults(); return; }
        searchTimer = setTimeout(function () { doSearch(q); }, 500);
    });

    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // don't submit the form
            const q = searchInput.value.trim();
            if (q.length >= 1) {
                clearTimeout(searchTimer);
                doSearch(q);
            }
        }
    });

    document.addEventListener('click', function (e) {
        if (!resultsBox.contains(e.target) && e.target !== searchInput) {
            clearResults();
        }
    });
})();
</script>
@endpush
