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
     * @return string
     */
    public static function render($stops)
    {
        $marker_color = get_option(ERM_Plugin::OPTION_MARKER_COLOR, ERM_Plugin::DEFAULT_MARKER_COLOR);
        $route_color  = get_option(ERM_Plugin::OPTION_ROUTE_COLOR, ERM_Plugin::DEFAULT_ROUTE_COLOR);

        ob_start();

        include ERM_PLUGIN_PATH . 'templates/map.php';

        return ob_get_clean();
    }
}
