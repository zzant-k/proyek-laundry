(function() {
    var mapEl = document.getElementById('orderMap');
    if (!mapEl) return;

    // Default: Indramayu (sesuai lokasi laundry)
    var defaultLat = -6.3293, defaultLng = 108.3243;
    var map = L.map('orderMap', { scrollWheelZoom: false }).setView([defaultLat, defaultLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 19
    }).addTo(map);

    var marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

    function updateCoords(lat, lng) {
        document.getElementById('cf_lat').value = lat.toFixed(7);
        document.getElementById('cf_lng').value = lng.toFixed(7);
    }

    function reverseGeocode(lat, lng) {
        updateCoords(lat, lng);
        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=18&addressdetails=1', {
            headers: { 'Accept-Language': 'id' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data && data.display_name) {
                document.getElementById('cf_alamat').value = data.display_name;
            }
        })
        .catch(function() {});
    }

    // Click on map
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        reverseGeocode(e.latlng.lat, e.latlng.lng);
    });

    // Drag marker
    marker.on('dragend', function() {
        var pos = marker.getLatLng();
        reverseGeocode(pos.lat, pos.lng);
    });

    // ─── ADDRESS SEARCH ────────────────────────────────────────────
    var searchInput = document.getElementById('mapSearchInput');
    var suggestBox  = document.getElementById('mapSearchSuggestions');
    var searchTimer = null;

    function showSuggestions(results) {
        suggestBox.innerHTML = '';
        if (!results.length) {
            suggestBox.innerHTML = '<div style="padding:10px 14px;color:#9ca3af;font-size:.83rem;">Tidak ditemukan hasil.</div>';
            suggestBox.style.display = 'block';
            return;
        }
        results.forEach(function(item) {
            var div = document.createElement('div');
            div.textContent = item.display_name;
            div.style.cssText = 'padding:10px 14px;cursor:pointer;font-size:.83rem;border-bottom:1px solid #f5f0f3;transition:background .15s;line-height:1.4;';
            div.onmouseenter = function() { this.style.background = '#fdf5f7'; };
            div.onmouseleave = function() { this.style.background = ''; };
            div.addEventListener('click', function() {
                var lat = parseFloat(item.lat);
                var lng = parseFloat(item.lon);
                map.setView([lat, lng], 17);
                marker.setLatLng([lat, lng]);
                updateCoords(lat, lng);
                document.getElementById('cf_alamat').value = item.display_name;
                searchInput.value = item.display_name;
                suggestBox.style.display = 'none';
            });
            suggestBox.appendChild(div);
        });
        suggestBox.style.display = 'block';
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        var q = this.value.trim();
        if (q.length < 3) { suggestBox.style.display = 'none'; return; }
        searchTimer = setTimeout(function() {
            fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(q) + '&limit=5&countrycodes=id', {
                headers: { 'Accept-Language': 'id' }
            })
            .then(function(r) { return r.json(); })
            .then(showSuggestions)
            .catch(function() {});
        }, 400); // debounce 400ms
    });

    // Tutup suggestion kalau klik di luar
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#mapSearchInput') && !e.target.closest('#mapSearchSuggestions')) {
            suggestBox.style.display = 'none';
        }
    });

    // Tutup dengan Escape
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') suggestBox.style.display = 'none';
    });

    // ─── GPS BUTTON ────────────────────────────────────────────────
    var _watchId = null;
    var _accuracyCircle = null;

    document.getElementById('btnGeolocate').addEventListener('click', function() {
        var btn = this;

        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung fitur geolokasi.');
            return;
        }

        // Hentikan watch sebelumnya
        if (_watchId !== null) { navigator.geolocation.clearWatch(_watchId); }

        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mencari...';
        btn.disabled = true;

        var bestAccuracy = Infinity;
        var firstFix     = true;

        // watchPosition: terus update sampai akurasi cukup baik (< 30m)
        _watchId = navigator.geolocation.watchPosition(
            function(pos) {
                var lat = pos.coords.latitude;
                var lng = pos.coords.longitude;
                var acc = pos.coords.accuracy; // dalam meter

                // Tampilkan akurasi realtime di tombol
                btn.innerHTML = '<i class="fas fa-location-dot"></i> ~' + Math.round(acc) + 'm';

                // Pertama kali dapat fix: pindahkan view peta
                if (firstFix) {
                    map.setView([lat, lng], 17);
                    firstFix = false;
                    btn.disabled = false;
                }
                marker.setLatLng([lat, lng]);

                // Gambar lingkaran radius akurasi GPS
                if (_accuracyCircle) _accuracyCircle.remove();
                _accuracyCircle = L.circle([lat, lng], {
                    radius: acc,
                    color: '#c67a89',
                    fillColor: '#f2a0b8',
                    fillOpacity: 0.15,
                    weight: 1.5
                }).addTo(map);

                // Update koordinat & alamat hanya kalau akurasi membaik
                if (acc < bestAccuracy) {
                    bestAccuracy = acc;
                    updateCoords(lat, lng);
                    reverseGeocode(lat, lng);
                }

                // Berhenti otomatis kalau sudah cukup akurat (<= 30m)
                if (acc <= 30) {
                    navigator.geolocation.clearWatch(_watchId);
                    _watchId = null;
                    btn.innerHTML = '<i class="fas fa-crosshairs"></i> GPS ';
                }
            },
            function(err) {
                var msg = 'Gagal mendapatkan lokasi.';
                if (err.code === 1) msg = 'Akses lokasi ditolak. Izinkan di pengaturan browser.';
                else if (err.code === 2) msg = 'Lokasi tidak tersedia. Pastikan GPS aktif.';
                else if (err.code === 3) msg = 'Timeout. Coba lagi di tempat lebih terbuka.';
                alert(msg);
                btn.innerHTML = '<i class="fas fa-crosshairs"></i> GPS';
                btn.disabled = false;
            },
            {
                enableHighAccuracy: true,
                timeout: 20000,
                maximumAge: 0
            }
        );
    });

    // Fix map sizing on scroll into view
    var resizeObserver = new ResizeObserver(function() { map.invalidateSize(); });
    resizeObserver.observe(mapEl);
})();
