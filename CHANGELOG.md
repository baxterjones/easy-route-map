# Changelog

## 1.1.11
- Updated Name and readme


## 1.1.10

- Fixed per-POI background colours from ACF fields, including shorthand and values entered without a leading hash.
- POI clusters now retain a shared custom colour when all grouped POIs use the same colour.


## 1.1.9

- Retain saved road geometry when the routing API key is removed.
- Explain routing lifecycle behaviour beneath the API key settings.
- Fix the remaining Plugin Check translator-comment warning.
- Retained all previously calculated road geometry when an API key is removed.
- Existing maps without road geometry are calculated after a valid key is connected.

## 1.1.7
- Fixed global POI background colours on preview and frontend markers.
- Clearing and testing an empty API key now removes cached road geometry and restores straight fallback lines.
- Route visibility and marker sequence now update immediately in the style preview.
- Adjusted the preview position so marker B's popup is fully visible.

## 1.1.6
- Added a bounded 2 km road-snapping retry for rural itinerary points that fall outside openrouteservice's default 350 metre radius.

## 1.1.5
- Added leg-by-leg routing fallback for longer multi-stop itineraries when a single openrouteservice request cannot be calculated.
- Added an editor-only routing error message when road geometry cannot be returned.
- Increased the admin preview zoom by two levels while keeping marker B's popup visible.

## 1.1.4
- Fixed missing road-following route geometry at display time.
- Moved route display and colour into Styles.
- Renamed the styling tab and improved the preview popup.

# Changelog

## 1.1.3
- Added persistent openrouteservice connection status and a combined Save & Test API action.
- Recalculated existing maps after a successful API test so road geometry replaces dashed fallback lines.
- Opened a route marker popup automatically in the admin preview.
- Updated documentation and demo links.
- Improved translation readiness for PHP and JavaScript interface strings.
- Updated WordPress.org readme service disclosures and release notes.

## 1.1.2
- Reworked the Overview screen around a clear first-map workflow and concise product explanation.
- Added the choice to create a dedicated Baxtersweb Maps field group or add the maintained map fields to an existing ACF field group.
- Added field-group status and contextual setup feedback without introducing field-by-field mapping.
- Added prominent, contextual Advanced Views integration guidance for archive cards, layouts and post loops.
- Expanded `[bxtr_map]` usage examples and removed the separate technical field-name list from the interface.
- Added direct dependency and integration links, while keeping routing and Advanced Views clearly optional.

## 1.1.1
- Refined the tabbed admin interface and restored version, documentation and demo links.
- Simplified setup status and ACF field creation controls.
- Added global POI marker-type defaults to reduce editor fields.
- Added live marker and colour previews with hex fields and native colour selectors.
- Removed Humanitarian and Topographic tile choices; maps now use standard OpenStreetMap tiles.
- Moved uninstall handling to Help & Data.

## 1.1.0
- Replaced browser-side OSRM demo requests with server-side openrouteservice routing using the site owner’s API key.
- Cached route geometry in post meta and retained a dashed straight-line fallback until a road route is calculated.
- Added Overview, Routing, Markers & POIs, and Help tabs.
- Added connection testing and clearer routing setup status.
- Added built-in WordPress Dashicons, theme icon classes, plain POI markers, and per-POI background colours.
- Added nearby POI grouping with click-to-zoom and click-to-spread behaviour.
- Replaced exact-position A+B route markers with separate fanned markers.
- Removed the SVG route marker dependency.
- Updated external-service disclosures.

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
