<?php

if (!defined('ABSPATH')) {
    exit;
}

class ERM_Shortcode
{
    public function __construct()
    {
        add_shortcode('easy_route_map', [$this, 'render']);
    }

    public function render($atts)
    {
        $atts = shortcode_atts(
            [
                'id' => 0,
            ],
            $atts,
            'easy_route_map'
        );

        $post_id = absint($atts['id']);

        if (!$post_id) {
            $post_id = get_the_ID();
        }

        if (!$post_id) {
            return '';
        }

        $stops = ERM_ACF::get_stops($post_id);

        return ERM_Map::render($stops);
    }
}
