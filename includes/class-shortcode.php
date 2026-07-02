<?php

if (!defined('ABSPATH')) {
    exit;
}

class IRM_Shortcode
{
    public function __construct()
    {
        add_shortcode('itinerary_route_map', [$this, 'render']);
    }

    public function render($atts)
    {
        $atts = shortcode_atts(
            [
                'id' => 0,
            ],
            $atts,
            'itinerary_route_map'
        );

        $post_id = absint($atts['id']);

        if (!$post_id) {
            $post_id = get_the_ID();
        }

        if (!$post_id) {
            return '';
        }

        $stops = IRM_ACF::get_stops($post_id);

        return IRM_Map::render($stops);
    }
}
