<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$uninstall_mode = get_option('erm_uninstall_mode', 'keep');

if ($uninstall_mode !== 'remove') {
    return;
}

$field_groups = get_posts([
    'post_type'      => 'acf-field-group',
    'post_status'    => 'any',
    'name'           => 'group_erm_route_map',
    'posts_per_page' => 1,
    'fields'         => 'ids',
]);

foreach ($field_groups as $field_group_id) {
    $fields = get_posts([
        'post_type'      => 'acf-field',
        'post_status'    => 'any',
        'post_parent'    => (int) $field_group_id,
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);

    foreach ($fields as $field_id) {
        wp_delete_post((int) $field_id, true);
    }

    wp_delete_post((int) $field_group_id, true);
}

delete_option('erm_marker_color');
delete_option('erm_route_color');
delete_option('erm_map_height');
delete_option('erm_border_radius');
delete_option('erm_marker_label');
delete_option('erm_custom_marker_label');
delete_option('erm_field_post_types');
delete_option('erm_acf_field_setup_done');
delete_option('erm_uninstall_mode');
