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
    const POI_MARKER_STYLE_FIELD = 'bxtr_poi_marker_style';
    const POI_BUILTIN_ICON_FIELD = 'bxtr_poi_builtin_icon';
    const POI_THEME_ICON_FIELD = 'bxtr_poi_theme_icon_class';
    const POI_COLOR_FIELD = 'bxtr_poi_color';

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
        return self::fields_installed();
    }

    public static function fields_installed()
    {
        return (bool) self::get_existing_field_id('field_bxtr_map_markers');
    }

    public static function get_field_groups()
    {
        if (!function_exists('acf_get_field_groups')) {
            return [];
        }

        $groups = acf_get_field_groups();
        $items = [];
        foreach ((array) $groups as $group) {
            if (empty($group['key']) || empty($group['title'])) {
                continue;
            }
            $items[$group['key']] = [
                'key' => $group['key'],
                'title' => $group['title'],
                'ID' => isset($group['ID']) ? (int) $group['ID'] : 0,
            ];
        }
        uasort($items, static function ($a, $b) {
            return strcasecmp($a['title'], $b['title']);
        });
        return $items;
    }

    public static function get_field_group_title($key)
    {
        $groups = self::get_field_groups();
        return isset($groups[$key]) ? $groups[$key]['title'] : '';
    }

    public static function setup_field_group($post_types, $include_poi_fields = false, $mode = 'new', $target_group_key = '')
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
        $mode = $mode === 'existing' ? 'existing' : 'new';
        $target_group_key = sanitize_key($target_group_key);

        if ($mode === 'existing') {
            $groups = self::get_field_groups();
            if (!$target_group_key || empty($groups[$target_group_key])) {
                return new WP_Error('bxtr_field_group_missing', __('Choose an existing ACF field group before adding the map fields.', 'baxtersweb-maps'));
            }
            $parent = !empty($groups[$target_group_key]['ID']) ? (int) $groups[$target_group_key]['ID'] : $target_group_key;
        } else {
            if (empty($post_types)) {
                $post_types = self::get_default_post_types();
            }

            $location = [];
            foreach ($post_types as $post_type) {
                $location[] = [[
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => $post_type,
                ]];
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
            $target_group_key = BXTR_Maps_Plugin::FIELD_GROUP_KEY;
        }

        self::create_repeater($parent, 'field_bxtr_map_markers', __('Map Markers', 'baxtersweb-maps'), self::REPEATER_FIELD, __('Add one row for each map marker. Drag rows to control the route order.', 'baxtersweb-maps'), __('Add Map Marker', 'baxtersweb-maps'), [
            ['field_bxtr_marker_title', __('Marker Title', 'baxtersweb-maps'), self::TITLE_FIELD, 'text', __('Optional title for this map marker.', 'baxtersweb-maps')],
            ['field_bxtr_marker_description', __('Marker Description', 'baxtersweb-maps'), self::DESCRIPTION_FIELD, 'textarea', __('Optional popup text for this map marker.', 'baxtersweb-maps')],
            ['field_bxtr_marker_coordinates', __('Marker Coordinates', 'baxtersweb-maps'), self::LOCATION_FIELD, 'open_street_map', __('Drop one marker for this map marker. Baxtersweb Maps needs Raw data, not Leaflet JS or iframe output.', 'baxtersweb-maps')],
        ]);

        if ($include_poi_fields) {
            $poi_fields = [
                ['field_bxtr_poi_title', __('POI Title', 'baxtersweb-maps'), self::POI_TITLE_FIELD, 'text', __('Optional title for this point of interest.', 'baxtersweb-maps')],
                ['field_bxtr_poi_type', __('POI Type', 'baxtersweb-maps'), self::POI_TYPE_FIELD, 'text', __('Optional category text shown in the popup.', 'baxtersweb-maps')],
            ];
            $icon_mode = get_option(BXTR_Maps_Plugin::OPTION_POI_ICON_MODE, BXTR_Maps_Plugin::DEFAULT_POI_ICON_MODE);
            if ($icon_mode === 'builtin') {
                $poi_fields[] = ['field_bxtr_poi_builtin_icon', __('POI Icon', 'baxtersweb-maps'), self::POI_BUILTIN_ICON_FIELD, 'select', __('Choose the built-in icon for this point of interest.', 'baxtersweb-maps')];
            } elseif ($icon_mode === 'theme') {
                $poi_fields[] = ['field_bxtr_poi_theme_icon_class', __('Theme Icon Class', 'baxtersweb-maps'), self::POI_THEME_ICON_FIELD, 'text', __('Optional override. Leave empty to use the default theme icon class from Baxtersweb Maps settings.', 'baxtersweb-maps')];
            }
            $poi_fields[] = ['field_bxtr_poi_color', __('Marker Background Colour', 'baxtersweb-maps'), self::POI_COLOR_FIELD, 'text', __('Optional hex colour such as #3d874d. Leave empty to use the global POI colour.', 'baxtersweb-maps')];
            $poi_fields[] = ['field_bxtr_poi_description', __('POI Description', 'baxtersweb-maps'), self::POI_DESCRIPTION_FIELD, 'textarea', __('Optional popup text for this point of interest.', 'baxtersweb-maps')];
            $poi_fields[] = ['field_bxtr_poi_coordinates', __('POI Coordinates', 'baxtersweb-maps'), self::POI_LOCATION_FIELD, 'open_street_map', __('Drop one marker for this point of interest.', 'baxtersweb-maps')];
            self::create_repeater($parent, 'field_bxtr_poi_markers', __('POI Markers', 'baxtersweb-maps'), self::POI_REPEATER_FIELD, __('Add optional points of interest. These appear on the map but do not affect the route line.', 'baxtersweb-maps'), __('Add POI Marker', 'baxtersweb-maps'), $poi_fields);
            self::delete_field_if_unused('field_bxtr_poi_marker_style');
            if ($icon_mode !== 'builtin') self::delete_field_if_unused('field_bxtr_poi_builtin_icon');
            if ($icon_mode !== 'theme') self::delete_field_if_unused('field_bxtr_poi_theme_icon_class');
        } else {
            self::delete_field_if_unused('field_bxtr_poi_markers');
        }

        update_option(BXTR_Maps_Plugin::OPTION_FIELD_POST_TYPES, $post_types);
        update_option(BXTR_Maps_Plugin::OPTION_FIELD_GROUP_MODE, $mode);
        update_option(BXTR_Maps_Plugin::OPTION_FIELD_GROUP_KEY, $target_group_key);
        update_option(BXTR_Maps_Plugin::OPTION_FIELD_SETUP_DONE, time());

        return [
            'group_key' => $target_group_key,
            'group_title' => self::get_field_group_title($target_group_key) ?: __('Baxtersweb Maps Fields', 'baxtersweb-maps'),
            'mode' => $mode,
        ];
    }



    private static function delete_field_if_unused($key)
    {
        $id = self::get_existing_field_id($key);
        if ($id) wp_delete_post($id, true);
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

            if ($field_name === self::POI_MARKER_STYLE_FIELD) {
                $args['choices'] = ['builtin' => __('Built-in icon', 'baxtersweb-maps'), 'theme' => __('Theme icon class', 'baxtersweb-maps'), 'plain' => __('Plain marker', 'baxtersweb-maps')];
                $args['default_value'] = 'builtin';
                $args['ui'] = 1;
            }

            if ($field_name === self::POI_BUILTIN_ICON_FIELD) {
                $args['choices'] = self::get_builtin_icons();
                $args['default_value'] = 'location-alt';
                $args['ui'] = 1;
            }

            if ($field_type === 'color_picker') {
                $args['default_value'] = '';
                $args['enable_opacity'] = 0;
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
        $pois = self::read_poi_points($post_id, $diagnostics);

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

    public static function get_builtin_icons()
    {
        return [
            'location-alt' => __('Location', 'baxtersweb-maps'), 'building' => __('Accommodation / Building', 'baxtersweb-maps'),
            'food' => __('Food', 'baxtersweb-maps'), 'coffee' => __('Coffee', 'baxtersweb-maps'), 'car' => __('Parking / Car', 'baxtersweb-maps'),
            'airplane' => __('Airport', 'baxtersweb-maps'), 'tickets-alt' => __('Event / Ticket', 'baxtersweb-maps'), 'store' => __('Shop', 'baxtersweb-maps'),
            'heart' => __('Medical / Heart', 'baxtersweb-maps'), 'info' => __('Information', 'baxtersweb-maps'), 'camera' => __('Attraction / Camera', 'baxtersweb-maps'),
            'palmtree' => __('Beach / Leisure', 'baxtersweb-maps'), 'flag' => __('Start / Finish', 'baxtersweb-maps'), 'admin-site-alt3' => __('Viewpoint / Globe', 'baxtersweb-maps'),
            'warning' => __('Warning', 'baxtersweb-maps'), 'yes-alt' => __('Check point', 'baxtersweb-maps'), 'groups' => __('Meeting point', 'baxtersweb-maps'),
            'money-alt' => __('Cash / Money', 'baxtersweb-maps'), 'rest-api' => __('Services', 'baxtersweb-maps'), 'star-filled' => __('Featured', 'baxtersweb-maps')
        ];
    }

    private static function read_poi_points($post_id, &$diagnostics)
    {
        $rows = get_field(self::POI_REPEATER_FIELD, $post_id);
        if (empty($rows) || !is_array($rows)) return [];
        $global_style = get_option(BXTR_Maps_Plugin::OPTION_POI_ICON_MODE, BXTR_Maps_Plugin::DEFAULT_POI_ICON_MODE);
        if (!in_array($global_style, ['builtin','theme','plain'], true)) $global_style = 'builtin';
        $default_icon = get_option(BXTR_Maps_Plugin::OPTION_POI_DEFAULT_ICON, BXTR_Maps_Plugin::DEFAULT_POI_ICON);
        if (!array_key_exists($default_icon, self::get_builtin_icons())) $default_icon = BXTR_Maps_Plugin::DEFAULT_POI_ICON;
        $default_theme_class = get_option(BXTR_Maps_Plugin::OPTION_POI_THEME_ICON_CLASS, '');
        $points = []; $index = 1;
        foreach ($rows as $row) {
            if (empty($row[self::POI_LOCATION_FIELD]) || !is_array($row[self::POI_LOCATION_FIELD])) continue;
            $coordinates = self::extract_coordinates($row[self::POI_LOCATION_FIELD]);
            if (!$coordinates) continue;
            $icon = sanitize_key($row[self::POI_BUILTIN_ICON_FIELD] ?? $default_icon);
            if (!array_key_exists($icon, self::get_builtin_icons())) $icon = $default_icon;
            $theme_value = (string) ($row[self::POI_THEME_ICON_FIELD] ?? '');
            if ($theme_value === '') $theme_value = $default_theme_class;
            $points[] = [
                'title' => sanitize_text_field($row[self::POI_TITLE_FIELD] ?? ''),
                'description' => wp_kses_post($row[self::POI_DESCRIPTION_FIELD] ?? ''),
                'type' => sanitize_text_field($row[self::POI_TYPE_FIELD] ?? ''),
                'lat' => $coordinates['lat'], 'lng' => $coordinates['lng'],
                'zoom' => (int) ($row[self::POI_LOCATION_FIELD]['zoom'] ?? 10), 'kind' => 'poi', 'number' => $index++,
                'markerStyle' => $global_style, 'builtinIcon' => $icon,
                'themeIconClass' => implode(' ', array_filter(array_map('sanitize_html_class', preg_split('/\s+/', $theme_value)))),
                'color' => self::normalise_poi_color($row),
            ];
        }
        return $points;
    }


    private static function normalise_poi_color($row)
    {
        $value = '';
        foreach ([self::POI_COLOR_FIELD, 'bxtr_poi_marker_color', 'field_bxtr_poi_color'] as $key) {
            if (isset($row[$key]) && $row[$key] !== '') {
                $value = is_array($row[$key]) ? ($row[$key]['value'] ?? '') : $row[$key];
                break;
            }
        }

        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }
        if ($value[0] !== '#') {
            $value = '#' . $value;
        }
        if (preg_match('/^#([0-9a-f]{3})$/i', $value, $matches)) {
            $value = '#' . $matches[1][0] . $matches[1][0] . $matches[1][1] . $matches[1][1] . $matches[1][2] . $matches[1][2];
        }

        return sanitize_hex_color($value) ?: '';
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
