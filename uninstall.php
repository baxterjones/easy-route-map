<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$bxtr_uninstall_mode = get_option('bxtr_uninstall_mode', 'keep');

if ($bxtr_uninstall_mode !== 'remove') {
    return;
}

$bxtr_field_groups = get_posts([
    'post_type'      => 'acf-field-group',
    'post_status'    => 'any',
    'name'           => 'group_bxtr_maps',
    'posts_per_page' => 1,
    'fields'         => 'ids',
]);

foreach ($bxtr_field_groups as $bxtr_field_group_id) {
    $bxtr_fields = get_posts([
        'post_type'      => 'acf-field',
        'post_status'    => 'any',
        'post_parent'    => (int) $bxtr_field_group_id,
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);

    foreach ($bxtr_fields as $bxtr_field_id) {
        wp_delete_post((int) $bxtr_field_id, true);
    }

    wp_delete_post((int) $bxtr_field_group_id, true);
}

delete_option('bxtr_marker_color');
delete_option('bxtr_route_color');
delete_option('bxtr_marker_number_color');
delete_option('bxtr_map_height');
delete_option('bxtr_border_radius');
delete_option('bxtr_marker_sequence');
delete_option('bxtr_field_post_types');
delete_option('bxtr_acf_field_setup_done');
delete_option('bxtr_field_group_mode');
delete_option('bxtr_field_group_key');
delete_option('bxtr_uninstall_mode');
delete_option('bxtr_draw_route');
delete_option('bxtr_poi_enabled');
delete_option('bxtr_poi_marker_color');
delete_option('bxtr_poi_icon_mode');
delete_option('bxtr_poi_theme_icon_class');
delete_option('bxtr_poi_default_icon');
delete_option('bxtr_map_tile_style');
delete_option('bxtr_ors_api_key');
delete_option('bxtr_ors_status');
delete_option('bxtr_ors_status_message');
delete_option('bxtr_ors_tested_at');
delete_option('bxtr_cluster_pois');
