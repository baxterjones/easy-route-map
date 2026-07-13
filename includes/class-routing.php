<?php

if (!defined('ABSPATH')) {
    exit;
}

class BXTR_Maps_Routing
{
    const META_ROUTE_GEOMETRY = '_bxtr_route_geometry';
    const META_ROUTE_HASH = '_bxtr_route_hash';
    const META_ROUTE_ERROR = '_bxtr_route_error';

    public function __construct()
    {
        add_action('acf/save_post', [$this, 'refresh_route'], 30);
    }

    public static function has_api_key()
    {
        return trim((string) get_option(BXTR_Maps_Plugin::OPTION_ORS_API_KEY, '')) !== '';
    }

    public static function get_geometry($post_id)
    {
        $geometry = get_post_meta($post_id, self::META_ROUTE_GEOMETRY, true);
        return is_array($geometry) ? $geometry : [];
    }

    public static function get_error($post_id)
    {
        return sanitize_text_field((string) get_post_meta(absint($post_id), self::META_ROUTE_ERROR, true));
    }

    public static function ensure_geometry($post_id)
    {
        $geometry = self::get_geometry($post_id);

        if (!empty($geometry) || !self::has_api_key()) {
            return $geometry;
        }

        $routing = new self();
        $routing->refresh_route($post_id);

        return self::get_geometry($post_id);
    }

    public static function test_connection($api_key)
    {
        return self::request_route($api_key, [[8.681495, 49.41461], [8.686507, 49.41943]]);
    }

    public static function clear_all_routes()
    {
        $post_types = (array) get_option(
            BXTR_Maps_Plugin::OPTION_FIELD_POST_TYPES,
            BXTR_Maps_ACF::get_default_post_types()
        );

        $post_types = array_values(array_filter(array_map('sanitize_key', $post_types)));

        if (empty($post_types)) {
            return 0;
        }

        $post_ids = get_posts([
            'post_type' => $post_types,
            'post_status' => ['publish', 'draft', 'pending', 'private', 'future'],
            'posts_per_page' => -1,
            'fields' => 'ids',
            'no_found_rows' => true,
        ]);

        foreach ($post_ids as $post_id) {
            delete_post_meta($post_id, self::META_ROUTE_GEOMETRY);
            delete_post_meta($post_id, self::META_ROUTE_HASH);
            delete_post_meta($post_id, self::META_ROUTE_ERROR);
        }

        return count($post_ids);
    }

    public static function refresh_all_routes()
    {
        $post_types = (array) get_option(
            BXTR_Maps_Plugin::OPTION_FIELD_POST_TYPES,
            BXTR_Maps_ACF::get_default_post_types()
        );

        $post_types = array_values(array_filter(array_map('sanitize_key', $post_types)));

        if (empty($post_types)) {
            return 0;
        }

        $post_ids = get_posts([
            'post_type' => $post_types,
            'post_status' => ['publish', 'draft', 'pending', 'private', 'future'],
            'posts_per_page' => -1,
            'fields' => 'ids',
            'no_found_rows' => true,
            'orderby' => 'ID',
            'order' => 'ASC',
        ]);

        if (empty($post_ids)) {
            return 0;
        }

        $routing = new self();
        $updated = 0;

        foreach ($post_ids as $post_id) {
            $before = self::get_geometry($post_id);
            $routing->refresh_route($post_id);
            $after = self::get_geometry($post_id);

            if (empty($before) && !empty($after)) {
                $updated++;
            }
        }

        return $updated;
    }

    public function refresh_route($post_id)
    {
        $post_id = absint($post_id);

        if (!$post_id || wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }

        $api_key = trim((string) get_option(BXTR_Maps_Plugin::OPTION_ORS_API_KEY, ''));
        $map_data = BXTR_Maps_ACF::get_map_data($post_id);
        $coordinates = self::unique_coordinates($map_data['stops']);
        $hash = md5(wp_json_encode($coordinates));

        if (count($coordinates) < 2) {
            delete_post_meta($post_id, self::META_ROUTE_GEOMETRY);
            update_post_meta($post_id, self::META_ROUTE_HASH, $hash);
            delete_post_meta($post_id, self::META_ROUTE_ERROR);
            return;
        }

        if ($api_key === '') {
            update_post_meta($post_id, self::META_ROUTE_ERROR, __('Add an openrouteservice API key to calculate a road-following route.', 'baxtersweb-maps'));
            return;
        }

        $saved_hash = (string) get_post_meta($post_id, self::META_ROUTE_HASH, true);
        $saved_geometry = self::get_geometry($post_id);

        if ($saved_hash === $hash && !empty($saved_geometry)) {
            return;
        }

        $result = self::request_route_with_snap_retry($api_key, $coordinates);

        // Longer or widely spaced itineraries can exceed a single directions
        // request limit. Retry each consecutive leg and merge the returned
        // road geometry before falling back to a straight line.
        if (is_wp_error($result) && count($coordinates) > 2) {
            $result = self::request_route_by_legs($api_key, $coordinates);
        }

        if (is_wp_error($result)) {
            update_post_meta($post_id, self::META_ROUTE_ERROR, $result->get_error_message());
            return;
        }

        update_post_meta($post_id, self::META_ROUTE_GEOMETRY, $result);
        update_post_meta($post_id, self::META_ROUTE_HASH, $hash);
        delete_post_meta($post_id, self::META_ROUTE_ERROR);
    }

