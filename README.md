# 🧭 Easy Route Map

A lightweight WordPress plugin for creating interactive route maps with **OpenStreetMap** and **Leaflet**.

Easy Route Map is built for route-based content: tours, safaris, travel itineraries, trails, delivery routes, event routes and other multi-stop journeys.

---

## Why Easy Route Map?

Most map plugins are embed-first: create a map somewhere, copy a shortcode, then paste it into a page.

Easy Route Map is content-first: route points live on the post, page, or custom post type entry, and the map is generated from that content.

---

## Features

- Interactive OpenStreetMap maps
- Leaflet-powered frontend display
- Numbered route point markers
- Automatic route drawing with fallback route line
- Smart overlapping marker support
- Custom marker and route colours
- Responsive design
- ACF repeater integration
- Lightweight and theme independent

---

## Current Requirements

- WordPress 6.7+
- PHP 8.1+
- Advanced Custom Fields (ACF)
- ACF OpenStreetMap Field

This first version keeps the original ACF-based workflow. Built-in route point fields without an ACF dependency are planned for a future version.

---

## Shortcode

Use the shortcode on a post, page, or custom post type entry that contains route point data:

```text
[easy_route_map]
```

Target a specific post ID:

```text
[easy_route_map id="123"]
```


---

## Expected ACF Fields

This version expects the existing field names below:

| Field | Type | Purpose |
| --- | --- | --- |
| `itinerary_day_items` | Repeater | Each row is treated as one route point. |
| `itinerary_day_item_title` | Text | Route point title. |
| `itinerary_day_item_location_coordinates` | OpenStreetMap | Marker coordinates. |
| `itinerary_day_item_location_description` | Textarea / WYSIWYG / Text | Optional popup description. |

---

## Roadmap

- Built-in route point fields without requiring ACF
- Visual pin picker for adding route points directly on a map
- Setup wizard for first-time users
- Optional ACF/custom field mapping
- Additional marker styles and route display options

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

---

## License

Released under the GPL v2 or later licence.

---

## Developed by

**Baxtersweb**

Practical WordPress solutions built for real businesses.

https://baxtersweb.com
