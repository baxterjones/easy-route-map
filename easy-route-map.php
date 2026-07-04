<?php
/**
 * Plugin Name: Easy Route Map
 * Plugin URI: https://baxtersweb.com/
 * Description: Display interactive Leaflet route maps from ACF route point fields using OpenStreetMap.
 * Version: 1.0.5
 * Author: Baxtersweb
 * Author URI: https://baxtersweb.com/
 * License: GPL2+
 */

if (!defined('ABSPATH')) {
    exit;
}

define('ERM_VERSION', '1.0.5');
define('ERM_PLUGIN_FILE', __FILE__);
define('ERM_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ERM_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once ERM_PLUGIN_PATH . 'includes/class-plugin.php';

ERM_Plugin::instance();
