document.addEventListener('DOMContentLoaded', function () {
    const mapEls = document.querySelectorAll('.bxtr-map');

    if (!mapEls.length) {
        return;
    }

    if (typeof L === 'undefined') {
        console.error('Baxtersweb Maps: Leaflet is not loaded.');
        return;
    }

    const tileLayers = {
        osm: {
            url: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
            options: { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' }
        },
        hot: {
            url: 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png',
            options: { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors, Tiles style by Humanitarian OpenStreetMap Team' }
        },
        topo: {
            url: 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
            options: { maxZoom: 17, attribution: '&copy; OpenStreetMap contributors, SRTM | Map style: &copy; OpenTopoMap' }
        }
    };

    function safeJson(value, fallback) {
        try {
            return JSON.parse(value || '[]');
        } catch (error) {
            console.error('Baxtersweb Maps: invalid map point data.', error);
            return fallback;
        }
    }

    function isHex(value, fallback) {
        return /^#[0-9a-f]{6}$/i.test(value || '') ? value : fallback;
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function normalisePoints(items, kind) {
        const points = [];

        items.forEach(function (item, index) {
            const lat = parseFloat(item.lat);
            const lng = parseFloat(item.lng);

            if (Number.isNaN(lat) || Number.isNaN(lng)) {
                return;
            }

            points.push({
                lat: lat,
                lng: lng,
                title: item.title || '',
                description: item.description || '',
                type: item.type || '',
                zoom: parseInt(item.zoom || '10', 10),
                number: item.number || index + 1,
                kind: kind
            });
        });

        return points;
    }

    function coordinateKey(point) {
        return point.lat.toFixed(6) + ',' + point.lng.toFixed(6);
    }

    function uniqueCoordinateCount(points) {
        const keys = {};

        points.forEach(function (point) {
            keys[coordinateKey(point)] = true;
        });

        return Object.keys(keys).length;
    }

    function uniqueRoutePoints(points) {
        const seen = {};
        const unique = [];

        points.forEach(function (point) {
            const key = coordinateKey(point);

            if (seen[key]) {
                return;
            }

            seen[key] = true;
            unique.push(point);
        });

        return unique;
    }

    function routeLabel(index, sequence) {
        if (sequence === 'numeric') {
            return String(index + 1);
        }
        let label = '';
        let value = index + 1;

        while (value > 0) {
            value--;
            label = String.fromCharCode(65 + (value % 26)) + label;
            value = Math.floor(value / 26);
        }

        return label;
    }

    function labelRange(points) {
        const labels = points.map(function (point) {
            return point.label;
        }).filter(Boolean);

        if (!labels.length) {
            return '';
        }

        if (labels.length === 1) {
            return labels[0];
        }

        if (labels.length === 2) {
            return labels.join('+');
        }

        return labels[0] + '-' + labels[labels.length - 1];
    }

    function groupedRouteMarkers(points, sequence) {
        const groups = {};
        const markers = [];

        points.forEach(function (point, index) {
            point.label = routeLabel(index, sequence);
            point.number = point.label;

            const key = coordinateKey(point);

            if (!groups[key]) {
                groups[key] = {
                    lat: point.lat,
                    lng: point.lng,
                    title: point.title || '',
                    description: point.description || '',
                    type: point.type || '',
                    zoom: point.zoom,
                    kind: 'route',
                    children: []
                };
                markers.push(groups[key]);
            }

            groups[key].children.push(point);
        });

        markers.forEach(function (marker) {
            marker.label = labelRange(marker.children);
            marker.number = marker.label;
            marker.isCombined = marker.children.length > 1;
        });

        return markers;
    }

    function createRouteIcon(label) {
        const safeLabel = escapeHtml(label);

        return L.divIcon({
            className: 'bxtr-marker bxtr-marker--route',
            html: '<span class="bxtr-marker__route"><svg class="bxtr-marker__route-svg" width="30" height="40" viewBox="0 0 18 22" aria-hidden="true" focusable="false"><path d="M18 9C18 16 9 22 9 22C9 22 0 16 0 9C3.55683e-08 6.61305 0.948211 4.32387 2.63604 2.63604C4.32387 0.948211 6.61305 0 9 0C11.3869 0 13.6761 0.948211 15.364 2.63604C17.0518 4.32387 18 6.61305 18 9Z" fill="currentColor"></path></svg><span class="bxtr-marker__number">' + safeLabel + '</span></span>',
            iconSize: [30, 40],
            iconAnchor: [15, 40],
            popupAnchor: [0, -38]
        });
    }

    function poiLabel(point, fallback) {
        return (point.type || point.title || fallback || 'POI').trim();
    }

    function createPoiIcon(point, fallbackLabel) {
        const label = poiLabel(point, fallbackLabel);

        return L.divIcon({
            className: 'bxtr-marker bxtr-marker--poi',
            html: '<span class="bxtr-marker__poi">' + escapeHtml(label) + '</span>',
            iconSize: null,
            iconAnchor: [0, 14],
            popupAnchor: [0, -14]
        });
    }

    function popupHtml(point, poiLabelFallback) {
        let heading = '';

        if (point.kind === 'route') {
            heading = point.title || ('Marker ' + point.number);
        } else if (point.title) {
            heading = point.title;
        } else if (point.type) {
            heading = point.type;
        } else {
            heading = poiLabelFallback || 'Point of Interest';
        }

        let popup = '<div class="bxtr-popup"><strong>' + escapeHtml(heading) + '</strong>';

        if (point.kind === 'route' && point.isCombined && point.children && point.children.length) {
            popup += '<ul class="bxtr-popup__combined">';
            point.children.forEach(function (child) {
                const childTitle = child.title || ('Marker ' + child.label);
                popup += '<li><strong>' + escapeHtml(child.label) + '.</strong> ' + escapeHtml(childTitle);
                if (child.description) {
                    popup += '<div class="bxtr-popup__description">' + child.description + '</div>';
                }
                popup += '</li>';
            });
            popup += '</ul>';
            popup += '</div>';
            return popup;
        }

        if (point.kind === 'poi' && point.type && point.title) {
            popup += '<span class="bxtr-popup__type">' + escapeHtml(point.type) + '</span>';
        }

        if (point.title && point.kind === 'route') {
            popup += '<span class="bxtr-popup__title">' + escapeHtml(point.title) + '</span>';
        }

        if (point.description) {
            popup += '<div class="bxtr-popup__description">' + point.description + '</div>';
        }

        popup += '</div>';
        return popup;
    }

    mapEls.forEach(function (mapEl) {
        const markerSequence = mapEl.dataset.markerSequence === 'numeric' ? 'numeric' : 'alphabetic';
        const stops = normalisePoints(safeJson(mapEl.dataset.stops, []), 'route');
        const routeMarkers = groupedRouteMarkers(stops, markerSequence);
        const pois = normalisePoints(safeJson(mapEl.dataset.pois, []), 'poi');
        const allPoints = routeMarkers.concat(pois);

        if (!allPoints.length) {
            return;
        }

        const routeColor = isHex(mapEl.dataset.routeColor, '#3388ff');
        const poiLabelFallback = (mapEl.dataset.poiLabel || 'Point of Interest').trim() || 'Point of Interest';
        const drawRoute = mapEl.dataset.drawRoute !== 'no';
        const tileStyle = tileLayers[mapEl.dataset.tileStyle] ? mapEl.dataset.tileStyle : 'osm';
        const firstPoint = allPoints[0];

        const map = L.map(mapEl, {
            scrollWheelZoom: false
        }).setView([firstPoint.lat, firstPoint.lng], firstPoint.zoom || 8);

        L.tileLayer(tileLayers[tileStyle].url, tileLayers[tileStyle].options).addTo(map);

        const bounds = allPoints.map(function (point) {
            return [point.lat, point.lng];
        });

        function fitToBounds(targetBounds) {
            const pointsToFit = targetBounds || bounds;

            if (pointsToFit.length > 1 && uniqueCoordinateCount(allPoints) > 1) {
                map.fitBounds(pointsToFit, { padding: [40, 40] });
            } else {
                map.setView(bounds[0], firstPoint.zoom || 10);
            }
        }

        let markersAdded = false;

        function addMarkers() {
            if (markersAdded) {
                return;
            }

            markersAdded = true;

            allPoints.forEach(function (point, index) {
                const icon = point.kind === 'route'
                    ? createRouteIcon(point.number)
                    : createPoiIcon(point, poiLabelFallback);

                L.marker([point.lat, point.lng], {
                    icon: icon,
                    zIndexOffset: point.kind === 'route' ? 1000 + index : 500
                }).addTo(map).bindPopup(popupHtml(point, poiLabelFallback));
            });
        }

        function addFallbackLine(routePoints) {
            if (!drawRoute || routePoints.length <= 1) {
                return null;
            }

            const routeBounds = routePoints.map(function (point) {
                return [point.lat, point.lng];
            });

            return L.polyline(routeBounds, {
                color: routeColor,
                weight: 3,
                opacity: 0.8
            }).addTo(map);
        }

        const routableStops = uniqueRoutePoints(stops);

        if (drawRoute && routableStops.length > 1) {
            let fallbackLine = addFallbackLine(routableStops);
            fitToBounds();
            addMarkers();

            const osrmCoords = routableStops.map(function (point) {
                return point.lng + ',' + point.lat;
            }).join(';');

            const controller = typeof AbortController !== 'undefined' ? new AbortController() : null;
            const timeoutId = controller ? window.setTimeout(function () { controller.abort(); }, 5000) : null;

            fetch('https://router.project-osrm.org/route/v1/driving/' + osrmCoords + '?overview=full&geometries=geojson', controller ? { signal: controller.signal } : {})
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('OSRM request failed.');
                    }
                    return response.json();
                })
                .then(function (data) {
                    if (timeoutId) {
                        window.clearTimeout(timeoutId);
                    }

                    if (!data.routes || !data.routes.length) {
                        throw new Error('No OSRM route found.');
                    }

                    const routeLayer = L.geoJSON(data.routes[0].geometry, {
                        style: { color: routeColor, weight: 4, opacity: 0.95 }
                    }).addTo(map);

                    if (fallbackLine) {
                        map.removeLayer(fallbackLine);
                        fallbackLine = null;
                    }

                    const combinedBounds = routeLayer.getBounds();
                    allPoints.forEach(function (point) {
                        combinedBounds.extend([point.lat, point.lng]);
                    });
                    map.fitBounds(combinedBounds, { padding: [40, 40] });
                    addMarkers();
                })
                .catch(function (error) {
                    if (timeoutId) {
                        window.clearTimeout(timeoutId);
                    }
                    console.warn('Baxtersweb Maps: using fallback route line.', error);
                    addMarkers();
                });
        } else {
            fitToBounds();
            addMarkers();
        }
    });
});
