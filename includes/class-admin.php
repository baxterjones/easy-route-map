<?php

if (!defined('ABSPATH')) {
    exit;
}

class BXTR_Maps_Admin
{
    private $message = '';
    private $message_type = 'success';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'menu']);
    }

    public function menu()
    {
        add_management_page(
            esc_html__('Baxtersweb Maps', 'baxtersweb-maps'),
            esc_html__('Baxtersweb Maps', 'baxtersweb-maps'),
            'manage_options',
            'baxtersweb-maps',
            [$this, 'page']
        );
    }

    public function page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        if ('POST' === sanitize_text_field(wp_unslash($_SERVER['REQUEST_METHOD'] ?? ''))) {
            if (isset($_POST['bxtr_settings_nonce'])) {
                check_admin_referer('bxtr_save_settings', 'bxtr_settings_nonce');
                $this->save_settings();
            } elseif (isset($_POST['bxtr_setup_fields_nonce'])) {
                check_admin_referer('bxtr_setup_fields', 'bxtr_setup_fields_nonce');
                $this->setup_fields();
            }
        }

        $marker_color = get_option(BXTR_Maps_Plugin::OPTION_MARKER_COLOR, BXTR_Maps_Plugin::DEFAULT_MARKER_COLOR);
        $route_color = get_option(BXTR_Maps_Plugin::OPTION_ROUTE_COLOR, BXTR_Maps_Plugin::DEFAULT_ROUTE_COLOR);
        $marker_number_color = get_option(BXTR_Maps_Plugin::OPTION_MARKER_NUMBER_COLOR, BXTR_Maps_Plugin::DEFAULT_MARKER_NUMBER_COLOR);
        $uninstall_mode = get_option(BXTR_Maps_Plugin::OPTION_UNINSTALL_MODE, 'keep');
        $map_height = get_option(BXTR_Maps_Plugin::OPTION_MAP_HEIGHT, BXTR_Maps_Plugin::DEFAULT_MAP_HEIGHT);
        $border_radius = get_option(BXTR_Maps_Plugin::OPTION_BORDER_RADIUS, BXTR_Maps_Plugin::DEFAULT_BORDER_RADIUS);
        $marker_sequence = get_option(BXTR_Maps_Plugin::OPTION_MARKER_SEQUENCE, BXTR_Maps_Plugin::DEFAULT_MARKER_SEQUENCE);
        $draw_route = get_option(BXTR_Maps_Plugin::OPTION_DRAW_ROUTE, 'yes');
        $poi_enabled = get_option(BXTR_Maps_Plugin::OPTION_POI_ENABLED, 'yes');
        $poi_marker_color = get_option(BXTR_Maps_Plugin::OPTION_POI_MARKER_COLOR, BXTR_Maps_Plugin::DEFAULT_POI_MARKER_COLOR);
        $map_tile_style = get_option(BXTR_Maps_Plugin::OPTION_MAP_TILE_STYLE, BXTR_Maps_Plugin::DEFAULT_MAP_TILE_STYLE);
        $post_types = BXTR_Maps_ACF::get_supported_post_types();
        $selected_post_types = get_option(BXTR_Maps_Plugin::OPTION_FIELD_POST_TYPES, BXTR_Maps_ACF::get_default_post_types());
        $acf_active = BXTR_Maps_ACF::is_acf_active();
        $osm_active = BXTR_Maps_ACF::is_osm_field_available();
        $field_group_exists = BXTR_Maps_ACF::field_group_exists();

        include BXTR_MAPS_PLUGIN_PATH . 'templates/admin-page.php';
    }

    private function save_settings()
    {
        check_admin_referer('bxtr_save_settings', 'bxtr_settings_nonce');

        $marker_color = isset($_POST['bxtr_marker_color']) ? sanitize_hex_color(wp_unslash($_POST['bxtr_marker_color'])) : '';
        $route_color = isset($_POST['bxtr_route_color']) ? sanitize_hex_color(wp_unslash($_POST['bxtr_route_color'])) : '';
        $marker_number_color = isset($_POST['bxtr_marker_number_color']) ? sanitize_hex_color(wp_unslash($_POST['bxtr_marker_number_color'])) : '';
        $map_height = isset($_POST['bxtr_map_height']) ? self::sanitize_css_value(sanitize_text_field(wp_unslash($_POST['bxtr_map_height'])), BXTR_Maps_Plugin::DEFAULT_MAP_HEIGHT) : BXTR_Maps_Plugin::DEFAULT_MAP_HEIGHT;
        $border_radius = isset($_POST['bxtr_border_radius']) ? self::sanitize_css_value(sanitize_text_field(wp_unslash($_POST['bxtr_border_radius'])), BXTR_Maps_Plugin::DEFAULT_BORDER_RADIUS) : BXTR_Maps_Plugin::DEFAULT_BORDER_RADIUS;
        $marker_sequence = isset($_POST['bxtr_marker_sequence']) ? sanitize_key(wp_unslash($_POST['bxtr_marker_sequence'])) : BXTR_Maps_Plugin::DEFAULT_MARKER_SEQUENCE;
        $draw_route = isset($_POST['bxtr_draw_route']) ? sanitize_key(wp_unslash($_POST['bxtr_draw_route'])) : 'no';
        $poi_enabled = isset($_POST['bxtr_poi_enabled']) ? sanitize_key(wp_unslash($_POST['bxtr_poi_enabled'])) : 'no';
        $poi_marker_color = isset($_POST['bxtr_poi_marker_color']) ? sanitize_hex_color(wp_unslash($_POST['bxtr_poi_marker_color'])) : '';
        $map_tile_style = isset($_POST['bxtr_map_tile_style']) ? sanitize_key(wp_unslash($_POST['bxtr_map_tile_style'])) : BXTR_Maps_Plugin::DEFAULT_MAP_TILE_STYLE;

        if (!$marker_color) {
            $marker_color = BXTR_Maps_Plugin::DEFAULT_MARKER_COLOR;
        }

        if (!$route_color) {
            $route_color = BXTR_Maps_Plugin::DEFAULT_ROUTE_COLOR;
        }

        if (!$marker_number_color) {
            $marker_number_color = BXTR_Maps_Plugin::DEFAULT_MARKER_NUMBER_COLOR;
        }

        if (!in_array($marker_sequence, ['alphabetic', 'numeric'], true)) {
            $marker_sequence = BXTR_Maps_Plugin::DEFAULT_MARKER_SEQUENCE;
        }

        if (!in_array($draw_route, ['yes', 'no'], true)) {
            $draw_route = 'yes';
        }

        if (!in_array($poi_enabled, ['yes', 'no'], true)) {
            $poi_enabled = 'yes';
        }

        if (!$poi_marker_color) {
            $poi_marker_color = BXTR_Maps_Plugin::DEFAULT_POI_MARKER_COLOR;
        }


        if (!in_array($map_tile_style, ['osm', 'hot', 'topo'], true)) {
            $map_tile_style = BXTR_Maps_Plugin::DEFAULT_MAP_TILE_STYLE;
        }

        $uninstall_mode = isset($_POST['bxtr_uninstall_mode']) ? sanitize_key(wp_unslash($_POST['bxtr_uninstall_mode'])) : 'keep';

        if (!in_array($uninstall_mode, ['keep', 'remove'], true)) {
            $uninstall_mode = 'keep';
        }

        update_option(BXTR_Maps_Plugin::OPTION_MARKER_COLOR, $marker_color);
        update_option(BXTR_Maps_Plugin::OPTION_ROUTE_COLOR, $route_color);
        update_option(BXTR_Maps_Plugin::OPTION_MARKER_NUMBER_COLOR, $marker_number_color);
        update_option(BXTR_Maps_Plugin::OPTION_MAP_HEIGHT, $map_height);
        update_option(BXTR_Maps_Plugin::OPTION_BORDER_RADIUS, $border_radius);
        update_option(BXTR_Maps_Plugin::OPTION_MARKER_SEQUENCE, $marker_sequence);
        update_option(BXTR_Maps_Plugin::OPTION_DRAW_ROUTE, $draw_route);
        update_option(BXTR_Maps_Plugin::OPTION_POI_ENABLED, $poi_enabled);
        update_option(BXTR_Maps_Plugin::OPTION_POI_MARKER_COLOR, $poi_marker_color);
        update_option(BXTR_Maps_Plugin::OPTION_MAP_TILE_STYLE, $map_tile_style);
        update_option(BXTR_Maps_Plugin::OPTION_UNINSTALL_MODE, $uninstall_mode);

        $this->message = esc_html__('Baxtersweb Maps settings saved.', 'baxtersweb-maps');
        $this->message_type = 'success';
    }

    private static function sanitize_css_value($value, $default)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return $default;
        }

        // Allow common fixed CSS length values. Avoid percentage heights because Leaflet maps
        // often cannot calculate them unless the parent container has an explicit height.
        if (!preg_match('/^\d+(\.\d+)?(px|vh|vw|rem|em)$/i', $value)) {
            return $default;
        }

        return $value;
    }

    private function setup_fields()
    {
        check_admin_referer('bxtr_setup_fields', 'bxtr_setup_fields_nonce');

        $post_types = isset($_POST['bxtr_field_post_types']) ? array_map('sanitize_key', (array) wp_unslash($_POST['bxtr_field_post_types'])) : [];
        $include_poi_fields = isset($_POST['bxtr_setup_include_poi']) && sanitize_key(wp_unslash($_POST['bxtr_setup_include_poi'])) === 'yes';
        $result = BXTR_Maps_ACF::setup_field_group($post_types, $include_poi_fields);

        if (is_wp_error($result)) {
            $this->message = $result->get_error_message();
            $this->message_type = 'error';
            return;
        }

        $this->message = esc_html__('Baxtersweb Maps ACF fields are ready. Existing generated fields were updated in place where possible.', 'baxtersweb-maps');
        $this->message_type = 'success';
    }
}
