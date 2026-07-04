<?php

if (!defined('ABSPATH')) {
    exit;
}

class ERM_Admin
{
    private $message = '';
    private $message_type = 'success';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'menu']);
    }

    public function menu()
    {
        add_menu_page(
            'Easy Route Map',
            'Easy Route Map',
            'manage_options',
            'easy-route-map',
            [$this, 'page'],
            'dashicons-location-alt',
            56
        );
    }

    public function page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_POST['erm_settings_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['erm_settings_nonce'])), 'erm_save_settings')) {
            $this->save_settings();
        }

        if (isset($_POST['erm_setup_fields_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['erm_setup_fields_nonce'])), 'erm_setup_fields')) {
            $this->setup_fields();
        }

        $marker_color = get_option(ERM_Plugin::OPTION_MARKER_COLOR, ERM_Plugin::DEFAULT_MARKER_COLOR);
        $route_color = get_option(ERM_Plugin::OPTION_ROUTE_COLOR, ERM_Plugin::DEFAULT_ROUTE_COLOR);
        $uninstall_mode = get_option(ERM_Plugin::OPTION_UNINSTALL_MODE, 'keep');
        $map_height = get_option(ERM_Plugin::OPTION_MAP_HEIGHT, ERM_Plugin::DEFAULT_MAP_HEIGHT);
        $border_radius = get_option(ERM_Plugin::OPTION_BORDER_RADIUS, ERM_Plugin::DEFAULT_BORDER_RADIUS);
        $marker_label = get_option(ERM_Plugin::OPTION_MARKER_LABEL, ERM_Plugin::DEFAULT_MARKER_LABEL);
        $custom_marker_label = get_option(ERM_Plugin::OPTION_CUSTOM_MARKER_LABEL, '');
        $post_types = ERM_ACF::get_supported_post_types();
        $selected_post_types = get_option(ERM_Plugin::OPTION_FIELD_POST_TYPES, ERM_ACF::get_default_post_types());
        $acf_active = ERM_ACF::is_acf_active();
        $osm_active = ERM_ACF::is_osm_field_available();
        $field_group_exists = ERM_ACF::field_group_exists();

        include ERM_PLUGIN_PATH . 'templates/admin-page.php';
    }

    private function save_settings()
    {
        $marker_color = isset($_POST['erm_marker_color']) ? sanitize_hex_color(wp_unslash($_POST['erm_marker_color'])) : '';
        $route_color = isset($_POST['erm_route_color']) ? sanitize_hex_color(wp_unslash($_POST['erm_route_color'])) : '';
        $map_height = isset($_POST['erm_map_height']) ? self::sanitize_css_value(wp_unslash($_POST['erm_map_height']), ERM_Plugin::DEFAULT_MAP_HEIGHT) : ERM_Plugin::DEFAULT_MAP_HEIGHT;
        $border_radius = isset($_POST['erm_border_radius']) ? self::sanitize_css_value(wp_unslash($_POST['erm_border_radius']), ERM_Plugin::DEFAULT_BORDER_RADIUS) : ERM_Plugin::DEFAULT_BORDER_RADIUS;
        $marker_label = isset($_POST['erm_marker_label']) ? sanitize_text_field(wp_unslash($_POST['erm_marker_label'])) : ERM_Plugin::DEFAULT_MARKER_LABEL;
        $custom_marker_label = isset($_POST['erm_custom_marker_label']) ? sanitize_text_field(wp_unslash($_POST['erm_custom_marker_label'])) : '';

        if (!$marker_color) {
            $marker_color = ERM_Plugin::DEFAULT_MARKER_COLOR;
        }

        if (!$route_color) {
            $route_color = ERM_Plugin::DEFAULT_ROUTE_COLOR;
        }

        $allowed_marker_labels = ['Stop', 'Point', 'Day', 'Location', 'Custom'];

        if (!in_array($marker_label, $allowed_marker_labels, true)) {
            $marker_label = ERM_Plugin::DEFAULT_MARKER_LABEL;
        }

        if ($marker_label === 'Custom' && $custom_marker_label === '') {
            $custom_marker_label = ERM_Plugin::DEFAULT_MARKER_LABEL;
        }

        $uninstall_mode = isset($_POST['erm_uninstall_mode']) ? sanitize_key(wp_unslash($_POST['erm_uninstall_mode'])) : 'keep';

        if (!in_array($uninstall_mode, ['keep', 'remove'], true)) {
            $uninstall_mode = 'keep';
        }

        update_option(ERM_Plugin::OPTION_MARKER_COLOR, $marker_color);
        update_option(ERM_Plugin::OPTION_ROUTE_COLOR, $route_color);
        update_option(ERM_Plugin::OPTION_MAP_HEIGHT, $map_height);
        update_option(ERM_Plugin::OPTION_BORDER_RADIUS, $border_radius);
        update_option(ERM_Plugin::OPTION_MARKER_LABEL, $marker_label);
        update_option(ERM_Plugin::OPTION_CUSTOM_MARKER_LABEL, $custom_marker_label);
        update_option(ERM_Plugin::OPTION_UNINSTALL_MODE, $uninstall_mode);

        $this->message = 'Easy Route Map settings saved.';
        $this->message_type = 'success';
    }

    private static function sanitize_css_value($value, $default)
    {
        $value = trim(sanitize_text_field($value));

        if ($value === '') {
            return $default;
        }

        // Allow common CSS length values such as 500px, 70vh, 40rem, 50%, and simple calc() expressions.
        // Disallow semicolons, braces, and colons to avoid breaking out of the inline CSS custom property.
        if (!preg_match('/^[a-zA-Z0-9\s\.\,\%\+\-\*\/\(\)]+$/', $value)) {
            return $default;
        }

        return $value;
    }

    private function setup_fields()
    {
        if (ERM_ACF::field_group_exists()) {
            $this->message = 'Easy Route Map ACF fields already exist. Duplicate field groups were not created.';
            $this->message_type = 'info';
            return;
        }

        $post_types = isset($_POST['erm_field_post_types']) ? array_map('sanitize_key', (array) wp_unslash($_POST['erm_field_post_types'])) : [];
        $result = ERM_ACF::setup_field_group($post_types);

        if (is_wp_error($result)) {
            $this->message = $result->get_error_message();
            $this->message_type = 'error';
            return;
        }

        $this->message = 'Easy Route Map ACF fields are ready. Edit a selected post type and add route points.';
        $this->message_type = 'success';
    }
}
