# Baxtersweb Maps

Baxtersweb Maps displays dynamic route maps from ACF Pro map marker fields.

It is built for WordPress developers and site builders who already manage structured content with Advanced Custom Fields Pro and want a clean way to display route maps without creating separate maps one by one.

## What it does

- Displays interactive OpenStreetMap maps with Leaflet.
- Reads map markers from ACF Pro repeater fields.
- Supports the ACF OpenStreetMap Field location picker.
- Connects ordered map markers with a route line.
- Displays POIs using built-in WordPress icons, theme icon classes, or plain markers.
- Supports per-POI background colours and nearby-marker grouping.
- Supports posts, pages, and custom post types.
- Includes one-click ACF field setup.
- Provides simple style settings for colours, map height, border radius, and marker labels.
- Bundles Leaflet locally with the plugin.

## Requirements

Baxtersweb Maps currently requires:

- WordPress 6.0 or newer
- PHP 7.4 or newer
- Advanced Custom Fields Pro
- ACF OpenStreetMap Field

The ACF OpenStreetMap field should use **Return Format: Raw data** and should allow **one marker per row**.

## Basic usage

1. Install and activate the required plugins.
2. Install and activate Baxtersweb Maps.
3. Go to **Tools → Baxtersweb Maps**.
4. Click **Set up ACF fields for me**.
5. Edit a selected post type and add map markers.
6. Add the shortcode where the map should display.

```text
[bxtr_map]
```

## Template usage

When outputting maps inside templates or loops, pass the current post ID into the shortcode.

PHP template:

```php
echo do_shortcode('[bxtr_map id="' . get_the_ID() . '"]');
```

Advanced Views Layout:

```twig
[bxtr_map id="{{ _layout.object_id }}"]
```

## Layer controls

Show route and POI layers:

```text
[bxtr_map route="yes" poi="yes"]
```

Show only POI markers:

```text
[bxtr_map route="no" poi="yes"]
```

Show only the route layer:

```text
[bxtr_map route="yes" poi="no"]
```

## External services

Baxtersweb Maps uses external services for map tiles and optional road-route calculation.

* OpenStreetMap Standard tiles are loaded in the visitor's browser from the OpenStreetMap Foundation tile service whenever a map is displayed. The visitor's IP address, browser details, referring page, and requested tile coordinates may be sent to OpenStreetMap. Tile usage policy: https://operations.osmfoundation.org/policies/tiles/ Terms of Use: https://osmfoundation.org/wiki/Terms_of_Use Privacy policy: https://osmfoundation.org/wiki/Privacy_Policy

* openrouteservice is used only when the site administrator has supplied a valid API key and road geometry needs to be calculated or recalculated. This may occur after route points are saved or changed, after an API key is successfully verified, or when an unrouted map is first rendered. The route coordinates are sent from the WordPress server to https://api.openrouteservice.org/ solely to calculate a road-following route. Returned route geometry is stored in WordPress and reused, so normal visitor views of an already calculated route do not make routing requests. Service and API information: https://openrouteservice.org/ Terms of Service: https://openrouteservice.org/terms-of-service/ Privacy policy: https://openrouteservice.org/privacy-policy/

* Leaflet JavaScript and CSS, WordPress Dashicons, and all Baxtersweb Maps plugin code are loaded locally. No icon CDN is used.

Baxtersweb Maps does not send WordPress user account data to these services.

## Source code

The plugin source code is included in this plugin package. The JavaScript and CSS files in `assets/js` and `assets/css` are human-readable source files and are not generated from a separate build step. No npm, webpack, or other build tooling is required to regenerate the shipped assets.

## License

GPL v2 or later.


## 1.1.6
- Added a bounded 2 km road-snapping retry for rural itinerary points.

## Routing lifecycle

Saved routes remain available when an API key is removed. Adding a valid key later calculates maps that do not yet have saved road geometry. New or changed routes fall back to dashed straight lines whenever routing is unavailable.
