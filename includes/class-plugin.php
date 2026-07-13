<?php

if (!defined('ABSPATH')) {
    exit;
}

class BXTR_Maps_Plugin
{
    const OPTION_MARKER_COLOR = 'bxtr_marker_color';
    const OPTION_ROUTE_COLOR = 'bxtr_route_color';
    const OPTION_MARKER_NUMBER_COLOR = 'bxtr_marker_number_color';
    const OPTION_FIELD_POST_TYPES = 'bxtr_field_post_types';
    const OPTION_FIELD_SETUP_DONE = 'bxtr_acf_field_setup_done';
    const OPTION_FIELD_GROUP_MODE = 'bxtr_field_group_mode';
    const OPTION_FIELD_GROUP_KEY = 'bxtr_field_group_key';
    const OPTION_UNINSTALL_MODE = 'bxtr_uninstall_mode';
    const OPTION_MAP_HEIGHT = 'bxtr_map_height';
    const OPTION_BORDER_RADIUS = 'bxtr_border_radius';
    const OPTION_MARKER_SEQUENCE = 'bxtr_marker_sequence';
    const OPTION_DRAW_ROUTE = 'bxtr_draw_route';
    const OPTION_POI_ENABLED = 'bxtr_poi_enabled';
    const OPTION_POI_MARKER_COLOR = 'bxtr_poi_marker_color';
    const OPTION_POI_ICON_MODE = 'bxtr_poi_icon_mode';
    const OPTION_POI_DEFAULT_ICON = 'bxtr_poi_default_icon';
    const OPTION_POI_THEME_ICON_CLASS = 'bxtr_poi_theme_icon_class';
    const OPTION_MAP_TILE_STYLE = 'bxtr_map_tile_style';
    const OPTION_ORS_API_KEY = 'bxtr_ors_api_key';
    const OPTION_CLUSTER_POIS = 'bxtr_cluster_pois';
    const OPTION_ORS_STATUS = 'bxtr_ors_status';
    const OPTION_ORS_STATUS_MESSAGE = 'bxtr_ors_status_message';
    const OPTION_ORS_TESTED_AT = 'bxtr_ors_tested_at';
    const DEFAULT_MARKER_COLOR = '#3d874d';
    const DEFAULT_ROUTE_COLOR = '#3388ff';
    const DEFAULT_MARKER_NUMBER_COLOR = '#ffffff';
    const DEFAULT_MAP_HEIGHT = '500px';
    const DEFAULT_BORDER_RADIUS = '12px';
    const DEFAULT_MARKER_SEQUENCE = 'alphabetic';
    const DEFAULT_POI_MARKER_COLOR = '#f59e0b';
    const DEFAULT_POI_ICON_MODE = 'builtin';
    const DEFAULT_POI_ICON = 'location-alt';
    const DEFAULT_MAP_TILE_STYLE = 'osm';
    const FIELD_GROUP_KEY = 'group_bxtr_maps';

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
        require_once BXTR_MAPS_PLUGIN_PATH . 'includes/class-admin.php';
        require_once BXTR_MAPS_PLUGIN_PATH . 'includes/class-acf.php';
        require_once BXTR_MAPS_PLUGIN_PATH . 'includes/class-map.php';
        require_once BXTR_MAPS_PLUGIN_PATH . 'includes/class-shortcode.php';
        require_once BXTR_MAPS_PLUGIN_PATH . 'includes/class-routing.php';

