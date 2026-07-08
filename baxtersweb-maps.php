<?php
/**
 * Plugin Name: Baxtersweb Maps
 * Description: Display interactive Leaflet route maps from ACF repeater map marker fields using OpenStreetMap.
 * Version: 1.0.6
 * Author: Baxter Jones
 * Author URI: https://baxtersweb.com
 * License: GPL2+
 * Text Domain: baxtersweb-maps
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('BXTR_MAPS_VERSION', '1.0.6');
define('BXTR_MAPS_PLUGIN_FILE', __FILE__);
define('BXTR_MAPS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('BXTR_MAPS_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once BXTR_MAPS_PLUGIN_PATH . 'includes/class-plugin.php';

BXTR_Maps_Plugin::instance();
