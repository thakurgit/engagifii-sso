<?php
/**
 * Authentication Handler Class
 *
 * @package Engagifii_SSO
 */

if (!defined('ABSPATH')) {
    exit;
}

class Engagifii_SSO_Auth {

    /**
     * Redirect user to Engagifii SSO login or return JSON authorization URL for AJAX.
     */
    public function login() {
        if (!session_id()) {
            session_start();
        }
        $_SESSION['sso_test'] = $_POST['sso_test'] ?? false;
        
        $options = get_option('engagifii_sso_settings', []);
        if (empty($options['client_id']) || empty($options['auth_endpoint'])) {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                wp_send_json_error(['message' => 'SSO settings are not configured.']);
            } else {
                wp_die('SSO settings are not configured.');
            }
        }

        $authorize_url = "{$options['auth_endpoint']}?response_type=code&client_id={$options['client_id']}&redirect_uri=" . site_url() . "&scope=" . $options['scope'];
        
        if (defined('DOING_AJAX') && DOING_AJAX) {
            wp_send_json_success(['url' => $authorize_url]);
        } else {
            wp_redirect($authorize_url);
            exit;
        }
    }

    /**
     * Handle OAuth Callback from Engagifii Identity Provider.
     */
    public function handle_callback() {
        if (!isset($_GET['code'])) {
            return;
        }

        $options = get_option('engagifii_sso_settings', []);
        if (empty($options['client_id']) || empty($options['client_secret']) || empty($options['token_endpoint'])) {
            wp_die('SSO settings are incomplete.');
        }

        $redirect_page_id = isset($options['sso_after_login']) ? $options['sso_after_login'] : '';
        $redirect_url = $redirect_page_id ? get_permalink($redirect_page_id) : site_url();
        $grant_type = $options['grant_type'] ?? 'authorization_code';

        $token_response = wp_remote_post($options['token_endpoint'], [
            'body' => [
                'grant_type'    => $grant_type,
                'code'          => $_GET['code'],
                'redirect_uri'  => site_url(),
                'client_id'     => $options['client_id'],
                'client_secret' => $options['client_secret']
            ]
        ]);
        
        if (is_wp_error($token_response)) {
            wp_die('SSO Token Request Failed.');
        }

        $token_data = json_decode(wp_remote_retrieve_body($token_response), true);
        if (!isset($token_data['access_token'])) {
            wp_die('Access token missing.');
        }

        $user_response = wp_remote_get($options['userinfo_endpoint'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $token_data['access_token']
            ]
        ]);

        if (is_wp_error($user_response)) {
            wp_die('Failed to retrieve user info.');
        }

        $user_data = json_decode(wp_remote_retrieve_body($user_response), true);
        if (empty($user_data['email'])) {
            wp_die('No email provided.');
        }

        if (!session_id()) {
            session_start();
        }
        $sso_test = $_SESSION['sso_test'] ?? false;
        if (current_user_can('administrator') && $sso_test == true) {
            echo "<table border='1' cellpadding='10' cellspacing='0'>";
            echo "<tr><th>Key</th><th>Value</th></tr>";
            foreach ($user_data as $key => $value) {
                echo "<tr><td>{$key}</td><td>{$value}</td></tr>";
            }
            echo '<tr><td colspan="2"><center><a style="padding:10px 20px; background:#000; color:white" href="'.$options['logout_url'] ."?ReturnUrl=" . urlencode(home_url()).'">Log Out</a></center></td></tr></table>';
            $_SESSION['sso_test'] = false;
            die;
        } 

        setcookie("access_token", $token_data['access_token'], [
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        $user = get_user_by('email', $user_data['email']);
        if (!$user) {
            $user_login = generate_unique_username($user_data['given_name'] ?? '', $user_data['family_name'] ?? '', $user_data['email']);
            $default_role = !empty($options['default_role']) ? $options['default_role'] : 'subscriber';
            $user_id = wp_insert_user([
                'user_login' => $user_login,
                'user_email' => sanitize_email($user_data['email']),
                'user_pass'  => wp_generate_password(),
                'first_name' => $user_data['given_name'] ?? '',
                'last_name'  => $user_data['family_name'] ?? '',
                'role'       => $default_role
            ]);

            if (is_wp_error($user_id)) {
                wp_die('User creation failed.');
            }
            $user = get_user_by('ID', $user_id);
            do_action('engagifii_sso_authenticated', $user->ID, $token_data['access_token']); 
        }

        do_action('engagifii_sso_loggedIn', $user->ID); 
        wp_set_auth_cookie($user->ID);
        wp_redirect($redirect_url);
        exit;
    }
}

/**
 * Procedural backward compatibility functions
 */
if (!function_exists('engagifii_sso_login')) {
    function engagifii_sso_login() {
        $auth = new Engagifii_SSO_Auth();
        $auth->login();
    }
}

if (!function_exists('engagifii_sso_callback')) {
    function engagifii_sso_callback() {
        $auth = new Engagifii_SSO_Auth();
        $auth->handle_callback();
    }
}
