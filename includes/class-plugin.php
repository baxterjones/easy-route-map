<?php

if (!defined('ABSPATH')) {
    exit;
}

class ERM_Plugin
{
    const OPTION_MARKER_COLOR = 'erm_marker_color';
    const OPTION_ROUTE_COLOR = 'erm_route_color';
    const OPTION_FIELD_POST_TYPES = 'erm_field_post_types';
    const OPTION_FIELD_SETUP_DONE = 'erm_acf_field_setup_done';
    const OPTION_UNINSTALL_MODE = 'erm_uninstall_mode';
    const OPTION_MAP_HEIGHT = 'erm_map_height';
    const OPTION_BORDER_RADIUS = 'erm_border_radius';
    const OPTION_MARKER_LABEL = 'erm_marker_label';
    const OPTION_CUSTOM_MARKER_LABEL = 'erm_custom_marker_label';
    const DEFAULT_MARKER_COLOR = '#3d874d';
    const DEFAULT_ROUTE_COLOR = '#3388ff';
    const DEFAULT_MAP_HEIGHT = '500px';
    const DEFAULT_BORDER_RADIUS = '12px';
    const DEFAULT_MARKER_LABEL = 'Stop';
    const FIELD_GROUP_KEY = 'group_erm_route_map';

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
        add_filter('plugin_action_links_' . plugin_basename(ERM_PLUGIN_FILE), [$this, 'plugin_action_links']);
    }

    public function plugin_action_links($links)
    {
        $settings_link = sprintf(
            '<a href="%s">%s</a>',
            esc_url(admin_url('admin.php?page=easy-route-map')),
            esc_html__('Settings', 'easy-route-map')
        );

        array_unshift($links, $settings_link);

        return $links;
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
        if ($hook === 'toplevel_page_easy-route-map') {
            wp_enqueue_style(
                'erm-admin',
                ERM_PLUGIN_URL . 'assets/css/admin.css',
                [],
                filemtime(ERM_PLUGIN_PATH . 'assets/css/admin.css')
            );

            wp_enqueue_script(
                'erm-admin',
                ERM_PLUGIN_URL . 'assets/js/admin.js',
                [],
                filemtime(ERM_PLUGIN_PATH . 'assets/js/admin.js'),
                true
            );
        }

        if (in_array($hook, ['post.php', 'post-new.php'], true)) {
            wp_enqueue_script(
                'erm-admin-editor',
                ERM_PLUGIN_URL . 'assets/js/admin.js',
                ['wp-data', 'wp-dom-ready'],
                filemtime(ERM_PLUGIN_PATH . 'assets/js/admin.js'),
                true
            );
        }
    }
}
