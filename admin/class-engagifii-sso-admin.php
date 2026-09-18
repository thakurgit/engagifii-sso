<?php
/**
 * Admin Dashboard & Settings Class
 *
 * @package Engagifii_SSO
 */

if (!defined('ABSPATH')) {
    exit;
}

class Engagifii_SSO_Admin {

    /**
     * Add Engagifii SSO page to WordPress admin menu.
     */
    public function add_admin_menu() {
        add_menu_page(
            'Engagifii SSO Settings',
            'Engagifii SSO',
            'manage_options',
            'engagifii-sso',
            [$this, 'display_settings_page'],
            ENGAGIFII_SSO_URL . 'assets/images/dashboard_menu.png',
            25
        );
    }

    /**
     * Merge all tabs values in array upon saving.
     *
     * @param array $new_input Submitted options.
     * @return array Merged settings array.
     */
    public function sanitize_settings($new_input) {
        $existing_settings = get_option('engagifii_sso_settings', []);
        return array_merge($existing_settings, (array) $new_input);
    }

    /**
     * Register plugin settings with WordPress Settings API.
     */
    public function register_settings() {
        register_setting('engagifii_sso_options', 'engagifii_sso_settings', [
            'sanitize_callback' => [$this, 'sanitize_settings']
        ]);
    }

    /**
     * Display the admin settings page view.
     */
    public function display_settings_page() {
        include ENGAGIFII_SSO_PATH . 'admin/views/admin-display.php';
    }

    /**
     * Add "Configure" link to plugin listing on Plugins page.
     *
     * @param array $links Action links.
     * @return array Modified action links.
     */
    public function add_settings_link($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=engagifii-sso') . '">Configure</a>';
        array_push($links, $settings_link);
        return $links;
    }

    /**
     * Enqueue stylesheets and JavaScripts for admin settings page.
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'my-plugin-admin-style',
            ENGAGIFII_SSO_URL . 'assets/css/admin-style.css',
            [],
            '1.0',
            'all'
        );
        wp_enqueue_media();
        wp_enqueue_script(
            'engagifii-sso',
            ENGAGIFII_SSO_URL . 'assets/js/admin-script.js',
            ['jquery'],
            '1.1',
            true
        );
        wp_localize_script(
            'engagifii-sso',
            'engagifii_sso_ajaxURL',
            admin_url('admin-ajax.php?action=engagifii_sso_login')
        );
    }
}

/**
 * Procedural backward compatibility functions
 */
if (!function_exists('engagifii_sso_menu')) {
    function engagifii_sso_menu() {
        $admin = new Engagifii_SSO_Admin();
        $admin->add_admin_menu();
    }
}

if (!function_exists('engagifii_sso_sanitize_settings')) {
    function engagifii_sso_sanitize_settings($new_input) {
        $admin = new Engagifii_SSO_Admin();
        return $admin->sanitize_settings($new_input);
    }
}

if (!function_exists('engagifii_sso_register_settings')) {
    function engagifii_sso_register_settings() {
        $admin = new Engagifii_SSO_Admin();
        $admin->register_settings();
    }
}

if (!function_exists('engagifii_sso_settings_page')) {
    function engagifii_sso_settings_page() {
        $admin = new Engagifii_SSO_Admin();
        $admin->display_settings_page();
    }
}

if (!function_exists('engagifii_sso_config_settings')) {
    function engagifii_sso_config_settings() {
        include ENGAGIFII_SSO_PATH . 'admin/views/tab-configure-sso.php';
    }
}

if (!function_exists('engagifii_login_settings_settings')) {
    function engagifii_login_settings_settings() {
        include ENGAGIFII_SSO_PATH . 'admin/views/tab-login-settings.php';
    }
}

if (!function_exists('engagifii_role_mapping_settings')) {
    function engagifii_role_mapping_settings() {
        include ENGAGIFII_SSO_PATH . 'admin/views/tab-role-mapping.php';
    }
}

if (!function_exists('engagifii_sso_help_section')) {
    function engagifii_sso_help_section() {
        include ENGAGIFII_SSO_PATH . 'admin/views/tab-help.php';
    }
}

if (!function_exists('engagifii_sso_settings_link')) {
    function engagifii_sso_settings_link($links) {
        $admin = new Engagifii_SSO_Admin();
        return $admin->add_settings_link($links);
    }
}

if (!function_exists('my_plugin_admin_styles')) {
    function my_plugin_admin_styles() {
        $admin = new Engagifii_SSO_Admin();
        $admin->enqueue_styles();
    }
}
