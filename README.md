# Baxtersweb Maps

Baxtersweb Maps displays dynamic route maps from ACF Pro map marker fields.

It is built for WordPress developers and site builders who already manage structured content with Advanced Custom Fields Pro and want a clean way to display route maps without creating separate maps one by one.

## What it does

- Displays interactive OpenStreetMap maps with Leaflet.
- Reads map markers from ACF Pro repeater fields.
- Supports the ACF OpenStreetMap Field location picker.
- Connects ordered map markers with a route line.
- Displays optional points of interest as clean text-label markers.
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

Baxtersweb Maps uses external services to display map tiles and route lines. These requests are made from the visitor's browser when a map is displayed.

- OpenStreetMap Standard tiles are loaded from the OpenStreetMap Foundation tile service when the Standard map style is selected. The visitor's IP address, browser details, referring page, and requested map tile coordinates may be sent to OpenStreetMap. Service information: https://operations.osmfoundation.org/policies/tiles/ Privacy policy: https://osmfoundation.org/wiki/Privacy_Policy
- Humanitarian map tiles are loaded from the OpenStreetMap France Humanitarian tile service when the Humanitarian map style is selected. The visitor's IP address, browser details, referring page, and requested map tile coordinates may be sent to OpenStreetMap France. Service information: https://tile.openstreetmap.fr/ Project information: https://www.openstreetmap.fr/
- OpenTopoMap tiles are loaded from OpenTopoMap when the OpenTopoMap style is selected. The visitor's IP address, browser details, referring page, and requested map tile coordinates may be sent to OpenTopoMap. Service information: https://opentopomap.org/ About/credits: https://opentopomap.org/about
- OSRM demo routing service is used to request route geometry between saved map markers. Map marker coordinates are sent from the visitor's browser to https://router.project-osrm.org/ only when route drawing is enabled and at least two unique map markers exist. Project information: https://project-osrm.org/ Source and usage information: https://github.com/Project-OSRM/osrm-backend
- Leaflet JavaScript and CSS are bundled locally with the plugin. No CDN is used for Leaflet assets.

Baxtersweb Maps does not send WordPress user account data to these services.

## Source code

The plugin source code is included in this plugin package. The JavaScript and CSS files in `assets/js` and `assets/css` are human-readable source files and are not generated from a separate build step. No npm, webpack, or other build tooling is required to regenerate the shipped assets.

## License

GPL v2 or later.
