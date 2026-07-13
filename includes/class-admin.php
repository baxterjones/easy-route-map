<?php
if (!defined('ABSPATH')) exit;

class BXTR_Maps_Admin {
    private $message = '';
    private $message_type = 'success';

    public function __construct() { add_action('admin_menu', [$this, 'menu']); }
    public function menu() { add_management_page(__('Baxtersweb Maps','baxtersweb-maps'), __('Baxtersweb Maps','baxtersweb-maps'), 'manage_options', 'baxtersweb-maps', [$this,'page']); }

    public function page() {
        if (!current_user_can('manage_options')) return;
        $active_tab = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'overview';
        if (!in_array($active_tab, ['overview','routing','markers','help'], true)) $active_tab = 'overview';

        if (isset($_SERVER['REQUEST_METHOD']) && 'POST' === sanitize_text_field(wp_unslash($_SERVER['REQUEST_METHOD']))) {
            if (isset($_POST['bxtr_settings_nonce'])) {
                check_admin_referer('bxtr_save_settings','bxtr_settings_nonce');
                $active_tab = isset($_POST['bxtr_active_tab']) ? sanitize_key(wp_unslash($_POST['bxtr_active_tab'])) : $active_tab;
                $this->save_settings(wp_unslash($_POST));
            } elseif (isset($_POST['bxtr_setup_fields_nonce'])) {
                check_admin_referer('bxtr_setup_fields','bxtr_setup_fields_nonce');
                $this->setup_fields(wp_unslash($_POST));
                $active_tab = 'overview';
            } elseif (isset($_POST['bxtr_test_routing_nonce'])) {
                check_admin_referer('bxtr_test_routing','bxtr_test_routing_nonce');
                $active_tab = 'routing';
                $this->save_settings(wp_unslash($_POST));
                $key = isset($_POST['bxtr_ors_api_key']) ? sanitize_text_field(wp_unslash($_POST['bxtr_ors_api_key'])) : '';
                update_option(BXTR_Maps_Plugin::OPTION_ORS_API_KEY, $key);
                update_option(BXTR_Maps_Plugin::OPTION_ORS_TESTED_AT, time());

                if ($key === '') {
                    delete_option(BXTR_Maps_Plugin::OPTION_ORS_API_KEY);
                    $this->message = __('API key removed. Existing saved road routes are retained. New or changed routes will use straight fallback lines until another valid key is connected.', 'baxtersweb-maps');
                    $this->message_type = 'success';
                    update_option(BXTR_Maps_Plugin::OPTION_ORS_STATUS, 'untested');
                    update_option(BXTR_Maps_Plugin::OPTION_ORS_STATUS_MESSAGE, __('No API key is saved. Existing saved routes remain available.', 'baxtersweb-maps'));
                } else {
                    $result = BXTR_Maps_Routing::test_connection($key);

                    if (is_wp_error($result)) {
                        $this->message = $result->get_error_message();
                        $this->message_type = 'error';
                        update_option(BXTR_Maps_Plugin::OPTION_ORS_STATUS, 'error');
                        update_option(BXTR_Maps_Plugin::OPTION_ORS_STATUS_MESSAGE, $this->message);
                    } else {
                        $updated = BXTR_Maps_Routing::refresh_all_routes();
                        update_option(BXTR_Maps_Plugin::OPTION_ORS_STATUS, 'connected');
                        update_option(BXTR_Maps_Plugin::OPTION_ORS_STATUS_MESSAGE, __('Connected — API key verified.', 'baxtersweb-maps'));
                        $this->message = sprintf(
                            /* translators: %d: number of existing maps that received road-following route geometry. */
                            _n(
                                'Routing connected successfully. %d existing map was updated.',
                                'Routing connected successfully. %d existing maps were updated.',
                                $updated,
                                'baxtersweb-maps'
                            ),
                            $updated
                        );
                    }
                }
            }
        }

        $values = [
            'marker_color' => get_option(BXTR_Maps_Plugin::OPTION_MARKER_COLOR, BXTR_Maps_Plugin::DEFAULT_MARKER_COLOR),
            'route_color' => get_option(BXTR_Maps_Plugin::OPTION_ROUTE_COLOR, BXTR_Maps_Plugin::DEFAULT_ROUTE_COLOR),
            'marker_number_color' => get_option(BXTR_Maps_Plugin::OPTION_MARKER_NUMBER_COLOR, BXTR_Maps_Plugin::DEFAULT_MARKER_NUMBER_COLOR),
            'poi_marker_color' => get_option(BXTR_Maps_Plugin::OPTION_POI_MARKER_COLOR, BXTR_Maps_Plugin::DEFAULT_POI_MARKER_COLOR),
            'poi_icon_mode' => get_option(BXTR_Maps_Plugin::OPTION_POI_ICON_MODE, BXTR_Maps_Plugin::DEFAULT_POI_ICON_MODE),
            'poi_default_icon' => get_option(BXTR_Maps_Plugin::OPTION_POI_DEFAULT_ICON, BXTR_Maps_Plugin::DEFAULT_POI_ICON),
            'poi_theme_icon_class' => get_option(BXTR_Maps_Plugin::OPTION_POI_THEME_ICON_CLASS, ''),
            'map_height' => get_option(BXTR_Maps_Plugin::OPTION_MAP_HEIGHT, BXTR_Maps_Plugin::DEFAULT_MAP_HEIGHT),
            'border_radius' => get_option(BXTR_Maps_Plugin::OPTION_BORDER_RADIUS, BXTR_Maps_Plugin::DEFAULT_BORDER_RADIUS),
            'marker_sequence' => get_option(BXTR_Maps_Plugin::OPTION_MARKER_SEQUENCE, BXTR_Maps_Plugin::DEFAULT_MARKER_SEQUENCE),
            'draw_route' => get_option(BXTR_Maps_Plugin::OPTION_DRAW_ROUTE, 'yes'),
            'poi_enabled' => get_option(BXTR_Maps_Plugin::OPTION_POI_ENABLED, 'yes'),
            'cluster_pois' => get_option(BXTR_Maps_Plugin::OPTION_CLUSTER_POIS, 'yes'),
            'ors_api_key' => get_option(BXTR_Maps_Plugin::OPTION_ORS_API_KEY, ''),
            'uninstall_mode' => get_option(BXTR_Maps_Plugin::OPTION_UNINSTALL_MODE, 'keep'),
            'ors_status' => get_option(BXTR_Maps_Plugin::OPTION_ORS_STATUS, 'untested'),
            'ors_status_message' => get_option(BXTR_Maps_Plugin::OPTION_ORS_STATUS_MESSAGE, ''),
            'ors_tested_at' => (int) get_option(BXTR_Maps_Plugin::OPTION_ORS_TESTED_AT, 0),
        ];
        $post_types = BXTR_Maps_ACF::get_supported_post_types();
        $selected_post_types = get_option(BXTR_Maps_Plugin::OPTION_FIELD_POST_TYPES, BXTR_Maps_ACF::get_default_post_types());
        $field_groups = BXTR_Maps_ACF::get_field_groups();
        $field_group_mode = get_option(BXTR_Maps_Plugin::OPTION_FIELD_GROUP_MODE, 'new');
        $selected_field_group_key = get_option(BXTR_Maps_Plugin::OPTION_FIELD_GROUP_KEY, BXTR_Maps_Plugin::FIELD_GROUP_KEY);
        $selected_field_group_title = BXTR_Maps_ACF::get_field_group_title($selected_field_group_key);
        $advanced_views_active = in_array('acf-views/acf-views.php', (array) get_option('active_plugins', []), true);
        $acf_active = BXTR_Maps_ACF::is_acf_active(); $osm_active = BXTR_Maps_ACF::is_osm_field_available();
        $field_group_exists = BXTR_Maps_ACF::field_group_exists(); $routing_connected = get_option(BXTR_Maps_Plugin::OPTION_ORS_STATUS, 'untested') === 'connected';
        $fields_last_updated = (int) get_option(BXTR_Maps_Plugin::OPTION_FIELD_SETUP_DONE, 0);
        include BXTR_MAPS_PLUGIN_PATH . 'templates/admin-page.php';
    }

