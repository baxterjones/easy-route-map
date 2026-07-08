# Changelog

## 1.0.6
- Corrected admin header Docs, Demo, and Plugin links.
- Added the requested POI icon key using the selected Unicode symbols plus P for parking and $ for cash.
- Moved the preview POI marker further away from map marker 2.
- Changed route markers from numbers to letters for cleaner route labels.
- Collapsed exact duplicate route coordinates into one combined marker, with the popup listing each route stop.
- Kept normal markers upright and only applies visual leaning when markers are genuinely close.
- Fixed the route marker drop shape so the pin tip completes the marker shape.
- Added clearer loop usage guidance under Display the map, including the Advanced Views Layout shortcode using {{ _layout.object_id }}.
- Improved shortcode post ID fallback handling for more loop/template contexts.
- Tightened map height validation to avoid percentage heights that can break Leaflet sizing.
- Cleaned admin style override wording and version badge spacing.

## 1.0.5
- Improved route colour setting.
- Improved overlapping marker handling.
- Added fallback route line when OSRM routing is unavailable.
