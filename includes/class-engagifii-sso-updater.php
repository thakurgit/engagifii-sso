<?php
/**
 * Plugin Update Checker Class (GitHub Releases)
 *
 * @package Engagifii_SSO
 */

if (!defined('ABSPATH')) {
    exit;
}

class Engagifii_SSO_Updater {

    /**
     * Default GitHub repository path (owner/repo).
     *
     * @var string
     */
    protected $repository = 'thakurgit/engagifii-sso';

    /**
     * Get the active repository name (allows constant override).
     *
     * @return string
     */
    public function get_repository() {
        return defined('ENGAGIFII_SSO_GITHUB_REPO') ? ENGAGIFII_SSO_GITHUB_REPO : $this->repository;
    }

    /**
     * Fetch recent releases from GitHub REST API with transient caching.
     *
     * @param int  $per_page    Number of releases to retrieve (default 2).
     * @param bool $force_check Whether to bypass transient cache.
     * @return array Array of GitHub release objects or empty array.
     */
    public function get_releases($per_page = 2, $force_check = false) {
        $transient_key = 'engagifii_sso_github_releases_' . intval($per_page);

        if (!$force_check) {
            $cached = get_transient($transient_key);
            if ($cached !== false) {
                return is_array($cached) ? $cached : [];
            }
        }

        $repo    = $this->get_repository();
        $api_url = "https://api.github.com/repos/{$repo}/releases?per_page=" . intval($per_page);

        $headers = [
            'Accept'     => 'application/vnd.github.v3+json',
            'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url(),
        ];

        if (defined('ENGAGIFII_SSO_GITHUB_TOKEN') && ENGAGIFII_SSO_GITHUB_TOKEN) {
            $headers['Authorization'] = 'Bearer ' . ENGAGIFII_SSO_GITHUB_TOKEN;
        }

        $response = wp_remote_get($api_url, [
            'timeout' => 10,
            'headers' => $headers,
        ]);

        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            // Cache empty array for 15 minutes to avoid spamming GitHub API on 404 or errors
            set_transient($transient_key, [], 15 * MINUTE_IN_SECONDS);
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body);

        if (!is_array($data)) {
            set_transient($transient_key, [], 15 * MINUTE_IN_SECONDS);
            return [];
        }

        // Cache valid releases for 6 hours
        set_transient($transient_key, $data, 6 * HOUR_IN_SECONDS);

