<?php
 /**
 * Plugin Name: Engagifii SSO Login
 * Plugin URI:  https://engagifii.com/
 * Description: Enables SSO login with a Engagifii credentials.
 * Author:      Engagifii
 * Author URI:  https://engagifii.com/
 * Version:     2.0.0
 * Text Domain: engagifii-sso
 * Domain Path: /languages/
 * License:     GPLv3 or later (license.txt)
 */

if (!defined('ABSPATH')) {
    exit;
}

// Add settings menu
function engagifii_sso_menu() {
    add_menu_page(
        'Engagifii SSO Settings', // Page title
        'Engagifii SSO', // Menu title
        'manage_options', // Capability
        'engagifii-sso', // Menu slug
        'engagifii_sso_settings_page', // Function to display page
        plugins_url('assets/images/dashboard_menu.png', __FILE__), 
        25 // Position in menu
    );
}
add_action('admin_menu', 'engagifii_sso_menu');

//merge all tabs value in array
function engagifii_sso_sanitize_settings($new_input) {
    $existing_settings = get_option('engagifii_sso_settings', []);
    return array_merge($existing_settings, $new_input);
}

// Register settings in a single array
function engagifii_sso_register_settings() {
    register_setting('engagifii_sso_options', 'engagifii_sso_settings', [
        'sanitize_callback' => 'engagifii_sso_sanitize_settings'
    ]);
}
add_action('admin_init', 'engagifii_sso_register_settings');

// Settings page content
function engagifii_sso_settings_page() {
    $current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'configure_sso';
    ?>
    <div class="wrap">
        <h1>Engagifii SSO Settings</h1>
        
        <h2 class="nav-tab-wrapper">
            <a href="?page=engagifii-sso&tab=configure_sso" class="nav-tab <?php echo ($current_tab == 'configure_sso') ? 'nav-tab-active' : ''; ?>">Configure SSO</a>
            <a href="?page=engagifii-sso&tab=login_settings" class="nav-tab <?php echo ($current_tab == 'login_settings') ? 'nav-tab-active' : ''; ?>">Login Settings</a>
            <a href="?page=engagifii-sso&tab=help" class="nav-tab <?php echo ($current_tab == 'help') ? 'nav-tab-active' : ''; ?>">Help</a>
        </h2>

        <div class="tab-content">
            <?php
            if ($current_tab == 'help') {
                engagifii_sso_help_section();
            } elseif($current_tab == 'login_settings') {
                engagifii_login_settings_settings();
			} else {
                engagifii_sso_config_settings();
            }
            ?>
        </div>
    </div>
    <?php
}

// Configure Settings
function engagifii_sso_config_settings() {
    $options = get_option('engagifii_sso_settings', []);

    $fields = [
        'client_id'        => 'Client ID',
        'client_secret'    => 'Client Secret',
        'scope'            => 'Scope',
        'auth_endpoint'    => 'Authorize Endpoint',
        'token_endpoint'   => 'Access Token Endpoint',
        'userinfo_endpoint'=> 'Get User Info Endpoint',
        'logout_url'       => 'IDP Logout URL',
        'grant_type'       => 'Grant Type',
    ];

    $defaults = [
        'client_secret'    => 'qu4al1ty.Is.Our.G4m3',
        'scope'            => 'openid profile email UsersAPI AccreditationAPI BilltrackingApi CommentApi NotesApi',
        'auth_endpoint'    => 'https://engagifii-identity-live.azurewebsites.net/connect/authorize',
        'token_endpoint'   => 'https://engagifii-identity-live.azurewebsites.net/connect/token',
        'userinfo_endpoint'=> 'https://engagifii-identity-live.azurewebsites.net/connect/userinfo',
        'logout_url'       => 'https://engagifii-identity-live.azurewebsites.net/Account/SignOut'
    ];
    ?>
        <form method="post" action="options.php">
            <?php settings_fields('engagifii_sso_options'); ?>
            <table class="form-table">
                <?php foreach ($fields as $key => $label) {
                    $value = $options[$key] ?? ($defaults[$key] ?? '');
                    echo "<tr><th>{$label}</th><td>";
					//password
					if($key == 'client_secret'){
						echo "<input style='width:500px' id='ClientSecret' type='password' name='engagifii_sso_settings[$key]' value='" . esc_attr($value) . "' /><i onclick='showClientSecret()' id='showClientSecret' class='dashicons dashicons-visibility'></i>";
					//select
					} else if($key == 'grant_type'){
						$value = $value ?: 'authorization_code';
						  echo '<select id="grantType" name="engagifii_sso_settings[' . $key . ']">
								  <option value="authorization_code" ' . selected($value, 'authorization_code', false) . '>Authorization Code</option>
								  <option value="client_credentials" ' . selected($value, 'client_credentials', false) . '>Client Credentials</option>
								  <option value="password" ' . selected($value, 'password', false) . '>Password</option>
								  <option value="refresh_token" ' . selected($value, 'refresh_token', false) . '>Refresh Token</option>
							  </select>';
					//text
					} else {
						echo "<input style='width:500px' type='text' name='engagifii_sso_settings[$key]' value='" . esc_attr($value) . "' />";
					}

                    echo "</td></tr>";
                } ?>
            </table>
            <?php submit_button(); ?>
        </form>
    <?php
}

