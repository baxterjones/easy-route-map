<?php

if (!defined('ABSPATH')) {
    exit;
}

class ERM_Map
{
    /**
     * Render the map template.
     *
     * @param array<int,array<string,mixed>> $stops
     * @param array<int,string> $diagnostics
     * @return string
     */
    public static function render($stops, $diagnostics = [])
    {
        $marker_color = get_option(ERM_Plugin::OPTION_MARKER_COLOR, ERM_Plugin::DEFAULT_MARKER_COLOR);
        $route_color  = get_option(ERM_Plugin::OPTION_ROUTE_COLOR, ERM_Plugin::DEFAULT_ROUTE_COLOR);
        $map_height = get_option(ERM_Plugin::OPTION_MAP_HEIGHT, ERM_Plugin::DEFAULT_MAP_HEIGHT);
        $border_radius = get_option(ERM_Plugin::OPTION_BORDER_RADIUS, ERM_Plugin::DEFAULT_BORDER_RADIUS);
        $marker_label = get_option(ERM_Plugin::OPTION_MARKER_LABEL, ERM_Plugin::DEFAULT_MARKER_LABEL);
        $custom_marker_label = get_option(ERM_Plugin::OPTION_CUSTOM_MARKER_LABEL, '');
        $marker_label_prefix = $marker_label === 'Custom' && $custom_marker_label !== '' ? $custom_marker_label : $marker_label;

        ob_start();

        include ERM_PLUGIN_PATH . 'templates/map.php';

        return ob_get_clean();
    }
}
