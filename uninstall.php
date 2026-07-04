<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('erm_marker_color');
delete_option('erm_route_color');
