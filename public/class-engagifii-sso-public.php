<?php
/**
 * Frontend & Public Functionality Class
 *
 * @package Engagifii_SSO
 */

if (!defined('ABSPATH')) {
    exit;
}

class Engagifii_SSO_Public {

    /**
     * Enqueue custom login styles to hide default WP login form if disabled in settings.
     */
    public function custom_login_styles() {
        $options = get_option('engagifii_sso_settings', []);
        if (empty($options['disable_login_form'])) {
            return;
        }
        echo '<style>
                #login_error, .login .login-heading, #loginform, .wp-login-lost-password {
                    display: none !important;
                }
              </style>';
    }

    /**
     * Inject Engagifii SSO button on standard WP Login page.
     */
    public function add_sso_button() {
        $options = get_option('engagifii_sso_settings', []);
        if (!empty($options['sso_logo'])) {
            $image_url = wp_get_attachment_url($options['sso_logo']);
        } else if (!empty($options['sso_login_button'])) {
            $image_url = esc_url(ENGAGIFII_SSO_URL . 'assets/images/Engagifii-Login_' . $options['sso_login_button'] . '.png');
        } else {
            $image_url = esc_url(ENGAGIFII_SSO_URL . 'assets/images/Engagifii-Login_1.png');
        }
        ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var loginForm = document.querySelector("#loginform");
                if (loginForm) {
                    var engagifiiButton = document.createElement('div');
                    engagifiiButton.className = "text-center";
                    engagifiiButton.style.paddingTop = "20px";
                    engagifiiButton.innerHTML = '<a class="" href="<?php echo wp_login_url(); ?>?action=engagifii_sso"><img src="<?php echo $image_url; ?>" alt="Login with Engagifii" style="max-width: 100%; height: auto;"></a>';
                    
                    loginForm.parentNode.insertBefore(engagifiiButton, loginForm.nextSibling);
                }
            });
        </script>
        <?php
    }

    /**
     * Shortcode callback for [login_with_engagifii].
     *
     * @return string Shortcode HTML output.
     */
    public function login_shortcode() {
        $options = get_option('engagifii_sso_settings', []);
        $plugin_url = ENGAGIFII_SSO_URL . 'assets/images/';
        $image_url = $plugin_url . 'Engagifii-Login.webp';
        $width = !empty($options['nav_button_width']) ? $options['nav_button_width'] : '110px';
        if (is_numeric($width)) {
            $width .= 'px';
        }

        if (is_user_logged_in()) {
            return '<a class="nav-link py-0" href="' . esc_url(wp_logout_url()) . '">Log Out <i class="fa fa-arrow-right-from-bracket ml-1"></i></a>';
        } else {
            return '<a class="nav-link px-3 py-0" title="Login with Engagifii" href="' . esc_url(site_url('/wp-login.php?action=engagifii_sso')) . '"><img alt="engagifii login" style="width: ' . esc_attr($width) . ';" src="' . esc_url($image_url) . '"/></a>';
        }
    }

    /**
     * Append login/logout link to designated WordPress navigation menu.
     *
     * @param string $items Menu items HTML.
     * @param object $args  Menu arguments.
     * @return string Modified menu items HTML.
     */
    public function add_login_logout_to_menu($items, $args) {
        $options = get_option('engagifii_sso_settings', []);
        if (empty($options['enable_nav_login'])) {
            return $items;
        }

        $selected_menu = isset($options['sso_nav_menu']) ? $options['sso_nav_menu'] : '';
        if (empty($selected_menu)) {
            return $items;
        }

        $should_add = false;

        if (is_object($args) && isset($args->menu)) {
            if (is_object($args->menu) && isset($args->menu->slug) && $args->menu->slug == $selected_menu) {
                $should_add = true;
            } elseif (is_numeric($args->menu) && $args->menu == $selected_menu) {
                $should_add = true;
            } elseif (is_string($args->menu) && $args->menu === $selected_menu) {
                $should_add = true;
            }
        }

        if ($should_add) {
            $items .= '<li class="menu-item engagifii-sso-nav-item">' . $this->login_shortcode() . '</li>';
        }
        return $items;
    }

    /**
     * Redirect user to Engagifii Identity Provider upon WordPress logout.
     */
    public function logout_redirect() {
        setcookie('access_token', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'secure'   => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        $options = get_option('engagifii_sso_settings', []);
        $logout_return_url = home_url();

        if (!is_user_logged_in() && isset($options['sso_after_logout']) && !empty($options['sso_after_logout'])) {
            $custom_logout_page = get_permalink($options['sso_after_logout']);
            if ($custom_logout_page) {
                $logout_return_url = $custom_logout_page;
            }
        }

        $site_logout_url = ($options['logout_url'] ?? '') . "?ReturnUrl=" . urlencode($logout_return_url);
        wp_redirect($site_logout_url);
        exit;
    }
}

/**
 * Procedural backward compatibility functions
 */
if (!function_exists('custom_login_styles')) {
    function custom_login_styles() {
        $public = new Engagifii_SSO_Public();
        $public->custom_login_styles();
    }
}

if (!function_exists('add_engagifii_sso_button')) {
    function add_engagifii_sso_button() {
        $public = new Engagifii_SSO_Public();
        $public->add_sso_button();
    }
}

if (!function_exists('engagifii_sso_login_shortcode')) {
    function engagifii_sso_login_shortcode() {
        $public = new Engagifii_SSO_Public();
        return $public->login_shortcode();
    }
}

if (!function_exists('engagifii_add_login_logout_to_menu')) {
    function engagifii_add_login_logout_to_menu($items, $args) {
        $public = new Engagifii_SSO_Public();
        return $public->add_login_logout_to_menu($items, $args);
    }
}

if (!function_exists('engagifii_sso_logout_redirect')) {
    function engagifii_sso_logout_redirect() {
        $public = new Engagifii_SSO_Public();
        $public->logout_redirect();
    }
}
