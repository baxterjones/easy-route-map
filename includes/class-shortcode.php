<?php

if (!defined('ABSPATH')) {
    exit;

}

class BXTR_Maps_Shortcode
{
    public function __construct()
    {
        add_shortcode('bxtr_map', [$this, 'render']);
    }

    public function render($atts)
    {
        $atts = shortcode_atts(
            [
                'id' => 0,
                'template' => 'default',
                'route' => '',
                'poi' => '',
            ],
            $atts,
            'bxtr_map'
        );

        $post_id = absint($atts['id']);

        if (!$post_id) {
            $post_id = $this->detect_post_id();
        }

        if (!$post_id) {
            return '';
        }

        $template = sanitize_key($atts['template']);
        $template = $template ? $template : 'default';

        $map_data = BXTR_Maps_ACF::get_map_data($post_id);

        $overrides = [
            'draw_route' => $atts['route'] !== '' ? sanitize_key($atts['route']) : '',
            'poi_enabled' => $atts['poi'] !== '' ? sanitize_key($atts['poi']) : '',
        ];

        return BXTR_Maps_Map::render($map_data['stops'], $map_data['pois'], $map_data['diagnostics'], $template, $overrides);
    }
    private function detect_post_id()
    {
        $post_id = get_the_ID();

        if ($post_id) {
            return absint($post_id);
        }

        global $post;

        if ($post instanceof WP_Post && !empty($post->ID)) {
            return absint($post->ID);
        }

        $queried_id = get_queried_object_id();

        return $queried_id ? absint($queried_id) : 0;
    }

}
