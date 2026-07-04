<?php

if (!defined('ABSPATH')) {
    exit;
}

class ERM_Plugin
{
    const OPTION_MARKER_COLOR = 'erm_marker_color';
    const OPTION_ROUTE_COLOR = 'erm_route_color';
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
        require_once ERM_PLUGIN_PATH . 'includes/class-admin.php';
        require_once ERM_PLUGIN_PATH . 'includes/class-acf.php';
        require_once ERM_PLUGIN_PATH . 'includes/class-map.php';
        require_once ERM_PLUGIN_PATH . 'includes/class-shortcode.php';

        new ERM_Admin();
        new ERM_Shortcode();

        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin']);
    }

    public function enqueue_frontend()
    {
        wp_enqueue_style(
            'erm-leaflet',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
            [],
            '1.9.4'
        );

        wp_enqueue_style(
            'erm-style',
            ERM_PLUGIN_URL . 'assets/css/easy-route-map.css',
            ['erm-leaflet'],
            filemtime(ERM_PLUGIN_PATH . 'assets/css/easy-route-map.css')
        );

        wp_enqueue_script(
            'erm-leaflet',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
            [],
            '1.9.4',
            true
        );

        wp_enqueue_script(
            'erm-script',
            ERM_PLUGIN_URL . 'assets/js/easy-route-map.js',
            ['erm-leaflet'],
            filemtime(ERM_PLUGIN_PATH . 'assets/js/easy-route-map.js'),
            true
        );
    }

    public function enqueue_admin($hook)
    {
        if ($hook !== 'tools_page_easy-route-map') {
            return;
        }

        wp_enqueue_style(
            'erm-admin',
            ERM_PLUGIN_URL . 'assets/css/admin.css',
            [],
            filemtime(ERM_PLUGIN_PATH . 'assets/css/admin.css')
        );
    }
}
