document.addEventListener('DOMContentLoaded', function () {
    const mapEl = document.getElementById('irm-map');

    if (!mapEl) {
        return;
    }

    if (typeof L === 'undefined') {
        console.error('Easy Route Map: Leaflet is not loaded.');
        return;
    }

    let stops = [];

    try {
        stops = JSON.parse(mapEl.dataset.stops || '[]');
    } catch (error) {
        console.error('Easy Route Map: invalid route point data.', error);
        return;
    }

    if (!stops.length) {
        return;
    }

    const configuredRouteColor = mapEl.dataset.routeColor || '#3388ff';
    const routeColor = /^#[0-9a-f]{6}$/i.test(configuredRouteColor) ? configuredRouteColor : '#3388ff';

    function getNearbyOffsetData(points, map) {
        const threshold = 42;
        const grouped = new Array(points.length).fill(false);
        const offsets = new Array(points.length).fill(0);

        points.forEach(function (point, index) {
            if (grouped[index]) {
                return;
            }

            const basePixel = map.latLngToLayerPoint([point.lat, point.lng]);
            const group = [index];
            grouped[index] = true;

            points.forEach(function (candidate, candidateIndex) {
                if (candidateIndex === index || grouped[candidateIndex]) {
                    return;
                }

                const candidatePixel = map.latLngToLayerPoint([candidate.lat, candidate.lng]);
                const distance = basePixel.distanceTo(candidatePixel);

                if (distance <= threshold) {
                    group.push(candidateIndex);
                    grouped[candidateIndex] = true;
                }
            });

            if (group.length <= 1) {
                return;
            }

            const spacing = 22;
            const start = -((group.length - 1) * spacing) / 2;

            group.forEach(function (pointIndex, groupIndex) {
                offsets[pointIndex] = start + (groupIndex * spacing);
            });
        });

        return offsets;
    }

    function createMarkerIcon(dayNumber, offset) {
        let leanClass = '';

        if (offset < 0) {
            leanClass = ' irm-marker__pin--left';
        } else if (offset > 0) {
            leanClass = ' irm-marker__pin--right';
        }

        return L.divIcon({
            className: 'irm-marker',
            html: '<span class="irm-marker__pin' + leanClass + '" style="--irm-marker-offset-x:' + offset + 'px;"><span class="irm-marker__number">' + dayNumber + '</span></span>',
            iconSize: [46, 52],
            iconAnchor: [23, 46],
            popupAnchor: [offset, -46]
        });
    }

    const firstStop = stops[0];

    const map = L.map(mapEl, {
        scrollWheelZoom: false
    }).setView([firstStop.lat, firstStop.lng], firstStop.zoom || 8);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const points = [];

    stops.forEach(function (stop, index) {
        const lat = parseFloat(stop.lat);
        const lng = parseFloat(stop.lng);

        if (Number.isNaN(lat) || Number.isNaN(lng)) {
            return;
        }

        points.push({
            lat: lat,
            lng: lng,
            title: stop.title || '',
            description: stop.description || '',
            dayNumber: index + 1
        });
    });

    if (!points.length) {
        return;
    }

    const bounds = points.map(function (point) {
        return [point.lat, point.lng];
    });

    function fitToBounds(targetBounds) {
        if (bounds.length > 1) {
            map.fitBounds(targetBounds || bounds, {
                padding: [40, 40]
            });
        } else {
            map.setView(bounds[0], firstStop.zoom || 10);
        }
    }

    function addMarkers() {
        const offsets = getNearbyOffsetData(points, map);

        points.forEach(function (point, index) {
            let popup = '<div class="irm-popup"><strong>Point ' + point.dayNumber + '</strong>';

            if (point.description) {
                popup += '<div class="irm-popup__description">' + point.description + '</div>';
            }

            popup += '</div>';

            L.marker([point.lat, point.lng], {
                icon: createMarkerIcon(point.dayNumber, offsets[index] || 0),
                zIndexOffset: 1000 + point.dayNumber
            })
                .addTo(map)
                .bindPopup(popup);
        });
    }

    function addFallbackLine() {
        if (bounds.length <= 1) {
            return null;
        }

        return L.polyline(bounds, {
            color: routeColor,
            weight: 3,
            opacity: 0.8
        }).addTo(map);
    }

    let fallbackLine = null;

    if (bounds.length > 1) {
        // Add a fallback immediately so the user always sees a route line,
        // even if the external OSRM request fails or is blocked.
        fallbackLine = addFallbackLine();
        fitToBounds();

        const osrmCoords = bounds
            .map(function (point) {
                return point[1] + ',' + point[0]; // lng,lat
            })
            .join(';');

        fetch('https://router.project-osrm.org/route/v1/driving/' + osrmCoords + '?overview=full&geometries=geojson')
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('OSRM request failed.');
                }

                return response.json();
            })
            .then(function (data) {
                if (!data.routes || !data.routes.length) {
                    throw new Error('No OSRM route found.');
                }

                const routeLayer = L.geoJSON(data.routes[0].geometry, {
                    style: {
                        color: routeColor,
                        weight: 4,
                        opacity: 0.95
                    }
                }).addTo(map);

                if (fallbackLine) {
                    map.removeLayer(fallbackLine);
                    fallbackLine = null;
                }

                map.fitBounds(routeLayer.getBounds(), {
                    padding: [40, 40]
                });

                addMarkers();
            })
            .catch(function (error) {
                console.warn('Easy Route Map: using fallback route line.', error);
                addMarkers();
            });
    } else {
        fitToBounds();
        addMarkers();
    }
});
