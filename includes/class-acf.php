<?php

if (!defined('ABSPATH')) {
    exit;
}

class IRM_ACF
{
    const REPEATER_FIELD = 'itinerary_day_items';
    const TITLE_FIELD = 'itinerary_day_item_title';
    const DESCRIPTION_FIELD = 'itinerary_day_item_location_description';
    const LOCATION_FIELD = 'itinerary_day_item_location_coordinates';

    /**
     * Get route points from the current post's ACF repeater.
     *
     * @param int $post_id
     * @return array<int,array<string,mixed>>
     */
    public static function get_stops($post_id)
    {
        if (!function_exists('get_field')) {
            return [];
        }

        $rows = get_field(self::REPEATER_FIELD, $post_id);

        if (empty($rows) || !is_array($rows)) {
            return [];
        }

        $stops = [];

        foreach ($rows as $row) {
            if (empty($row[self::LOCATION_FIELD]) || !is_array($row[self::LOCATION_FIELD])) {
                continue;
            }

            $location = $row[self::LOCATION_FIELD];

            // Prefer the actual dropped OSM marker position.
            // Fall back to the map centre if a marker is not available.
            if (!empty($location['markers'][0]['lat']) && !empty($location['markers'][0]['lng'])) {
                $lat = (float) $location['markers'][0]['lat'];
                $lng = (float) $location['markers'][0]['lng'];
            } else {
                $lat = isset($location['lat']) ? (float) $location['lat'] : 0;
                $lng = isset($location['lng']) ? (float) $location['lng'] : 0;
            }

            if (!$lat || !$lng) {
                continue;
            }

            $stops[] = [
                'title'       => isset($row[self::TITLE_FIELD]) ? sanitize_text_field($row[self::TITLE_FIELD]) : '',
                'description' => isset($row[self::DESCRIPTION_FIELD]) ? wp_kses_post($row[self::DESCRIPTION_FIELD]) : '',
                'lat'         => $lat,
                'lng'         => $lng,
                'zoom'        => (int) ($location['zoom'] ?? 10),
            ];
        }

        return $stops;
    }
}