    private function save_settings(array $post_data) {
        $hex_options = [
            'bxtr_marker_color'        => [BXTR_Maps_Plugin::OPTION_MARKER_COLOR, BXTR_Maps_Plugin::DEFAULT_MARKER_COLOR],
            'bxtr_route_color'         => [BXTR_Maps_Plugin::OPTION_ROUTE_COLOR, BXTR_Maps_Plugin::DEFAULT_ROUTE_COLOR],
            'bxtr_marker_number_color' => [BXTR_Maps_Plugin::OPTION_MARKER_NUMBER_COLOR, BXTR_Maps_Plugin::DEFAULT_MARKER_NUMBER_COLOR],
            'bxtr_poi_marker_color'    => [BXTR_Maps_Plugin::OPTION_POI_MARKER_COLOR, BXTR_Maps_Plugin::DEFAULT_POI_MARKER_COLOR],
        ];

        foreach ($hex_options as $field => $config) {
            if (!isset($post_data[$field])) {
                continue;
            }
            $value = sanitize_hex_color($post_data[$field]);
            update_option($config[0], $value ?: $config[1]);
        }

        if (isset($post_data['bxtr_map_height'])) {
            update_option(BXTR_Maps_Plugin::OPTION_MAP_HEIGHT, self::sanitize_css_value($post_data['bxtr_map_height'], BXTR_Maps_Plugin::DEFAULT_MAP_HEIGHT));
        }
        if (isset($post_data['bxtr_border_radius'])) {
            update_option(BXTR_Maps_Plugin::OPTION_BORDER_RADIUS, self::sanitize_css_value($post_data['bxtr_border_radius'], BXTR_Maps_Plugin::DEFAULT_BORDER_RADIUS, true));
        }

        $select_options = [
            'marker_sequence' => ['alphabetic', 'numeric'],
            'draw_route'      => ['yes', 'no'],
            'poi_enabled'     => ['yes', 'no'],
            'cluster_pois'    => ['yes', 'no'],
            'poi_icon_mode'   => ['builtin', 'theme', 'plain'],
            'uninstall_mode'  => ['keep', 'remove'],
        ];
        foreach ($select_options as $key => $allowed) {
            $name = 'bxtr_' . $key;
            if (!isset($post_data[$name])) {
                continue;
            }
            $value = sanitize_key($post_data[$name]);
            if (!in_array($value, $allowed, true)) {
                $value = $allowed[0];
            }
            update_option(constant('BXTR_Maps_Plugin::OPTION_' . strtoupper($key)), $value);
        }

        if (isset($post_data['bxtr_poi_default_icon'])) {
            $icon = sanitize_key($post_data['bxtr_poi_default_icon']);
            if (!array_key_exists($icon, BXTR_Maps_ACF::get_builtin_icons())) {
                $icon = BXTR_Maps_Plugin::DEFAULT_POI_ICON;
            }
            update_option(BXTR_Maps_Plugin::OPTION_POI_DEFAULT_ICON, $icon);
        }

        if (isset($post_data['bxtr_poi_theme_icon_class'])) {
            $classes = preg_split('/\s+/', (string) $post_data['bxtr_poi_theme_icon_class']);
            update_option(BXTR_Maps_Plugin::OPTION_POI_THEME_ICON_CLASS, implode(' ', array_filter(array_map('sanitize_html_class', $classes))));
        }

        if (isset($post_data['bxtr_ors_api_key'])) {
            update_option(BXTR_Maps_Plugin::OPTION_ORS_API_KEY, sanitize_text_field($post_data['bxtr_ors_api_key']));
        }

        $this->message = __('Baxtersweb Maps settings saved.', 'baxtersweb-maps');
    }