        new BXTR_Maps_Admin();
        new BXTR_Maps_Shortcode();
        new BXTR_Maps_Routing();

        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin']);
        add_filter('plugin_action_links_' . plugin_basename(BXTR_MAPS_PLUGIN_FILE), [$this, 'plugin_action_links']);
    }

    public function plugin_action_links($links)
    {
        $settings_link = sprintf(
            '<a href="%s">%s</a>',
            esc_url(admin_url('tools.php?page=baxtersweb-maps')),
            esc_html__('Settings', 'baxtersweb-maps')
        );

        array_unshift($links, $settings_link);

        return $links;
    }

    public function enqueue_frontend()
    {
        wp_enqueue_style(
            'bxtr-leaflet',
            BXTR_MAPS_PLUGIN_URL . 'assets/vendor/leaflet/leaflet.css',
            [],
            '1.9.4'
        );

        wp_enqueue_style('dashicons');

        wp_enqueue_style(
            'bxtr-style',
            BXTR_MAPS_PLUGIN_URL . 'assets/css/baxtersweb-maps.css',
            ['bxtr-leaflet'],
            filemtime(BXTR_MAPS_PLUGIN_PATH . 'assets/css/baxtersweb-maps.css')
        );

        wp_enqueue_script(
            'bxtr-leaflet',
            BXTR_MAPS_PLUGIN_URL . 'assets/vendor/leaflet/leaflet.js',
            [],
            '1.9.4',
            true
        );

        wp_enqueue_script(
            'bxtr-script',
            BXTR_MAPS_PLUGIN_URL . 'assets/js/baxtersweb-maps.js',
            ['bxtr-leaflet'],
            filemtime(BXTR_MAPS_PLUGIN_PATH . 'assets/js/baxtersweb-maps.js'),
            true
        );


        wp_localize_script(
            'bxtr-script',
            'BXTRMapsFrontend',
            [
                'marker' => __('Marker', 'baxtersweb-maps'),
                'pointOfInterest' => __('Point of Interest', 'baxtersweb-maps'),
            ]
        );
    }

    public function enqueue_admin($hook)
    {
        if ($hook === 'tools_page_baxtersweb-maps') {
            wp_enqueue_style('dashicons');

            wp_enqueue_style(
                'bxtr-leaflet',
                BXTR_MAPS_PLUGIN_URL . 'assets/vendor/leaflet/leaflet.css',
                [],
                '1.9.4'
            );

            wp_enqueue_style(
                'bxtr-style',
                BXTR_MAPS_PLUGIN_URL . 'assets/css/baxtersweb-maps.css',
                ['bxtr-leaflet'],
                filemtime(BXTR_MAPS_PLUGIN_PATH . 'assets/css/baxtersweb-maps.css')
            );

            wp_enqueue_style(
                'bxtr-admin',
                BXTR_MAPS_PLUGIN_URL . 'assets/css/admin.css',
                ['bxtr-style'],
                filemtime(BXTR_MAPS_PLUGIN_PATH . 'assets/css/admin.css')
            );

            wp_enqueue_script(
                'bxtr-leaflet',
                BXTR_MAPS_PLUGIN_URL . 'assets/vendor/leaflet/leaflet.js',
                [],
                '1.9.4',
                true
            );

            wp_enqueue_script(
                'bxtr-admin',
                BXTR_MAPS_PLUGIN_URL . 'assets/js/admin.js',
                ['bxtr-leaflet'],
                filemtime(BXTR_MAPS_PLUGIN_PATH . 'assets/js/admin.js'),
                true
            );

            wp_localize_script(
                'bxtr-admin',
                'BXTRMapsAdmin',
                [
                    'copy' => __('Copy', 'baxtersweb-maps'),
                    'copied' => __('Copied', 'baxtersweb-maps'),
                    'pointOfInterest' => __('Hotel', 'baxtersweb-maps'),
                    'exampleExtraMarker' => __('Example supporting point.', 'baxtersweb-maps'),
                    'stopA' => __('Marker A', 'baxtersweb-maps'),
                    'stopB' => __('Marker B', 'baxtersweb-maps'),
                    'exampleRouteStop' => __('Example route marker popup content.', 'baxtersweb-maps'),
                    'clickedMarkerTitle' => __('Example Map Marker', 'baxtersweb-maps'),
                    'clickedMarkerDescription' => __('This is dummy popup content so you can preview the marker and popup styling.', 'baxtersweb-maps'),
                ]
            );
        }

        if (in_array($hook, ['post.php', 'post-new.php'], true)) {
            wp_enqueue_script(
                'bxtr-admin-editor',
                BXTR_MAPS_PLUGIN_URL . 'assets/js/admin.js',
                ['wp-data', 'wp-dom-ready'],
                filemtime(BXTR_MAPS_PLUGIN_PATH . 'assets/js/admin.js'),
                true
            );
        }
    }
}
