=== Easy Route Map ===
Contributors: baxtersweb
Tags: route map, openstreetmap, leaflet, acf, travel, itinerary, maps
Requires at least: 6.7
Tested up to: 6.7
Requires PHP: 8.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create interactive OpenStreetMap route maps from route point fields in WordPress.

== Description ==

Easy Route Map is a lightweight WordPress plugin for creating interactive route maps with OpenStreetMap and Leaflet.

It is built for route-based content: tours, safaris, travel itineraries, trails, delivery routes, event routes and other multi-stop journeys.

Most map plugins are embed-first: create a map somewhere, copy a shortcode, then paste it into a page.

Easy Route Map is content-first: route points live on the post, page, or custom post type entry, and the map is generated from that content.

== Features ==

* Interactive OpenStreetMap maps
* Leaflet-powered frontend display
* Numbered route point markers
* Automatic route drawing with fallback route line
* Smart overlapping marker support
* Custom marker and route colours
* Responsive design
* ACF repeater integration
* Lightweight and theme independent

== Current Requirements ==

* WordPress 6.7+
* PHP 8.1+
* Advanced Custom Fields (ACF)
* ACF OpenStreetMap Field

This first version keeps the original ACF-based workflow. Built-in route point fields without an ACF dependency are planned for a future version.

== Shortcode ==

Use the shortcode on a post, page, or custom post type entry that contains route point data:

[easy_route_map]

Target a specific post ID:

[easy_route_map id="123"]


== Expected ACF Fields ==

This version expects the existing field names below:

* itinerary_day_items - Repeater. Each row is treated as one route point.
* itinerary_day_item_title - Text. Route point title.
* itinerary_day_item_location_coordinates - OpenStreetMap. Marker coordinates.
* itinerary_day_item_location_description - Textarea / WYSIWYG / Text. Optional popup description.

== Roadmap ==

* Built-in route point fields without requiring ACF
* Visual pin picker for adding route points directly on a map
* Setup wizard for first-time users
* Optional ACF/custom field mapping
* Additional marker styles and route display options

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/` or install the plugin zip from the WordPress admin area.
2. Activate Easy Route Map.
3. Add the expected ACF route point fields to the post type where you want maps.
4. Add `[easy_route_map]` to display the map.

== Changelog ==

= 1.0.0 =
* Initial Easy Route Map release.
* Interactive OpenStreetMap integration.
* Numbered route point markers.
* Automatic route drawing with fallback route line.
* Overlapping marker support.
* Custom marker and route colours.
* ACF repeater integration.
* Responsive layout.
* Theme-independent implementation.
