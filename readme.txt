=== Baxtersweb Maps ===
Contributors: baxterjones
Tags: maps, openstreetmap, leaflet, routes, acf
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create interactive route maps and points of interest from ACF Pro fields.

== Description ==

Baxtersweb Maps helps WordPress developers and site builders display route maps from structured ACF Pro field data.

Instead of creating a separate map and embedding it manually, you add map markers directly to posts, pages, or custom post types. Baxtersweb Maps then displays those points as an interactive OpenStreetMap route map using Leaflet.

Baxtersweb Maps is useful for:

* Travel itineraries
* Safari routes
* Hiking trails
* Cycling routes
* Road trips
* Delivery routes
* Multi-location guides
* Festival or venue maps

Baxtersweb Maps uses OpenStreetMap and Leaflet, so no Google Maps API key is required.

= Features =

* Interactive route maps
* ACF Pro repeater support for ordered map markers
* ACF OpenStreetMap Field support for visual location picking
* Route lines between ordered points
* Optional points of interest that do not affect the route line
* Text-label POI markers for flexible map labels
* Alphabetical or numbered route markers
* Duplicate map marker handling
* Custom route colour, marker colour, marker text colour, POI colour, map height, border radius, and marker numbering settings
* Works with posts, pages, and custom post types
* One-click ACF field setup
* Leaflet bundled locally with the plugin

= Requirements =

Baxtersweb Maps currently requires:

* Advanced Custom Fields Pro
* ACF OpenStreetMap Field

The ACF OpenStreetMap field should use Return Format: Raw data and should allow one marker per row.

== Demo ==

Check out the [Baxtersweb Maps demo page](https://baxtersweb.com/baxtersweb-maps-demo/) to see example route maps, points of interest, and styling options.

== Documentation ==

Read the [Baxtersweb Maps documentation](https://baxtersweb.com/baxtersweb-maps-docs/) for setup instructions, shortcode usage, and developer examples.

== Installation ==

1. Upload and activate Baxtersweb Maps.
2. Install and activate Advanced Custom Fields Pro.
3. Install and activate ACF OpenStreetMap Field.
4. Go to Tools > Baxtersweb Maps.
5. Click Set up ACF fields for me.
6. Add map markers to your content.
7. Display the map using `[bxtr_map]`.

== Frequently Asked Questions ==

= Do I need a Google Maps API key? =

No. Baxtersweb Maps uses OpenStreetMap and Leaflet.

= Does this work with custom post types? =

Yes. Baxtersweb Maps works with posts, pages, and custom post types selected during field setup.

= Does this require ACF Pro? =

Yes. Baxtersweb Maps currently uses ACF Pro repeater fields for map markers and optional points of interest.

= Can I use it inside templates or loops? =

Yes. Pass the current post ID into the shortcode.

PHP template example:

`echo do_shortcode('[bxtr_map id="' . get_the_ID() . '"]');`

Advanced Views Layout example:

`[bxtr_map id="{{ _layout.object_id }}"]`

= Can I show only points of interest? =

Yes. Use `[bxtr_map route="no" poi="yes"]`.

= Can I customise the map appearance? =

Yes. You can change the route colour, marker colour, marker text colour, POI colour, map height, border radius, and marker numbering from the settings.

For map height, include a CSS unit such as `500px`, `70vh`, or `40rem`. Percentage heights are intentionally avoided because Leaflet maps often cannot calculate them unless the parent container has a fixed height.

== External Services ==

Baxtersweb Maps uses external services to display map tiles and route lines. These requests are made from the visitor's browser when a map is displayed.

* OpenStreetMap Standard tiles are loaded from the OpenStreetMap Foundation tile service when the Standard map style is selected. The visitor's IP address, browser details, referring page, and requested map tile coordinates may be sent to OpenStreetMap. Service information: https://operations.osmfoundation.org/policies/tiles/ Privacy policy: https://osmfoundation.org/wiki/Privacy_Policy
* Humanitarian map tiles are loaded from the OpenStreetMap France Humanitarian tile service when the Humanitarian map style is selected. The visitor's IP address, browser details, referring page, and requested map tile coordinates may be sent to OpenStreetMap France. Service information: https://tile.openstreetmap.fr/ Project information: https://www.openstreetmap.fr/
* OpenTopoMap tiles are loaded from OpenTopoMap when the OpenTopoMap style is selected. The visitor's IP address, browser details, referring page, and requested map tile coordinates may be sent to OpenTopoMap. Service information: https://opentopomap.org/ About/credits: https://opentopomap.org/about
* OSRM demo routing service is used to request route geometry between saved map markers. Map marker coordinates are sent from the visitor's browser to https://router.project-osrm.org/ only when route drawing is enabled and at least two unique map markers exist. Project information: https://project-osrm.org/ Source and usage information: https://github.com/Project-OSRM/osrm-backend
* Leaflet JavaScript and CSS are bundled locally with the plugin. No CDN is used for Leaflet assets.

Baxtersweb Maps does not send WordPress user account data to these services.

== Source Code ==

The plugin source code is included in this plugin package. The JavaScript and CSS files in assets/js and assets/css are human-readable source files and are not generated from a separate build step. No npm, webpack, or other build tooling is required to regenerate the shipped assets.

== Screenshots ==

1. Create interactive route maps with custom markers and points of interest.
2. Add titles and descriptions to each location marker.
3. Manage map content using simple Advanced Custom Fields repeaters.
4. Quickly create the required fields and confirm your map setup.
5. Customise marker colours, labels and preview your map styles.
6. Display maps anywhere using a simple shortcode.

== Upgrade Notice ==

= 1.0.6 =
Initial public release of Baxtersweb Maps.

== Changelog ==

= 1.0.6 =
* Moved the settings screen under Tools.
* Bundled Leaflet locally instead of loading it from a CDN.
* Replaced POI icon markers with clean text-label markers.
* Added ACF Pro requirement wording.
* Added route marker text colour setting.
* Changed route markers to letter labels.
* Improved duplicate map marker handling.
* Added clearer template and loop shortcode guidance.
* Added Advanced Views Layout shortcode example.
* Added external service disclosure for map tiles and OSRM routing.
* Improved Plugin Check compatibility around nonces, sanitisation, direct file access, and translator comments.

= 1.0.5 =
* Added map height setting.
* Added border radius setting.
* Added marker label setting with custom label support.

= 1.0.4 =
* Improved setup experience.
* Added dedicated admin screen.
* Improved generated field layout.
* Improved default map settings.

= 1.0.3 =
* Prevented duplicate field setup.

= 1.0.2 =
* Improved onboarding.
* Added uninstall data preference.

= 1.0.1 =
* Added automated ACF field setup.

= 1.0.0 =
* Initial release.