    private static function unique_coordinates($stops)
    {
        $coordinates = [];
        $seen = [];

        foreach ((array) $stops as $point) {
            $lng = isset($point['lng']) ? (float) $point['lng'] : null;
            $lat = isset($point['lat']) ? (float) $point['lat'] : null;

            if ($lng === null || $lat === null) {
                continue;
            }

            $key = sprintf('%.6F,%.6F', $lng, $lat);
            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $coordinates[] = [$lng, $lat];
        }

        return $coordinates;
    }


    private static function request_route_by_legs($api_key, $coordinates)
    {
        $merged = [];

        for ($index = 0, $last = count($coordinates) - 1; $index < $last; $index++) {
            $leg = self::request_route_with_snap_retry($api_key, [$coordinates[$index], $coordinates[$index + 1]]);

            if (is_wp_error($leg)) {
                return $leg;
            }

            $leg_coordinates = isset($leg['coordinates']) && is_array($leg['coordinates'])
                ? $leg['coordinates']
                : [];

            if (empty($leg_coordinates)) {
                return new WP_Error(
                    'bxtr_ors_leg_geometry',
                    __('No usable road route was returned for one of the route legs.', 'baxtersweb-maps')
                );
            }

            // Avoid duplicating the shared coordinate where two legs meet.
            if (!empty($merged)) {
                array_shift($leg_coordinates);
            }

            $merged = array_merge($merged, $leg_coordinates);
        }

        if (count($merged) < 2) {
            return new WP_Error(
                'bxtr_ors_merged_geometry',
                __('No usable road route was returned for these markers.', 'baxtersweb-maps')
            );
        }

        return [
            'type' => 'LineString',
            'coordinates' => array_values($merged),
        ];
    }

    private static function request_route_with_snap_retry($api_key, $coordinates)
    {
        $result = self::request_route($api_key, $coordinates);

        if (!is_wp_error($result)) {
            return $result;
        }

        $message = strtolower($result->get_error_message());

        // Rural itinerary points can be farther than the service's default
        // 350 metre snapping distance from a mapped drivable road. Retry only
        // this specific routing failure with a bounded 2 km search radius.
        if (strpos($message, 'routable point within a radius') === false) {
            return $result;
        }

        return self::request_route($api_key, $coordinates, 2000);
    }

    private static function request_route($api_key, $coordinates, $snap_radius = null)
    {
        $response = wp_remote_post(
            'https://api.openrouteservice.org/v2/directions/driving-car/geojson',
            [
                'timeout' => 20,
                'headers' => [
                    'Authorization' => $api_key,
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Accept' => 'application/geo+json, application/json',
                ],
                'body' => wp_json_encode(array_filter([
                    'coordinates' => array_values($coordinates),
                    'radiuses' => $snap_radius !== null
                        ? array_fill(0, count($coordinates), (int) $snap_radius)
                        : null,
                ], static function ($value) {
                    return $value !== null;
                })),
                'data_format' => 'body',
            ]
        );

        if (is_wp_error($response)) {
            return new WP_Error('bxtr_ors_request', __('The routing service could not be reached. Check the connection and try again.', 'baxtersweb-maps'));
        }

        $status = (int) wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($status < 200 || $status >= 300) {
            $message = isset($body['error']['message']) ? sanitize_text_field($body['error']['message']) : __('The routing service rejected the request.', 'baxtersweb-maps');
            return new WP_Error('bxtr_ors_response', $message);
        }

        $geometry = $body['features'][0]['geometry'] ?? [];

        if (empty($geometry['type']) || empty($geometry['coordinates'])) {
            return new WP_Error('bxtr_ors_geometry', __('No usable road route was returned for these markers.', 'baxtersweb-maps'));
        }

        if ($geometry['type'] === 'LineString') {
            $route_coordinates = array_values($geometry['coordinates']);
        } elseif ($geometry['type'] === 'MultiLineString') {
            $route_coordinates = [];
            foreach ((array) $geometry['coordinates'] as $line) {
                if (!is_array($line)) {
                    continue;
                }
                if (!empty($route_coordinates)) {
                    array_shift($line);
                }
                $route_coordinates = array_merge($route_coordinates, $line);
            }
        } else {
            return new WP_Error('bxtr_ors_geometry', __('No usable road route was returned for these markers.', 'baxtersweb-maps'));
        }

        if (count($route_coordinates) < 2) {
            return new WP_Error('bxtr_ors_geometry', __('No usable road route was returned for these markers.', 'baxtersweb-maps'));
        }

        return [
            'type' => 'LineString',
            'coordinates' => array_values($route_coordinates),
        ];
    }
}
