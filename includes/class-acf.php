<?php

if (!defined('ABSPATH')) {
    exit;
}

class BXTR_Maps_ACF
{
    const REPEATER_FIELD = 'bxtr_map_markers';
    const TITLE_FIELD = 'bxtr_marker_title';
    const DESCRIPTION_FIELD = 'bxtr_marker_description';
    const LOCATION_FIELD = 'bxtr_marker_coordinates';

    const POI_REPEATER_FIELD = 'bxtr_poi_markers';
    const POI_TITLE_FIELD = 'bxtr_poi_title';
    const POI_DESCRIPTION_FIELD = 'bxtr_poi_description';
    const POI_TYPE_FIELD = 'bxtr_poi_type';
    const POI_LOCATION_FIELD = 'bxtr_poi_coordinates';

    public static function is_acf_active()
    {
        return function_exists('get_field') && function_exists('acf_get_field_groups') && self::is_repeater_field_available();
    }

    public static function is_repeater_field_available()
    {
        if (!function_exists('acf_get_field_types')) {
            return false;
        }

        $types = acf_get_field_types();

        return is_array($types) && isset($types['repeater']);
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

        return (bool) acf_get_field_group(BXTR_Maps_Plugin::FIELD_GROUP_KEY);
    }

    public static function setup_field_group($post_types, $include_poi_fields = false)
    {
        if (!self::is_acf_active()) {
            return new WP_Error('bxtr_acf_missing', __('Advanced Custom Fields Pro is not active.', 'baxtersweb-maps'));
        }

        if (!self::is_osm_field_available()) {
            return new WP_Error('bxtr_osm_missing', __('ACF OpenStreetMap Field is not active.', 'baxtersweb-maps'));
        }

        if (!function_exists('acf_update_field_group') || !function_exists('acf_update_field')) {
            return new WP_Error('bxtr_acf_api_missing', __('The required ACF field update functions are not available.', 'baxtersweb-maps'));
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
            'key' => BXTR_Maps_Plugin::FIELD_GROUP_KEY,
            'title' => __('Baxtersweb Maps Fields', 'baxtersweb-maps'),
            'fields' => [],
            'location' => $location,
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => __('Map marker and point-of-interest fields created by Baxtersweb Maps.', 'baxtersweb-maps'),
            'show_in_rest' => 0,
        ];

        $existing_group_id = self::get_single_acf_post_id('acf-field-group', BXTR_Maps_Plugin::FIELD_GROUP_KEY);
        $existing_group = function_exists('acf_get_field_group') ? acf_get_field_group(BXTR_Maps_Plugin::FIELD_GROUP_KEY) : false;

        if ($existing_group_id) {
            $field_group['ID'] = $existing_group_id;
        } elseif (!empty($existing_group['ID'])) {
            $field_group['ID'] = (int) $existing_group['ID'];
        }

        $group = acf_update_field_group($field_group);

        if (empty($group['ID']) && empty($field_group['ID'])) {
            return new WP_Error('bxtr_field_group_failed', __('The ACF field group could not be created or updated.', 'baxtersweb-maps'));
        }

        $parent = !empty($group['ID']) ? (int) $group['ID'] : (int) $field_group['ID'];

        self::create_repeater($parent, 'field_bxtr_map_markers', __('Map Markers', 'baxtersweb-maps'), self::REPEATER_FIELD, __('Add one row for each map marker. Drag rows to control the route order.', 'baxtersweb-maps'), __('Add Map Marker', 'baxtersweb-maps'), [
            ['field_bxtr_marker_title', __('Marker Title', 'baxtersweb-maps'), self::TITLE_FIELD, 'text', __('Optional title for this map marker.', 'baxtersweb-maps')],
            ['field_bxtr_marker_description', __('Marker Description', 'baxtersweb-maps'), self::DESCRIPTION_FIELD, 'textarea', __('Optional popup text for this map marker.', 'baxtersweb-maps')],
            ['field_bxtr_marker_coordinates', __('Marker Coordinates', 'baxtersweb-maps'), self::LOCATION_FIELD, 'open_street_map', __('Drop one marker for this map marker. Baxtersweb Maps needs Raw data, not Leaflet JS or iframe output.', 'baxtersweb-maps')],
        ]);

        if ($include_poi_fields) {
            self::create_repeater($parent, 'field_bxtr_poi_markers', __('POI Markers', 'baxtersweb-maps'), self::POI_REPEATER_FIELD, __('Add optional point-of-interest markers. These appear on the map but do not affect the route line.', 'baxtersweb-maps'), __('Add POI Marker', 'baxtersweb-maps'), [
                ['field_bxtr_poi_title', __('POI Title', 'baxtersweb-maps'), self::POI_TITLE_FIELD, 'text', __('Optional title for this point of interest.', 'baxtersweb-maps')],
                ['field_bxtr_poi_type', __('POI Type', 'baxtersweb-maps'), self::POI_TYPE_FIELD, 'text', __('Optional label shown inside the POI marker, for example Hotel, Toilet, Gate, Viewpoint, Parking, or Cash.', 'baxtersweb-maps')],
                ['field_bxtr_poi_description', __('POI Description', 'baxtersweb-maps'), self::POI_DESCRIPTION_FIELD, 'textarea', __('Optional popup text for this point of interest.', 'baxtersweb-maps')],
                ['field_bxtr_poi_coordinates', __('POI Coordinates', 'baxtersweb-maps'), self::POI_LOCATION_FIELD, 'open_street_map', __('Drop one marker for this point of interest.', 'baxtersweb-maps')],
            ]);
        }

        update_option(BXTR_Maps_Plugin::OPTION_FIELD_POST_TYPES, $post_types);
        update_option(BXTR_Maps_Plugin::OPTION_FIELD_SETUP_DONE, time());

        return true;
    }