        return $data;
    }

    /**
     * Get the latest release object from GitHub.
     *
     * @param bool $force_check Whether to bypass transient cache.
     * @return object|null Latest release object or null on failure.
     */
    public function get_latest_release($force_check = false) {
        $releases = $this->get_releases(2, $force_check);
        return (!empty($releases) && is_object($releases[0])) ? $releases[0] : null;
    }

    /**
     * Determine best download zip package URL from GitHub release.
     *
     * Prioritizes attached .zip release assets, then falls back to zipball.
     *
     * @param object $release GitHub release object.
     * @return string Download URL.
     */
    protected function get_download_package($release) {
        if (!empty($release->assets) && is_array($release->assets)) {
            foreach ($release->assets as $asset) {
                if (!empty($asset->browser_download_url) && strtolower(pathinfo($asset->name, PATHINFO_EXTENSION)) === 'zip') {
                    return $asset->browser_download_url;
                }
            }
        }

        if (!empty($release->zipball_url)) {
            return $release->zipball_url;
        }

        return 'https://github.com/' . $this->get_repository() . '/archive/refs/tags/' . $release->tag_name . '.zip';
    }

    /**
     * Convert simple markdown formatting to HTML for WordPress modal.
     *
     * @param string $markdown Raw markdown content.
     * @return string Formatted HTML.
     */
    protected function parse_markdown($markdown) {
        if (empty($markdown)) {
            return '';
        }

        // Escape raw HTML for security
        $text = esc_html($markdown);

        // Convert markdown headers
        $text = preg_replace('/^### (.*?)$/m', '<h5 style="margin: 12px 0 6px 0; font-size: 13px;">$1</h5>', $text);
        $text = preg_replace('/^## (.*?)$/m', '<h4 style="margin: 14px 0 8px 0; font-size: 14px;">$1</h4>', $text);
        $text = preg_replace('/^# (.*?)$/m', '<h3 style="margin: 16px 0 10px 0; font-size: 15px;">$1</h3>', $text);

        // Bold & Italic
        $text = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $text);
        $text = preg_replace('/(?<!\*)\*(?!\*)(.*?)(?<!\*)\*(?!\*)/s', '<em>$1</em>', $text);

        // Inline code
        $text = preg_replace('/`(.*?)`/', '<code>$1</code>', $text);

        // Convert list items
        $text = preg_replace('/^[*-] (.*?)$/m', '<li>$1</li>', $text);
        $text = preg_replace('/(<li>.*?<\/li>)/s', '<ul>$1</ul>', $text);
        $text = str_replace("</ul>\n<ul>", "\n", $text);
        $text = str_replace("</ul><ul>", "", $text);

        return make_clickable(wpautop($text));
    }

    /**
     * Build changelog HTML displaying last 2 releases and link to all GitHub changelogs.
     *
     * @param array $releases Array of GitHub release objects.
     * @return string Rendered HTML changelog.
     */
    protected function build_changelog_html($releases) {
        $repo         = $this->get_repository();
        $releases_url = 'https://github.com/' . $repo . '/releases';
        $changelog    = '';

        if (!empty($releases) && is_array($releases)) {
            $count = 0;
            foreach ($releases as $rel) {
                if ($count >= 2) {
                    break;
                }

                $title = !empty($rel->name) ? $rel->name : $rel->tag_name;
                $date  = !empty($rel->published_at) ? gmdate('F j, Y', strtotime($rel->published_at)) : '';
                $body  = !empty($rel->body) ? $this->parse_markdown($rel->body) : '<p>No release notes provided for this version.</p>';

                $changelog .= '<div class="engagifii-release-entry" style="margin-bottom: 22px; padding-bottom: 16px; border-bottom: 1px solid #eee;">';
                $changelog .= '<h4 style="margin: 0 0 8px 0; font-size: 15px; color: #1d2327;">';
                $changelog .= esc_html($title);
                if ($date) {
                    $changelog .= ' <span style="font-size: 12px; font-weight: normal; color: #646970;">(' . esc_html($date) . ')</span>';
                }
                $changelog .= '</h4>';
                $changelog .= $body;
                $changelog .= '</div>';

                $count++;
            }
        }

        if (empty($changelog)) {
            $changelog = '<p>No recent release notes found.</p>';
        }

        // Link to all changelogs on GitHub
        $changelog .= '<div class="engagifii-all-releases-link" style="margin-top: 16px;">';
        $changelog .= '<p><a href="' . esc_url($releases_url) . '" target="_blank" rel="noopener noreferrer" style="font-weight: 600; text-decoration: none;">';
        $changelog .= 'View all releases &amp; full changelog on GitHub &rarr;';
        $changelog .= '</a></p>';
        $changelog .= '</div>';

        return $changelog;
    }

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

        if (!isset($args->slug) || ($args->slug !== $plugin_dirname && $args->slug !== 'engagifii-sso')) {
            return $res;
        }

        $releases = $this->get_releases(2);
        if (empty($releases) || !is_object($releases[0])) {
            return $res;
        }

        $release      = $releases[0];
        $version      = ltrim($release->tag_name, 'v');
        $download_url = $this->get_download_package($release);

        $res = new stdClass();
        $res->name           = !empty($release->name) ? $release->name : 'Engagifii SSO Login';
        $res->slug           = 'engagifii-sso';
        $res->version        = $version;
        $res->author         = '<a href="https://crescerance.com/">Crescerance</a>';
        $res->author_profile = 'https://crescerance.com/';
        $res->homepage       = $release->html_url ?? ('https://github.com/' . $this->get_repository());
        $res->download_link  = $download_url;
        $res->trunk          = $download_url;
        $res->last_updated   = !empty($release->published_at) ? gmdate('Y-m-d H:i:s', strtotime($release->published_at)) : '';

        // Display the last 2 releases from GitHub followed by link to all changelogs
        $changelog = $this->build_changelog_html($releases);

        $res->sections = [
            'description'  => 'Enables Single Sign-On (SSO) login with Engagifii credentials via OAuth 2.0 and OpenID Connect.',
            'installation' => '1. Upload plugin to the <code>/wp-content/plugins/</code> directory.<br>2. Activate via WordPress Plugins menu.<br>3. Configure credentials in Engagifii SSO settings.',
            'changelog'    => $changelog,
        ];

        return $res;
    }

    /**
     * Check GitHub releases for plugin updates and inject transient data.
     *
     * @param object $transient Site transient object.
     * @return object Updated transient.
     */
    public function check_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }

        $release = $this->get_latest_release();
        if (!$release || empty($release->tag_name)) {
            return $transient;
        }

        $current_version = defined('ENGAGIFII_SSO_VERSION') ? ENGAGIFII_SSO_VERSION : '1.0.0';
        $new_version     = ltrim($release->tag_name, 'v');
        $basename        = defined('ENGAGIFII_SSO_BASENAME') ? ENGAGIFII_SSO_BASENAME : plugin_basename(__FILE__);
        $slug            = 'engagifii-sso';
        $package_url     = $this->get_download_package($release);

        $plugin_data = (object) [
            'slug'        => $slug,
            'plugin'      => $basename,
            'new_version' => $new_version,
            'url'         => $release->html_url ?? ('https://github.com/' . $this->get_repository()),
            'package'     => $package_url,
        ];

        if (version_compare($current_version, $new_version, '<')) {
            $transient->response[$basename] = $plugin_data;
        } else {
            $transient->no_update[$basename] = $plugin_data;
        }

        return $transient;
    }

    /**
     * Ensure extracted GitHub zipball directory is renamed to the expected plugin slug.
     *
     * @param string      $source        File path of temporary unzipped directory.
     * @param string      $remote_source Remote source path.
     * @param WP_Upgrader $upgrader      Instance of WP_Upgrader.
     * @param array       $hook_extra    Extra arguments passed to hooked filter.
     * @return string Modified source directory path.
     */
    public function fix_folder_name($source, $remote_source, $upgrader, $hook_extra = []) {
        global $wp_filesystem;

        $plugin_basename = defined('ENGAGIFII_SSO_BASENAME') ? ENGAGIFII_SSO_BASENAME : plugin_basename(__FILE__);

        if (!isset($hook_extra['plugin']) || $hook_extra['plugin'] !== $plugin_basename) {
            return $source;
        }

        $expected_slug       = 'engagifii-sso';
        $proper_destination = trailingslashit($remote_source) . $expected_slug . '/';

        if (trailingslashit($source) !== $proper_destination && is_object($wp_filesystem)) {
            if ($wp_filesystem->move($source, $proper_destination)) {
                return $proper_destination;
            }
        }

        return $source;
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
