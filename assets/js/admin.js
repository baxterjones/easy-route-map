(function () {
    function bxtrString(key, fallback) {
        return window.BXTRMapsAdmin && window.BXTRMapsAdmin[key] ? window.BXTRMapsAdmin[key] : fallback;
    }

    function setupShortcodeCopyButtons() {
        var buttons = document.querySelectorAll('.bxtr-copy-shortcode');

        if (!buttons.length) {
            return;
        }

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                var targetId = button.getAttribute('data-copy-target');
                var target = targetId ? document.getElementById(targetId) : null;

                if (!target) {
                    return;
                }

                var text = target.textContent || target.innerText || '';

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(function () {
                        button.textContent = bxtrString('copied', 'Copied');
                        window.setTimeout(function () {
                            button.textContent = bxtrString('copy', 'Copy');
                        }, 1600);
                    });
                    return;
                }

                var textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.setAttribute('readonly', 'readonly');
                textarea.style.position = 'absolute';
                textarea.style.left = '-9999px';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                button.textContent = bxtrString('copied', 'Copied');
                window.setTimeout(function () {
                    button.textContent = bxtrString('copy', 'Copy');
                }, 1600);
            });
        });
    }


    function setupConfirmForms() {
        var forms = document.querySelectorAll('.bxtr-confirm-submit[data-confirm-message]');

        forms.forEach(function (form) {
            form.addEventListener('submit', function (event) {
                var message = form.getAttribute('data-confirm-message');

                if (message && !window.confirm(message)) {
                    event.preventDefault();
                }
            });
        });
    }

    function disableGutenbergFullscreen() {
        if (typeof wp === 'undefined' || !wp.domReady || !wp.data) {
            return;
        }

        wp.domReady(function () {
            var preferences = wp.data.select('core/preferences');
            var editor = wp.data.dispatch('core/edit-post');

            if (!preferences || !editor || typeof editor.toggleFeature !== 'function') {
                return;
            }

            var isFullscreen = preferences.get('core/edit-post', 'fullscreenMode');

            if (isFullscreen) {
                editor.toggleFeature('fullscreenMode');
            }
        });
    }

    function setupPreviewSync() {
        var preview = document.getElementById('bxtr-preview-map');

        if (!preview || typeof L === 'undefined') {
            return;
        }

        var markerInput = document.getElementById('bxtr_marker_color');
        var routeInput = document.getElementById('bxtr_route_color');
        var markerNumberInput = document.getElementById('bxtr_marker_number_color');
        var poiInput = document.getElementById('bxtr_poi_marker_color');
        var drawRouteInput = document.getElementById('bxtr_draw_route');
        var poiEnabledInput = document.getElementById('bxtr_poi_enabled');
        var tileInput = document.getElementById('bxtr_map_tile_style');
        var markerSequenceInput = document.getElementById('bxtr_marker_sequence');
        var previewMap = null;

        function value(input, fallback) {
            return input && input.value ? input.value : fallback;
        }

        function checked(input) {
            return !input || input.checked;
        }

        function refreshDataset() {
            preview.dataset.routeColor = value(routeInput, '#3388ff');
            preview.dataset.markerColor = value(markerInput, '#3d874d');
            preview.dataset.markerNumberColor = value(markerNumberInput, '#ffffff');
            preview.dataset.poiMarkerColor = value(poiInput, '#f59e0b');
            preview.dataset.drawRoute = checked(drawRouteInput) ? 'yes' : 'no';
            preview.dataset.tileStyle = value(tileInput, 'osm');
            preview.dataset.markerSequence = value(markerSequenceInput, 'alphabetic');
            preview.style.setProperty('--bxtr-marker-color', value(markerInput, '#3d874d'));
            preview.style.setProperty('--bxtr-marker-number-color', value(markerNumberInput, '#ffffff'));
            preview.style.setProperty('--bxtr-route-color', value(routeInput, '#3388ff'));
            preview.style.setProperty('--bxtr-poi-marker-color', value(poiInput, '#f59e0b'));
        }

        function renderPreview() {
            refreshDataset();
            if (previewMap) {
                previewMap.remove();
                previewMap = null;
            }

            preview.innerHTML = '';

            var pois = checked(poiEnabledInput)
                ? [
                    { title: bxtrString('pointOfInterest', 'Airport'), type: 'Airport', lat: -33.9715, lng: 18.6021, description: bxtrString('exampleExtraMarker', 'Example supporting point of interest.') }
                ]
                : [];

            preview.dataset.stops = JSON.stringify([
                { title: bxtrString('stopA', 'Marker One'), lat: -33.9249, lng: 18.4241, number: 1, description: bxtrString('exampleRouteStop', 'Example map marker.') },
                { title: bxtrString('stopB', 'Marker Two - Hout Bay'), lat: -34.0433, lng: 18.3489, number: 2, description: bxtrString('exampleRouteStop', 'Example map marker.') }
            ]);
            preview.dataset.pois = JSON.stringify(pois);
            preview.dataset.markerSequence = value(markerSequenceInput, 'alphabetic');

            // Reuse a tiny local preview instead of relying on the frontend initialiser running again.
            var map = L.map(preview, { scrollWheelZoom: false, zoomControl: false }).setView([-33.98, 18.47], 9);
            previewMap = map;
            var tileUrl = 'https://tile.openstreetmap.org/{z}/{x}/{y}.png';

            if (preview.dataset.tileStyle === 'hot') {
                tileUrl = 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png';
            } else if (preview.dataset.tileStyle === 'topo') {
                tileUrl = 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png';
            }

            L.tileLayer(tileUrl, { maxZoom: 18, attribution: '&copy; OpenStreetMap contributors' }).addTo(map);

            var markerSequence = value(markerSequenceInput, 'alphabetic');
            var firstLabel = markerSequence === 'numeric' ? '1' : 'A';
            var secondLabel = markerSequence === 'numeric' ? '2' : 'B';
            var routePoints = [[-33.9249, 18.4241], [-34.0433, 18.3489]];

            if (preview.dataset.drawRoute !== 'no') {
                L.polyline(routePoints, { color: preview.dataset.routeColor, weight: 4, opacity: 0.95 }).addTo(map);
            }

            var firstPreviewMarker = L.marker(routePoints[0], { icon: L.divIcon({ className: 'bxtr-marker bxtr-marker--route', html: '<span class="bxtr-marker__route"><svg class="bxtr-marker__route-svg" width="30" height="40" viewBox="0 0 18 22" aria-hidden="true" focusable="false"><path d="M18 9C18 16 9 22 9 22C9 22 0 16 0 9C3.55683e-08 6.61305 0.948211 4.32387 2.63604 2.63604C4.32387 0.948211 6.61305 0 9 0C11.3869 0 13.6761 0.948211 15.364 2.63604C17.0518 4.32387 18 6.61305 18 9Z" fill="currentColor"></path></svg><span class="bxtr-marker__number">' + firstLabel + '</span></span>', iconSize: [30, 40], iconAnchor: [15, 40], popupAnchor: [0, -38] }) }).addTo(map).bindPopup('<div class="bxtr-popup"><strong>' + bxtrString('clickedMarkerTitle', 'Example Map Marker') + '</strong><span class="bxtr-popup__title">' + bxtrString('stopA', 'Marker One') + '</span><div class="bxtr-popup__description"><p>' + bxtrString('clickedMarkerDescription', 'This is dummy popup content so you can preview the marker and popup styling.') + '</p></div></div>');
            L.marker(routePoints[1], { icon: L.divIcon({ className: 'bxtr-marker bxtr-marker--route', html: '<span class="bxtr-marker__route"><svg class="bxtr-marker__route-svg" width="30" height="40" viewBox="0 0 18 22" aria-hidden="true" focusable="false"><path d="M18 9C18 16 9 22 9 22C9 22 0 16 0 9C3.55683e-08 6.61305 0.948211 4.32387 2.63604 2.63604C4.32387 0.948211 6.61305 0 9 0C11.3869 0 13.6761 0.948211 15.364 2.63604C17.0518 4.32387 18 6.61305 18 9Z" fill="currentColor"></path></svg><span class="bxtr-marker__number">' + secondLabel + '</span></span>', iconSize: [30, 40], iconAnchor: [15, 40], popupAnchor: [0, -38] }) }).addTo(map);

            if (checked(poiEnabledInput)) {
                L.marker([-33.9715, 18.6021], { icon: L.divIcon({ className: 'bxtr-marker bxtr-marker--poi', html: '<span class="bxtr-marker__poi">Airport</span>', iconSize: null, iconAnchor: [0, 14] }) }).addTo(map);
            }

            var previewBounds = routePoints.slice();
            if (checked(poiEnabledInput)) {
                previewBounds.push([-33.9715, 18.6021]);
            }
            map.fitBounds(previewBounds, { padding: [55, 55], maxZoom: 9 });
            window.setTimeout(function () {
                map.invalidateSize();
                firstPreviewMarker.openPopup();
            }, 100);
        }

        [markerInput, markerNumberInput, routeInput, poiInput, drawRouteInput, poiEnabledInput, tileInput, markerSequenceInput].forEach(function (input) {
            if (!input) {
                return;
            }

            input.addEventListener('input', renderPreview);
            input.addEventListener('change', renderPreview);
        });

        renderPreview();
    }

    function ready() {
        setupShortcodeCopyButtons();
        setupConfirmForms();
        setupPreviewSync();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', ready);
    } else {
        ready();
    }

    disableGutenbergFullscreen();
}());
