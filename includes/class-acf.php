<?php

if (!defined('ABSPATH')) {
    exit;
}

class ERM_ACF
{
    const REPEATER_FIELD = 'erm_route_points';
    const TITLE_FIELD = 'erm_point_title';
    const DESCRIPTION_FIELD = 'erm_point_description';
    const LOCATION_FIELD = 'erm_point_location';

    public static function is_acf_active()
    {
        return function_exists('get_field') && function_exists('acf_get_field_groups');
    }

    public static function is_osm_field_available()
    {
        if (!function_exists('acf_get_field_types')) {
            return false;
        }

        $types = acf_get_field_types();

        return is_array($types) && isset($types['open_street_map']);
    }

    public static function get_supported_post_types()
    {
        $post_types = get_post_types(['public' => true], 'objects');
        $excluded = ['attachment'];
        $items = [];

        foreach ($post_types as $post_type => $object) {
            if (in_array($post_type, $excluded, true)) {
                continue;
            }

            $items[$post_type] = $object->labels->singular_name ?: $object->label;
        }

        return $items;
    }

    public static function get_default_post_types()
    {
        $available = self::get_supported_post_types();
        $defaults = array_values(array_intersect(['post', 'page'], array_keys($available)));

        return !empty($defaults) ? $defaults : array_keys($available);
    }

    public static function field_group_exists()
    {
        if (!function_exists('acf_get_field_group')) {
            return false;
        }

        return (bool) acf_get_field_group(ERM_Plugin::FIELD_GROUP_KEY);
    }

    public static function setup_field_group($post_types)
    {
        if (!self::is_acf_active()) {
            return new WP_Error('erm_acf_missing', 'Advanced Custom Fields is not active.');
        }

        if (!self::is_osm_field_available()) {
            return new WP_Error('erm_osm_missing', 'ACF OpenStreetMap Field is not active.');
        }

        if (!function_exists('acf_update_field_group') || !function_exists('acf_update_field')) {
            return new WP_Error('erm_acf_api_missing', 'The required ACF field update functions are not available.');
        }

        if (self::field_group_exists()) {
            return new WP_Error('erm_field_group_exists', 'Easy Route Map ACF fields already exist. Duplicate field groups were not created.');
        }

        $available_post_types = array_keys(self::get_supported_post_types());
        $post_types = array_values(array_intersect((array) $post_types, $available_post_types));

        if (empty($post_types)) {
            $post_types = self::get_default_post_types();
        }

        $location = [];

        foreach ($post_types as $post_type) {
            $location[] = [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => $post_type,
                ],
            ];
        }

        $field_group = [
            'key' => ERM_Plugin::FIELD_GROUP_KEY,
            'title' => 'Easy Route Map Fields',
            'fields' => [],
            'location' => $location,
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => 'Route point fields created by Easy Route Map.',
            'show_in_rest' => 0,
        ];

        $group = acf_update_field_group($field_group);

        if (empty($group['ID'])) {
            return new WP_Error('erm_field_group_failed', 'The ACF field group could not be created.');
        }

        $parent = (int) $group['ID'];

        $repeater = acf_update_field([
            'key' => 'field_erm_route_points',
            'label' => 'Route Points',
            'name' => self::REPEATER_FIELD,
            'type' => 'repeater',
            'parent' => $parent,
            'instructions' => 'Add one row for each point in the route. Drag rows to control the route order.',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => ['width' => '', 'class' => '', 'id' => ''],
            'collapsed' => 'field_erm_point_title',
            'min' => 0,
            'max' => 0,
            'layout' => 'block',
            'button_label' => 'Add Route Point',
        ]);

        $repeater_parent = !empty($repeater['ID']) ? (int) $repeater['ID'] : 'field_erm_route_points';

        acf_update_field([
            'key' => 'field_erm_point_title',
            'label' => 'Point Title',
            'name' => self::TITLE_FIELD,
            'type' => 'text',
            'parent' => $repeater_parent,
            'instructions' => 'Optional title for this route point.',
            'required' => 0,
            'wrapper' => ['width' => '', 'class' => '', 'id' => ''],
            'menu_order' => 0,
        ]);