    private static function get_single_acf_post_id($post_type, $post_name)
    {
        $ids = get_posts([
            'post_type' => $post_type,
            'post_status' => ['publish', 'acf-disabled'],
            'name' => $post_name,
            'numberposts' => -1,
            'orderby' => 'ID',
            'order' => 'ASC',
            'fields' => 'ids',
        ]);

        if (empty($ids)) {
            return 0;
        }

        $keep = (int) array_shift($ids);

        foreach ($ids as $duplicate_id) {
            wp_delete_post((int) $duplicate_id, true);
        }

        return $keep;
    }

    private static function get_existing_field_id($key)
    {
        if (function_exists('acf_get_field')) {
            $field = acf_get_field($key);

            if (is_array($field) && !empty($field['ID'])) {
                return (int) $field['ID'];
            }
        }

        return self::get_single_acf_post_id('acf-field', $key);
    }

    private static function create_repeater($parent, $key, $label, $name, $instructions, $button_label, $sub_fields)
    {
        $repeater_args = [
            'key' => $key,
            'label' => $label,
            'name' => $name,
            'type' => 'repeater',
            'parent' => $parent,
            'instructions' => $instructions,
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => ['width' => '', 'class' => '', 'id' => ''],
            'collapsed' => $sub_fields[0][0],
            'min' => 0,
            'max' => 0,
            'layout' => 'block',
            'button_label' => $button_label,
        ];

        $existing_repeater_id = self::get_existing_field_id($key);

        if ($existing_repeater_id) {
            $repeater_args['ID'] = $existing_repeater_id;
        }

        $repeater = acf_update_field($repeater_args);

        $repeater_parent = !empty($repeater['ID']) ? (int) $repeater['ID'] : $key;
        $order = 0;

        foreach ($sub_fields as $field) {
            [$field_key, $field_label, $field_name, $field_type, $field_instructions] = $field;

            $args = [
                'key' => $field_key,
                'label' => $field_label,
                'name' => $field_name,
                'type' => $field_type,
                'parent' => $repeater_parent,
                'instructions' => $field_instructions,
                'required' => $field_type === 'open_street_map' ? 1 : 0,
                'wrapper' => ['width' => '', 'class' => '', 'id' => ''],
                'menu_order' => $order,
            ];

            if ($field_type === 'textarea') {
                $args['rows'] = 3;
                $args['new_lines'] = 'wpautop';
            }

            if ($field_type === 'open_street_map') {
                $args['return_format'] = 'raw';
                $args['max_markers'] = 1;
                $args['center_lat'] = -28.5;
                $args['center_lng'] = 24.7;
                $args['zoom'] = 8;
                $args['allow_map_layers'] = 0;
                $args['layers'] = ['OpenStreetMap.Mapnik'];
                $args['height'] = 350;
            }

            $existing_field_id = self::get_existing_field_id($field_key);

            if ($existing_field_id) {
                $args['ID'] = $existing_field_id;
            }

            acf_update_field($args);
            $order++;
        }
    }

