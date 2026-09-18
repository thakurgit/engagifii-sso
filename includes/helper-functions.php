<?php
/**
 * Helper functions for Engagifii SSO Login.
 *
 * @package Engagifii_SSO
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('generate_unique_username')) {
    /**
     * Generate a unique username based on first/last name or email.
     *
     * @param string $first_name First name of the user.
     * @param string $last_name  Last name of the user.
     * @param string $email      Email address of the user.
     * @return string Unique WordPress username.
     */
    function generate_unique_username($first_name, $last_name, $email) {
        global $wpdb;

        // Generate base username
        if (!empty($first_name) && !empty($last_name)) {
            $username = sanitize_user(strtolower($first_name . '-' . $last_name));
        } else {
            $email_parts = explode('@', $email);
            $username = sanitize_user(strtolower($email_parts[0]));
        }

        // Ensure username is unique
        $original_username = $username;
        $counter = 1;
        while (username_exists($username)) {
            $username = $original_username . $counter;
            $counter++;
        }

        return $username;
    }
}
