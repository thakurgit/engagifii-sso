<?php
/**
 * Plugin Name: Engagifii SSO Login
 * Plugin URI:  https://crescerance.com/
 * Description: Enables SSO login with a Engagifii credentials.
 * Author:      Engagifii
 * Author URI:  https://Crescerance.com/
 * Version:     2.3.0
 * Text Domain: engagifii-sso
 * Domain Path: /languages/
 * License:     GPLv3 or later (license.txt)
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define Plugin Constants
define('ENGAGIFII_SSO_VERSION', '2.3.0');
define('ENGAGIFII_SSO_PATH', plugin_dir_path(__FILE__));
define('ENGAGIFII_SSO_URL', plugin_dir_url(__FILE__));
define('ENGAGIFII_SSO_BASENAME', plugin_basename(__FILE__));

// Require Main Orchestrator Class
require_once ENGAGIFII_SSO_PATH . 'includes/class-engagifii-sso.php';

/**
 * Begins execution of the plugin.
 */
function run_engagifii_sso() {
    $plugin = new Engagifii_SSO();
    $plugin->run();
}
run_engagifii_sso();