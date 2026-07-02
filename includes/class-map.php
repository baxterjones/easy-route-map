<?php

if (!defined('ABSPATH')) {
    exit;
}

class IRM_Map
{
    /**
     * Render the map template.
     *
     * @param array<int,array<string,mixed>> $stops
     * @return string
     */
    public static function render($stops)
    {
        $marker_color = get_option(IRM_Plugin::OPTION_MARKER_COLOR, IRM_Plugin::DEFAULT_MARKER_COLOR);
        $route_color  = get_option(IRM_Plugin::OPTION_ROUTE_COLOR, IRM_Plugin::DEFAULT_ROUTE_COLOR);

        ob_start();

        include IRM_PLUGIN_PATH . 'templates/map.php';

        return ob_get_clean();
    }
}
