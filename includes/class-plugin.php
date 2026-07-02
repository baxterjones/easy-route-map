<?php

if (!defined('ABSPATH')) {
    exit;
}

class IRM_Plugin
{
    const OPTION_MARKER_COLOR = 'irm_marker_color';
    const OPTION_ROUTE_COLOR = 'irm_route_color';
    const DEFAULT_MARKER_COLOR = '#3d874d';
    const DEFAULT_ROUTE_COLOR = '#3388ff';

    private static $instance = null;

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        require_once IRM_PLUGIN_PATH . 'includes/class-admin.php';
        require_once IRM_PLUGIN_PATH . 'includes/class-acf.php';
        require_once IRM_PLUGIN_PATH . 'includes/class-map.php';
        require_once IRM_PLUGIN_PATH . 'includes/class-shortcode.php';

        new IRM_Admin();
        new IRM_Shortcode();

        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin']);
    }

    public function enqueue_frontend()
    {
        wp_enqueue_style(
            'irm-leaflet',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
            [],
            '1.9.4'
        );

        wp_enqueue_style(
            'irm-style',
            IRM_PLUGIN_URL . 'assets/css/itinerary-route-map.css',
            ['irm-leaflet'],
            filemtime(IRM_PLUGIN_PATH . 'assets/css/itinerary-route-map.css')
        );

        wp_enqueue_script(
            'irm-leaflet',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
            [],
            '1.9.4',
            true
        );

        wp_enqueue_script(
            'irm-script',
            IRM_PLUGIN_URL . 'assets/js/itinerary-route-map.js',
            ['irm-leaflet'],
            filemtime(IRM_PLUGIN_PATH . 'assets/js/itinerary-route-map.js'),
            true
        );
    }

    public function enqueue_admin($hook)
    {
        if ($hook !== 'tools_page_itinerary-route-map') {
            return;
        }

        wp_enqueue_style(
            'irm-admin',
            IRM_PLUGIN_URL . 'assets/css/admin.css',
            [],
            filemtime(IRM_PLUGIN_PATH . 'assets/css/admin.css')
        );
    }
}