        acf_update_field([
            'key' => 'field_erm_point_description',
            'label' => 'Point Description',
            'name' => self::DESCRIPTION_FIELD,
            'type' => 'textarea',
            'parent' => $repeater_parent,
            'instructions' => 'Optional popup text for this route point.',
            'required' => 0,
            'wrapper' => ['width' => '', 'class' => '', 'id' => ''],
            'rows' => 3,
            'new_lines' => 'wpautop',
            'menu_order' => 1,
        ]);

        acf_update_field([
            'key' => 'field_erm_point_location',
            'label' => 'Point Map',
            'name' => self::LOCATION_FIELD,
            'type' => 'open_street_map',
            'parent' => $repeater_parent,
            'instructions' => 'Drop one marker for this route point. Easy Route Map needs Raw data, not Leaflet JS or iframe output.',
            'required' => 1,
            'wrapper' => ['width' => '', 'class' => '', 'id' => ''],
            'return_format' => 'raw',
            'max_markers' => 1,
            'center_lat' => -28.5,
            'center_lng' => 24.7,
            'zoom' => 8,
            'allow_map_layers' => 0,
            'layers' => ['OpenStreetMap.Mapnik'],
            'height' => 350,
            'menu_order' => 2,
        ]);

        update_option(ERM_Plugin::OPTION_FIELD_POST_TYPES, $post_types);
        update_option(ERM_Plugin::OPTION_FIELD_SETUP_DONE, time());

        return true;
    }

    /**
     * Get route points from the current post's ACF repeater.
     *
     * @param int $post_id
     * @return array<int,array<string,mixed>>
     */
    public static function get_stops($post_id)
    {
        return self::get_route_points($post_id)['stops'];
    }

    public static function get_route_points($post_id)
    {
        $diagnostics = [];

        if (!self::is_acf_active()) {
            return [
                'stops' => [],
                'diagnostics' => ['Advanced Custom Fields is not active. Install and activate ACF, then set up the Easy Route Map fields.'],
            ];
        }

        if (!self::is_osm_field_available()) {
            $diagnostics[] = 'ACF OpenStreetMap Field is not active. Install and activate the ACF OpenStreetMap Field plugin.';
        }

        $field_object = function_exists('get_field_object') ? get_field_object(self::REPEATER_FIELD, $post_id) : false;

        if (!$field_object) {
            return [
                'stops' => [],
                'diagnostics' => array_merge($diagnostics, [
                    'Route points field not found. Expected ACF repeater field name: ' . self::REPEATER_FIELD . '.',
                    'Use the Easy Route Map admin page to create the ACF field group automatically.',
                ]),
            ];
        }

        $rows = get_field(self::REPEATER_FIELD, $post_id);

        if (empty($rows) || !is_array($rows)) {
            return [
                'stops' => [],
                'diagnostics' => array_merge($diagnostics, [
                    'The route points field exists, but no route points are saved for this post yet.',
                    'Add at least one row to the Route Points repeater and drop one marker in the Point Location field.',
                ]),
            ];
        }

        $stops = [];
        $invalid_rows = 0;
        $non_raw_rows = 0;

        foreach ($rows as $row) {
            if (empty($row[self::LOCATION_FIELD])) {
                $invalid_rows++;
                continue;
            }

            $location = $row[self::LOCATION_FIELD];

            if (!is_array($location)) {
                $non_raw_rows++;
                continue;
            }

            $lat = 0;
            $lng = 0;

            if (!empty($location['markers'][0]['lat']) && !empty($location['markers'][0]['lng'])) {
                $lat = (float) $location['markers'][0]['lat'];
                $lng = (float) $location['markers'][0]['lng'];
            } elseif (!empty($location['lat']) && !empty($location['lng'])) {
                $lat = (float) $location['lat'];
                $lng = (float) $location['lng'];
            }

            if (!$lat || !$lng) {
                $invalid_rows++;
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

        if (empty($stops)) {
            $diagnostics[] = 'Route point rows were found, but no valid coordinates could be read.';

            if ($non_raw_rows > 0) {
                $diagnostics[] = 'The OpenStreetMap field may not be set to Return Format: Raw data. Easy Route Map cannot read Leaflet JS or iframe output.';
            }

            if ($invalid_rows > 0) {
                $diagnostics[] = 'Check that each Point Location field contains one saved marker.';
            }
        } elseif (count($stops) === 1) {
            $diagnostics[] = 'Only one route point was found. The marker can display, but a route line needs at least two points.';
        }

        return [
            'stops' => $stops,
            'diagnostics' => $diagnostics,
        ];
    }
}
