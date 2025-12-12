<?php
/**
 * Plugin Name: Nivoda API Integration
 * Plugin URI: https://wordpress.org/plugins/nivoda-api-integration/
 * Description: Professional WordPress plugin for integrating Nivoda GraphQL API - search diamonds, manage inventory, and display jewelry products
 * Version: 1.0.0
 * Author: Nivoda Team
 * Author URI: https://profiles.wordpress.org/nivoda/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: nivoda-api-integration
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Plugin version
define('NIVODA_API_VERSION', '1.0.0');

// Plugin directory path
define('NIVODA_API_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Plugin directory URL
define('NIVODA_API_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function nivoda_api_integration_activate() {
    require_once NIVODA_API_PLUGIN_DIR . 'includes/class-nivoda-activator.php';
    Nivoda_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function nivoda_api_integration_deactivate() {
    require_once NIVODA_API_PLUGIN_DIR . 'includes/class-nivoda-deactivator.php';
    Nivoda_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'nivoda_api_integration_activate');
register_deactivation_hook(__FILE__, 'nivoda_api_integration_deactivate');

/**
 * The core plugin class
 */
require NIVODA_API_PLUGIN_DIR . 'includes/class-nivoda-api.php';

/**
 * Begins execution of the plugin.
 */
function nivoda_api_integration_run() {
    $plugin = new Nivoda_API();
    $plugin->run();
}

nivoda_api_integration_run();
