<?php

if (!defined('ABSPATH')) {
    exit;
}

class ERM_Admin
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'menu']);
    }

    public function menu()
    {
        add_management_page(
            'Easy Route Map',
            'Easy Route Map',
            'manage_options',
            'easy-route-map',
            [$this, 'page']
        );
    }

    public function page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_POST['erm_settings_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['erm_settings_nonce'])), 'erm_save_settings')) {
            $marker_color = isset($_POST['erm_marker_color']) ? sanitize_hex_color(wp_unslash($_POST['erm_marker_color'])) : '';
            $route_color = isset($_POST['erm_route_color']) ? sanitize_hex_color(wp_unslash($_POST['erm_route_color'])) : '';

            if (!$marker_color) {
                $marker_color = ERM_Plugin::DEFAULT_MARKER_COLOR;
            }

            if (!$route_color) {
                $route_color = ERM_Plugin::DEFAULT_ROUTE_COLOR;
            }

            update_option(ERM_Plugin::OPTION_MARKER_COLOR, $marker_color);
            update_option(ERM_Plugin::OPTION_ROUTE_COLOR, $route_color);

            echo '<div class="notice notice-success is-dismissible"><p>Easy Route Map settings saved.</p></div>';
        }

        $marker_color = get_option(ERM_Plugin::OPTION_MARKER_COLOR, ERM_Plugin::DEFAULT_MARKER_COLOR);
        $route_color = get_option(ERM_Plugin::OPTION_ROUTE_COLOR, ERM_Plugin::DEFAULT_ROUTE_COLOR);

        include ERM_PLUGIN_PATH . 'templates/admin-page.php';
    }
}
