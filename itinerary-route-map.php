<?php
/**
 * Plugin Name: Itinerary Route Map
 * Plugin URI: https://baxtersweb.com/
 * Description: Display an interactive Leaflet route map from an ACF itinerary repeater.
 * Version: 1.0.5
 * Author: Baxtersweb
 * Author URI: https://baxtersweb.com/
 * License: GPL2+
 */

if (!defined('ABSPATH')) {
    exit;
}

define('IRM_VERSION', '1.0.5');
define('IRM_PLUGIN_FILE', __FILE__);
define('IRM_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('IRM_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once IRM_PLUGIN_PATH . 'includes/class-plugin.php';

IRM_Plugin::instance();
