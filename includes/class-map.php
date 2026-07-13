<?php

if (!defined('ABSPATH')) {
    exit;
}

class BXTR_Maps_Map
{
    /**
     * Render the map template.
     *
     * @param array<int,array<string,mixed>> $stops
     * @param array<int,array<string,mixed>> $pois
     * @param array<int,string> $diagnostics
     * @param string $template
     * @param array<string,string> $overrides
     * @return string
     */
    public static function render($stops, $pois = [], $diagnostics = [], $template = 'default', $overrides = [], $route_geometry = [])
    {
        $marker_color = get_option(BXTR_Maps_Plugin::OPTION_MARKER_COLOR, BXTR_Maps_Plugin::DEFAULT_MARKER_COLOR);
        $route_color = get_option(BXTR_Maps_Plugin::OPTION_ROUTE_COLOR, BXTR_Maps_Plugin::DEFAULT_ROUTE_COLOR);
        $marker_number_color = get_option(BXTR_Maps_Plugin::OPTION_MARKER_NUMBER_COLOR, BXTR_Maps_Plugin::DEFAULT_MARKER_NUMBER_COLOR);
        $poi_marker_color = get_option(BXTR_Maps_Plugin::OPTION_POI_MARKER_COLOR, BXTR_Maps_Plugin::DEFAULT_POI_MARKER_COLOR);
        $map_height = get_option(BXTR_Maps_Plugin::OPTION_MAP_HEIGHT, BXTR_Maps_Plugin::DEFAULT_MAP_HEIGHT);
        $border_radius = get_option(BXTR_Maps_Plugin::OPTION_BORDER_RADIUS, BXTR_Maps_Plugin::DEFAULT_BORDER_RADIUS);
        $marker_sequence = get_option(BXTR_Maps_Plugin::OPTION_MARKER_SEQUENCE, BXTR_Maps_Plugin::DEFAULT_MARKER_SEQUENCE);
        $draw_route = get_option(BXTR_Maps_Plugin::OPTION_DRAW_ROUTE, 'yes');
        $poi_enabled = get_option(BXTR_Maps_Plugin::OPTION_POI_ENABLED, 'yes');
        $map_tile_style = 'osm';
        $cluster_pois = get_option(BXTR_Maps_Plugin::OPTION_CLUSTER_POIS, 'yes');

        if (!empty($overrides['draw_route']) && in_array($overrides['draw_route'], ['yes', 'no'], true)) {
            $draw_route = $overrides['draw_route'];
        }

        if (!empty($overrides['poi_enabled']) && in_array($overrides['poi_enabled'], ['yes', 'no'], true)) {
            $poi_enabled = $overrides['poi_enabled'];
        }

        if ($draw_route === 'no' && !empty($diagnostics)) {
            $diagnostics = array_values(array_filter($diagnostics, static function ($diagnostic) {
                return strpos($diagnostic, 'Only one map marker was found') === false;
            }));
        }

        if ($poi_enabled !== 'yes') {
            $pois = [];
        }

        if (!in_array($marker_sequence, ['alphabetic', 'numeric'], true)) {
            $marker_sequence = BXTR_Maps_Plugin::DEFAULT_MARKER_SEQUENCE;
        }

        $map_id = 'bxtr-map-' . wp_generate_uuid4();
        $template = sanitize_key($template) ?: 'default';

        /**
         * Allows developers to adjust the map configuration for named shortcode templates.
         *
         * Advanced use: named configurations can be provided by filtering bxtr_template_config.
         */
        $config = apply_filters('bxtr_template_config', [
            'template' => $template,
            'marker_color' => $marker_color,
            'route_color' => $route_color,
            'marker_number_color' => $marker_number_color,
            'poi_marker_color' => $poi_marker_color,
            'draw_route' => $draw_route,
            'poi_enabled' => $poi_enabled,
            'map_height' => $map_height,
            'border_radius' => $border_radius,
            'marker_sequence' => $marker_sequence,
            'map_tile_style' => $map_tile_style,
            'cluster_pois' => $cluster_pois,
            'route_geometry' => is_array($route_geometry) ? $route_geometry : [],
        ], $template);

        ob_start();

        include BXTR_MAPS_PLUGIN_PATH . 'templates/map.php';

        return ob_get_clean();
    }
}