    public static function get_stops($post_id)
    {
        return self::get_map_data($post_id)['stops'];
    }

    public static function get_route_points($post_id)
    {
        return self::get_map_data($post_id);
    }

    public static function get_map_data($post_id)
    {
        $diagnostics = [];

        if (!self::is_acf_active()) {
            return [
                'stops' => [],
                'pois' => [],
                'diagnostics' => [__('Advanced Custom Fields Pro is not active. Install and activate ACF Pro, then set up the Baxtersweb Maps fields.', 'baxtersweb-maps')],
            ];
        }

        if (!self::is_osm_field_available()) {
            $diagnostics[] = __('ACF OpenStreetMap Field is not active. Install and activate the ACF OpenStreetMap Field plugin.', 'baxtersweb-maps');
        }

        $stops = self::read_repeater_points($post_id, self::REPEATER_FIELD, self::TITLE_FIELD, self::DESCRIPTION_FIELD, self::LOCATION_FIELD, '', $diagnostics, 'route');
        $pois = self::read_repeater_points($post_id, self::POI_REPEATER_FIELD, self::POI_TITLE_FIELD, self::POI_DESCRIPTION_FIELD, self::POI_LOCATION_FIELD, self::POI_TYPE_FIELD, $diagnostics, 'poi');

        if (count($stops) === 1) {
            $diagnostics[] = __('Only one map marker was found. The marker can display, but a route line needs at least two map markers.', 'baxtersweb-maps');
        }

        return [
            'stops' => $stops,
            'pois' => $pois,
            'diagnostics' => array_values(array_unique($diagnostics)),
        ];
    }

    private static function read_repeater_points($post_id, $repeater_field, $title_field, $description_field, $location_field, $type_field, &$diagnostics, $point_kind)
    {
        $field_object = function_exists('get_field_object') ? get_field_object($repeater_field, $post_id) : false;

        if (!$field_object) {
            if ($point_kind === 'route') {
                /* translators: %s: expected ACF repeater field name. */
                $diagnostics[] = sprintf( __('Map markers field not found. Expected ACF repeater field name: %s.', 'baxtersweb-maps'), $repeater_field );
            }
            return [];
        }

        $rows = get_field($repeater_field, $post_id);

        if (empty($rows) || !is_array($rows)) {
            return [];
        }

        $points = [];
        $invalid_rows = 0;
        $non_raw_rows = 0;
        $index = 1;

        foreach ($rows as $row) {
            if (empty($row[$location_field])) {
                $invalid_rows++;
                continue;
            }

            $location = $row[$location_field];

            if (!is_array($location)) {
                $non_raw_rows++;
                continue;
            }

            $coordinates = self::extract_coordinates($location);

            if (!$coordinates) {
                $invalid_rows++;
                continue;
            }

            $point = [
                'title' => isset($row[$title_field]) ? sanitize_text_field($row[$title_field]) : '',
                'description' => isset($row[$description_field]) ? wp_kses_post($row[$description_field]) : '',
                'lat' => $coordinates['lat'],
                'lng' => $coordinates['lng'],
                'zoom' => (int) ($location['zoom'] ?? 10),
                'kind' => $point_kind,
                'number' => $index,
            ];

            if ($type_field !== '') {
                $point['type'] = isset($row[$type_field]) ? sanitize_text_field($row[$type_field]) : '';
            }

            $points[] = $point;
            $index++;
        }

        if ($non_raw_rows > 0) {
            $diagnostics[] = __('One or more OpenStreetMap fields may not be set to Return Format: Raw data. Baxtersweb Maps cannot read Leaflet JS or iframe output.', 'baxtersweb-maps');
        }

        if ($invalid_rows > 0 && empty($points) && $point_kind === 'route') {
            $diagnostics[] = __('Map marker rows were found, but no valid coordinates could be read. Check that each Marker Coordinates field contains one saved marker.', 'baxtersweb-maps');
        }

        return $points;
    }

    private static function extract_coordinates($location)
    {
        if (!empty($location['markers'][0]['lat']) && !empty($location['markers'][0]['lng'])) {
            return [
                'lat' => (float) $location['markers'][0]['lat'],
                'lng' => (float) $location['markers'][0]['lng'],
            ];
        }

        if (!empty($location['lat']) && !empty($location['lng'])) {
            return [
                'lat' => (float) $location['lat'],
                'lng' => (float) $location['lng'],
            ];
        }

        return false;
    }
}
