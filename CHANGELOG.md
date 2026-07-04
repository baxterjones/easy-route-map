# Changelog

## [1.0.5] - 2026-07-04
- Added map height setting with support for CSS values such as `500px`, `70vh`, and `40rem`.
- Added border radius setting.
- Added marker label setting for Stop, Point, Day, Location, or a custom label.
- Updated frontend output to use configurable map appearance variables.

## [1.0.4] - 2026-07-04
- Moved Easy Route Map from Tools to its own top-level admin menu.
- Updated generated ACF field order to Point Title, Point Description, Point Map.
- Changed generated point description field to a simpler textarea.
- Set generated map field centre to Southern Africa instead of 0,0 ocean coordinates, with zoom level 8.

## [1.0.3] - 2026-07-04

- Prevented duplicate ACF field group creation from the admin setup button.
- Disabled the setup button once the Easy Route Map field group already exists.

## [1.0.2] - 2026-07-04

### Added

- Added plugin name and version to the admin page header.
- Added a clearer requirements block and two-column setup layout.
- Added shortcode copy buttons.
- Added a field-name reference and test checklist on the admin page.
- Added an uninstall preference for keeping or removing Easy Route Map data when the plugin is deleted.

### Changed

- Set the imported OpenStreetMap field default zoom level to 8.
- Improved admin page wording and first-time setup flow.

## [1.0.1] - 2026-07-04

### Added

- Added a Settings link on the WordPress plugin list screen.
- Added one-click ACF field group setup from the Easy Route Map admin page.
- Added dependency checks for Advanced Custom Fields and ACF OpenStreetMap Field.
- Added cleaner Easy Route Map field names:
  - `erm_route_points`
  - `erm_point_title`
  - `erm_point_location`
  - `erm_point_description`
- Added setup guidance for the OpenStreetMap field return format.
- Added clearer frontend diagnostics when route points are missing or invalid.
- Added admin editor script to turn off Gutenberg fullscreen mode when possible.

### Changed

- Improved the admin page flow around requirements, field setup, adding route points, and displaying the map.
- Updated documentation to focus on getting a first result quickly.

## [1.0.0] - 2026-07-04

### Initial Easy Route Map Release

- Renamed the plugin from Itinerary Route Map to Easy Route Map.
- Added the new `[easy_route_map]` shortcode.
- Removed the old shortcode and standardised the plugin namespace to `erm`.
- Fixed renamed frontend asset paths for CSS and JavaScript.
- Updated admin wording from itinerary stops to route points.
- Updated documentation, requirements and roadmap.
- Interactive OpenStreetMap integration.
- Numbered route point markers.
- Automatic route drawing with fallback route line.
- Overlapping marker support.
- Custom marker and route colours.
- ACF repeater integration.
- Responsive layout.
- Theme-independent implementation.
