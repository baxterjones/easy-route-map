<?php
/**
 * Plugin Name: Baxtersweb Maps
 * Plugin URI: https://baxtersweb.com/baxtersweb-maps-docs/
 * Description: Create interactive OpenStreetMap route maps from ACF Pro repeater fields with connected routes, multiple stops, numbered markers and points of interest.
 * Version: 1.1.11
 * Author: Baxter Jones
 * Author URI: https://baxtersweb.com
 * License: GPL2+
 * Text Domain: baxtersweb-maps
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('BXTR_MAPS_VERSION', '1.1.10');
define('BXTR_MAPS_PLUGIN_FILE', __FILE__);
define('BXTR_MAPS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('BXTR_MAPS_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once BXTR_MAPS_PLUGIN_PATH . 'includes/class-plugin.php';

BXTR_Maps_Plugin::instance();