// Login settings Section
function engagifii_login_settings_settings() {
    $options = get_option('engagifii_sso_settings', []);

    $fields = [
        'disable_login_form'       => 'Disable Default Login form',
        'sso_login_button'       => 'SSO Login Button'
    ];
    ?>
        <form method="post" action="options.php">
            <?php settings_fields('engagifii_sso_options'); ?>
            <table class="form-table">
                <?php foreach ($fields as $key => $label) {
                    $value = $options[$key] ?? ($defaults[$key] ?? '');
                    echo "<tr><th>{$label}</th><td>";
					//checkbox
					if ($key == 'disable_login_form') {
						$checked = !empty($value) ? 'checked' : '';
						echo '<div class="form-check form-switch">
							<input class="form-check-input" type="checkbox" name="engagifii_sso_settings[' . $key . ']" '. $checked .'> 
							</div>';
					//radio 
					} else if($key == 'sso_login_button'){
						 $checked1 = ($value === '1' || empty($value)) ? 'checked' : ''; 
						  $checked2 = ($value === '2') ? 'checked' : '';
						  $plugin_url = plugin_dir_url(__FILE__) . 'assets/images/';
						echo "<div class='sso_buttons'><label><input type='radio' name='engagifii_sso_settings[$key]' value='1' $checked1 /><img src='{$plugin_url}Engagifii-Login_1.png' /></label>
						<label><input type='radio' name='engagifii_sso_settings[$key]' value='2' $checked2 /><img src='{$plugin_url}Engagifii-Login_2.png' /></label></div>";
						$sso_logo = esc_attr(get_option('engagifii_sso_settings')['sso_logo'] ?? '');
						$hidden = $sso_logo ? '' : 'hidden';
						echo "<div>
								<img style='max-width:150px;height:auto;padding-bottom:8px' src='" . wp_get_attachment_url($sso_logo) . "' /><br>
								<input type='hidden' name='engagifii_sso_settings[sso_logo]' class='postbox' value='{$sso_logo}'>
								<button class='remove_sso_logo button {$hidden}'>Remove Logo</button>
								<button class='set_sso_logo button'>Add Your own Logo</button>
							  </div>";
					}

                    echo "</td></tr>";
                } ?>
            </table>
            <?php submit_button(); ?>
        </form>
    <?php
}

// Help Section
function engagifii_sso_help_section() {
    ?>
    <h3>Need Help?</h3>
    <p>For support, visit <a href="https://www.engagifii.com/contact-engagifii-support" target="_blank">Engagifii Support</a>.</p>
    <?php
}

// Redirect users to Engagifii SSO login
function engagifii_sso_login() {
	 $options = get_option('engagifii_sso_settings', []);
    if (empty($options['client_id']) || empty($options['auth_endpoint'])) {
        wp_die('SSO settings are not configured.');
    }
    $client_id = get_option('engagifii_client_id');
    $redirect_uri = site_url();
	 $authorize_url = "{$options['auth_endpoint']}?response_type=code&client_id={$options['client_id']}&redirect_uri=" . site_url() . "&scope=" . $options['scope'];
    
    
    wp_redirect($authorize_url);
    exit;
}
add_action('login_form_engagifii_sso', 'engagifii_sso_login');

