=== Baxtersweb Maps – OpenStreetMap Route Maps for ACF ===
Contributors: baxterjones
Tags: maps, openstreetmap, route maps, acf, advanced custom fields
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.1.11
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create interactive OpenStreetMap route maps from ACF Pro repeater fields with connected routes, multiple stops, numbered markers and points of interest.

== Description ==

Baxtersweb Maps lets you create interactive OpenStreetMap route maps directly from ACF Pro repeater fields. Display connected routes, multiple stops, numbered markers and points of interest without manually creating separate maps for every page.

Simply add your locations to an ACF repeater attached to any post, page or custom post type, and Baxtersweb Maps generates a responsive Leaflet-powered map from your content.

Ideal for:

* Travel itineraries
* Safari routes
* Hiking trails
* Cycling routes
* Road trips
* Delivery routes
* Campus or facility guides
* Festival and event maps
* Property developments
* Tourism websites

Baxtersweb Maps uses OpenStreetMap and Leaflet for map rendering. If you provide a free openrouteservice API key, routes will follow real roads and paths. Without an API key, markers are connected using a dashed straight line.

= Features =

* Interactive route maps
* ACF Pro repeater support for ordered map markers
* ACF OpenStreetMap Field support for visual location picking
* Route lines between ordered points
* Optional points of interest that do not affect the route line
* Built-in Dashicon, theme-class, or plain POI markers
* Individual POI background colours
* Nearby POI grouping with click-to-spread behaviour
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

Follow the steps below to install Baxtersweb Maps on your site.

1. Search for Baxtersweb Maps, then click install and activate.
2. Install and activate Advanced Custom Fields Pro.
3. Install and activate ACF OpenStreetMap Field.
4. Go to Tools > Baxtersweb Maps.
5. Set up the ACF fields using a new or existing field group.
6. Add map markers to your content.
7. Optional: add and test an openrouteservice API key under the Routing tab.
8. Display the map using `[bxtr_map]`.

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

Baxtersweb Maps uses external services for map tiles and optional road-route calculation.

* OpenStreetMap Standard tiles are loaded in the visitor's browser from the OpenStreetMap Foundation tile service whenever a map is displayed. The visitor's IP address, browser details, referring page, and requested tile coordinates may be sent to OpenStreetMap. Tile usage policy: https://operations.osmfoundation.org/policies/tiles/ Terms of Use: https://osmfoundation.org/wiki/Terms_of_Use Privacy policy: https://osmfoundation.org/wiki/Privacy_Policy

* openrouteservice is used only when the site administrator has supplied a valid API key and road geometry needs to be calculated or recalculated. This may occur after route points are saved or changed, after an API key is successfully verified, or when an unrouted map is first rendered. The route coordinates are sent from the WordPress server to https://api.openrouteservice.org/ solely to calculate a road-following route. Returned route geometry is stored in WordPress and reused, so normal visitor views of an already calculated route do not make routing requests. Service and API information: https://openrouteservice.org/ Terms of Service: https://openrouteservice.org/terms-of-service/ Privacy policy: https://openrouteservice.org/privacy-policy/

* Leaflet JavaScript and CSS, WordPress Dashicons, and all Baxtersweb Maps plugin code are loaded locally. No icon CDN is used.

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

= 1.1.9 =
* Retains existing saved road routes when the API key is removed.
* Automatically calculates previously unrouted maps when a valid API key is connected.
* Documents routing lifecycle behaviour on the Routing tab.
* Resolves the final Plugin Check translator-comment warning.
Resolves Plugin Check findings without changing plugin behaviour.

== Changelog ==

= 1.1.11 =
* Updated Name and readme

= 1.1.10 =
* Fixed per-POI background colours from ACF fields, including shorthand and values entered without a leading hash.
* POI clusters now retain a shared custom colour when all grouped POIs use the same colour.

= 1.1.9 =
* Existing saved road routes remain available when the API key is removed.
* Maps without saved road geometry are calculated when a valid API key is connected.
* Added routing lifecycle guidance beneath the API key field.
* Fixed the final Plugin Check translator-comment finding.

= 1.1.7 =
* Fixed global POI background colours on preview and frontend markers.
* Clearing an API key removes cached road geometry and restores fallback lines.
* Route visibility and marker sequence update immediately in the style preview.
* Adjusted marker B's preview popup position.

= 1.1.6 =
* Retries rural route points with a bounded 2 km road-snapping radius when the default 350 metre search fails.
* Keeps the exact marker location while routing to the nearest mapped drivable road within that limit.

= 1.1.4 =
* Fixed road-following routes being calculated but not displayed when cached geometry was missing.
* Moved route display and route colour into the Styles tab.
* Renamed Markers & POIs to Styles.
* Improved the admin preview popup position and zoom.

= 1.1.4 =
* Added a persistent openrouteservice connection status below the API key.
* Replaced the separate save and test controls with Save & Test API.
* Fixed existing maps remaining on dashed fallback lines after connecting the routing API.
* Opens a route marker popup automatically in the admin preview.
* Updated documentation and demo links.
* Improved translation readiness for frontend and admin JavaScript strings.
* Updated external-service documentation for WordPress.org review.

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
