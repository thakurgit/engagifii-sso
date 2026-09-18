<?php
/**
 * Plugin Update Checker Class
 *
 * @package Engagifii_SSO
 */

if (!defined('ABSPATH')) {
    exit;
}

class Engagifii_SSO_Updater {

    /**
     * Provide plugin information for WordPress update modal.
     *
     * @param object $res    Default response.
     * @param string $action Requested action.
     * @param object $args   Arguments.
     * @return object Updated plugin information response.
     */
    public function plugin_info($res, $action, $args) {
        if ('plugin_information' !== $action) {
            return $res;
        }

        $plugin_basename = defined('ENGAGIFII_SSO_BASENAME') ? ENGAGIFII_SSO_BASENAME : plugin_basename(__FILE__);
        $plugin_dirname  = dirname($plugin_basename);

        if ($plugin_dirname !== $args->slug && 'engagifii-sso' !== $args->slug) {
            return $res;
        }

        $remote = wp_remote_get(
            'https://engagifiiweb.com/engagifii_plugins/engagifii_sso/plugin-updates.json',
            [
                'timeout' => 10,
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]
        );

        if (
            is_wp_error($remote)
            || 200 !== wp_remote_retrieve_response_code($remote)
            || empty(wp_remote_retrieve_body($remote))
        ) {
            return $res;
        }

        $remote = json_decode(wp_remote_retrieve_body($remote));
        $res = new stdClass();
        $res->name           = $remote->name;
        $res->slug           = $remote->slug;
        $res->author         = $remote->author;
        $res->author_profile = $remote->author_profile;
        $res->version        = $remote->version;
        $res->tested         = $remote->tested;
        $res->requires       = $remote->requires;
        $res->requires_php   = $remote->requires_php;
        $res->download_link  = $remote->download_url;
        $res->trunk          = $remote->download_url;
        $res->last_updated   = $remote->last_updated;
        $res->sections       = [
            'description'  => $remote->sections->description,
            'installation' => $remote->sections->installation,
            'changelog'    => $remote->sections->changelog
        ];

        if (!empty($remote->sections->screenshots)) {
            $res->sections['screenshots'] = $remote->sections->screenshots;
        }

        $res->banners = [
            'low'  => $remote->banners->low,
            'high' => $remote->banners->high
        ];

        return $res;
    }

    /**
     * Check remote server for plugin updates and inject transient data.
     *
     * @param object $transient Site transient object.
     * @return object Updated transient.
     */
    public function check_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }

        $response = wp_remote_get('https://engagifiiweb.com/engagifii_plugins/engagifii_sso/plugin-updates.json');

        if (is_wp_error($response)) {
            return $transient;
        }

        $data = json_decode(wp_remote_retrieve_body($response));
        if (empty($data) || empty($data->version)) {
            return $transient;
        }

        $version = defined('ENGAGIFII_SSO_VERSION') ? ENGAGIFII_SSO_VERSION : '1.0.0';
        $basename = defined('ENGAGIFII_SSO_BASENAME') ? ENGAGIFII_SSO_BASENAME : plugin_basename(__FILE__);

        if (version_compare($version, $data->version, '<')) {
            $transient->response[$basename] = (object) [
                'slug'        => 'engagifii-sso',
                'plugin'      => $basename,
                'new_version' => $data->version,
                'url'         => 'https://engagifiiweb.com/engagifii_plugins/engagifii_sso',
                'package'     => $data->download_url,
            ];
        }

        return $transient;
    }
}

/**
 * Procedural backward compatibility functions
 */
if (!function_exists('engagifii_sso_plugin_info')) {
    function engagifii_sso_plugin_info($res, $action, $args) {
        $updater = new Engagifii_SSO_Updater();
        return $updater->plugin_info($res, $action, $args);
    }
}

if (!function_exists('engaifii_sso_plugin_update')) {
    function engaifii_sso_plugin_update($transient) {
        $updater = new Engagifii_SSO_Updater();
        return $updater->check_update($transient);
    }
}