// Handle SSO Callback
function engagifii_sso_callback() {
    if (!isset($_GET['code'])) {
        return;
    }
	$options = get_option('engagifii_sso_settings', []);
    if (empty($options['client_id']) || empty($options['client_secret']) || empty($options['token_endpoint'])) {
        wp_die('SSO settings are incomplete.');
    }
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
$userinfo_endpoint = get_option('userinfo_endpoint');
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

    $user = get_user_by('email', $user_data['email']);
    if (!$user) {
	  
	  $user_login = generate_unique_username($user_data['given_name'] ?? '', $user_data['family_name'] ?? '', $user_data['email']);
        $user_id = wp_insert_user([
            'user_login' => $user_login,
            'user_email' => $user_data['email'],
            'user_pass'  => wp_generate_password(),
            'first_name' => $user_data['given_name'] ?? '',
            'last_name'  => $user_data['family_name'] ?? '',
            'role'       => 'subscriber'
        ]);

        if (is_wp_error($user_id)) {
            wp_die('User creation failed.');
        }
        $user = get_user_by('ID', $user_id);
    }

    wp_set_auth_cookie($user->ID);
    wp_redirect(site_url());
    exit;
}
add_action('init', 'engagifii_sso_callback');

//create user
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

// Remove login error messages and default form fields
function custom_login_styles() {
	$options = get_option('engagifii_sso_settings', []);
	if(!$options['disable_login_form']){
		return;
		add_filter('login_errors', '__return_empty_string');
	}
    echo '<style>
            #login_error, .login .login-heading, #loginform, .wp-login-lost-password {
                display: none !important;
            }
          </style>';
}
add_action('login_enqueue_scripts', 'custom_login_styles');

// Add Custom SSO Button on Login Page
function add_engagifii_sso_button() {
	$options = get_option('engagifii_sso_settings', []);
	if($options['sso_logo']){
	 $image_url = wp_get_attachment_url($options['sso_logo']);
	}else if($options['sso_login_button']){
	 $image_url = esc_url(plugins_url('assets/images/Engagifii-Login_'.$options['sso_login_button'].'.png', __FILE__));
	}else {
	 $image_url = esc_url(plugins_url('assets/images/Engagifii-Login_1.png', __FILE__));
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
add_action('login_footer', 'add_engagifii_sso_button');

// Logout Redirect
function engagifii_sso_logout_redirect() {
	$options = get_option('engagifii_sso_settings', []);
    $site_logout_url = $options['logout_url'] ."?ReturnUrl=" . urlencode(home_url());
    wp_redirect($site_logout_url);
    exit;
}
add_action('wp_logout', 'engagifii_sso_logout_redirect');


//plugin update check
add_filter('pre_set_site_transient_update_plugins', 'engaifii_sso_plugin_update');
function engaifii_sso_plugin_update($transient) {
	
    // Only check for updates if we're not in the admin area
    if (is_admin()) {
        return $transient;
    }

    // Get the current version of the plugin
    $current_version = '2.0.0'; // Update this to your current version

    // Fetch the update information from your JSON file
    $response = wp_remote_get('https://engagifiiweb.com/engagifii_plugins/engagifii_sso/plugin-updates.json');

    if (is_wp_error($response)) {
        return $transient; // Return if there's an error
    }

    $data = json_decode(wp_remote_retrieve_body($response)); 

    // Check if there's a new version available
    if (version_compare($current_version, $data->version, '<')) {
        $transient->response[plugin_basename(__FILE__)] = (object) array(
            'slug' => 'engagifii-sso', //plugin folder name
            'plugin' => plugin_basename(__FILE__),
            'new_version' => $data->version,
            'url' => 'https://engagifiiweb.com/engagifii_plugins/engagifii_sso',
            'package' => $data->download_url,
        );
    }

    return $transient;
}

//plugin actions button
function engagifii_sso_settings_link($links) {
   $settings_link = '<a href="' . admin_url('admin.php?page=engagifii-sso') . '">Configure</a>';
   array_push($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'engagifii_sso_settings_link');

//add style css
function my_plugin_admin_styles() {
    wp_enqueue_style(
        'my-plugin-admin-style',
        plugin_dir_url(__FILE__) . 'assets/css/admin-style.css',
        array(),
        '1.0',
        'all'
    );
	 wp_enqueue_media();
	wp_enqueue_script(
        'my-plugin-admin-script',
        plugin_dir_url(__FILE__) . 'assets/js/admin-script.js',
        array('jquery'), // Dependencies (if needed)
        '1.0',
        true // Load in footer
    );
}
add_action('admin_enqueue_scripts', 'my_plugin_admin_styles');