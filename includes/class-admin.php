<?php

if (!defined('ABSPATH')) {
    exit;
}

class IRM_Admin
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'menu']);
    }

    public function menu()
    {
        add_management_page(
            'Itinerary Route Map',
            'Itinerary Route Map',
            'manage_options',
            'itinerary-route-map',
            [$this, 'page']
        );
    }

    public function page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_POST['irm_settings_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['irm_settings_nonce'])), 'irm_save_settings')) {
            $marker_color = isset($_POST['irm_marker_color']) ? sanitize_hex_color(wp_unslash($_POST['irm_marker_color'])) : '';
            $route_color = isset($_POST['irm_route_color']) ? sanitize_hex_color(wp_unslash($_POST['irm_route_color'])) : '';

            if (!$marker_color) {
                $marker_color = IRM_Plugin::DEFAULT_MARKER_COLOR;
            }

            if (!$route_color) {
                $route_color = IRM_Plugin::DEFAULT_ROUTE_COLOR;
            }

            update_option(IRM_Plugin::OPTION_MARKER_COLOR, $marker_color);
            update_option(IRM_Plugin::OPTION_ROUTE_COLOR, $route_color);

            echo '<div class="notice notice-success is-dismissible"><p>Itinerary Route Map settings saved.</p></div>';
        }

        $marker_color = get_option(IRM_Plugin::OPTION_MARKER_COLOR, IRM_Plugin::DEFAULT_MARKER_COLOR);
        $route_color = get_option(IRM_Plugin::OPTION_ROUTE_COLOR, IRM_Plugin::DEFAULT_ROUTE_COLOR);

        include IRM_PLUGIN_PATH . 'templates/admin-page.php';
    }
}
