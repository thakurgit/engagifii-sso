<?php
/**
 * Main Plugin Orchestrator Class
 *
 * @package Engagifii_SSO
 */

if (!defined('ABSPATH')) {
    exit;
}

class Engagifii_SSO {

    /**
     * Admin instance.
     *
     * @var Engagifii_SSO_Admin
     */
    protected $admin;

    /**
     * Public instance.
     *
     * @var Engagifii_SSO_Public
     */
    protected $public;

    /**
     * Auth instance.
     *
     * @var Engagifii_SSO_Auth
     */
    protected $auth;

    /**
     * Updater instance.
     *
     * @var Engagifii_SSO_Updater
     */
    protected $updater;

    /**
     * Initialize the plugin class and set up properties.
     */
    public function __construct() {
        $this->load_dependencies();
        $this->admin   = new Engagifii_SSO_Admin();
        $this->public  = new Engagifii_SSO_Public();
        $this->auth    = new Engagifii_SSO_Auth();
        $this->updater = new Engagifii_SSO_Updater();
    }

    /**
     * Load required dependency files.
     */
    private function load_dependencies() {
        require_once ENGAGIFII_SSO_PATH . 'includes/helper-functions.php';
        require_once ENGAGIFII_SSO_PATH . 'includes/class-engagifii-sso-auth.php';
        require_once ENGAGIFII_SSO_PATH . 'includes/class-engagifii-sso-updater.php';
        require_once ENGAGIFII_SSO_PATH . 'admin/class-engagifii-sso-admin.php';
        require_once ENGAGIFII_SSO_PATH . 'public/class-engagifii-sso-public.php';
    }

    /**
     * Register all hooks with WordPress.
     */
    public function run() {
        // Admin Hooks
        add_action('admin_menu', [$this->admin, 'add_admin_menu']);
        add_action('admin_init', [$this->admin, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this->admin, 'enqueue_styles']);
        add_filter('plugin_action_links_' . ENGAGIFII_SSO_BASENAME, [$this->admin, 'add_settings_link']);

        // Auth Hooks
        add_action('login_form_engagifii_sso', [$this->auth, 'login']);
        add_action('wp_ajax_engagifii_sso_login', [$this->auth, 'login']);
        add_action('wp_ajax_nopriv_engagifii_sso_login', [$this->auth, 'login']);
        add_action('init', [$this->auth, 'handle_callback']);

        // Public / Frontend Hooks
        add_action('login_enqueue_scripts', [$this->public, 'custom_login_styles']);
        add_action('login_footer', [$this->public, 'add_sso_button']);
        add_shortcode('login_with_engagifii', [$this->public, 'login_shortcode']);
        add_filter('wp_nav_menu_items', [$this->public, 'add_login_logout_to_menu'], 10, 2);
        add_action('wp_logout', [$this->public, 'logout_redirect'], 20);

        // Plugin Updater Hooks
        add_filter('plugins_api', [$this->updater, 'plugin_info'], 20, 3);
        add_filter('pre_set_site_transient_update_plugins', [$this->updater, 'check_update']);
    }
}