    private static function sanitize_css_value($value, $default, $allow_percent = false) {
        $value = trim(sanitize_text_field($value));
        $units = $allow_percent ? 'px|vh|vw|rem|em|%' : 'px|vh|vw|rem|em';
        return preg_match('/^\d+(\.\d+)?(' . $units . ')$/i', $value) ? $value : $default;
    }

    private function setup_fields(array $post_data) {
        check_admin_referer('bxtr_setup_fields', 'bxtr_setup_fields_nonce');
        $post_types = isset($post_data['bxtr_field_post_types']) ? array_map('sanitize_key', (array) $post_data['bxtr_field_post_types']) : [];
        $include_pois = isset($post_data['bxtr_include_poi_fields']);
        $mode = isset($post_data['bxtr_field_group_mode']) && 'existing' === sanitize_key($post_data['bxtr_field_group_mode']) ? 'existing' : 'new';
        $target_group_key = isset($post_data['bxtr_existing_field_group']) ? sanitize_key($post_data['bxtr_existing_field_group']) : '';
        $result=BXTR_Maps_ACF::setup_field_group($post_types,$include_pois,$mode,$target_group_key);
        if (is_wp_error($result)) { $this->message=$result->get_error_message(); $this->message_type='error'; return; }
        $group_title = !empty($result['group_title']) ? $result['group_title'] : __('the selected field group','baxtersweb-maps');
        if ($include_pois) {
            /* translators: %s: ACF field group title. */
            $this->message = sprintf(__('The route and POI fields are ready in “%s”.', 'baxtersweb-maps'), $group_title);
        } else {
            /* translators: %s: ACF field group title. */
            $this->message = sprintf(__('The route fields are ready in “%s”. POI fields are not included.', 'baxtersweb-maps'), $group_title);
        }
    }
}
