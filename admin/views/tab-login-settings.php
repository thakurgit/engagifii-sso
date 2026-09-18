<?php
/**
 * View template for Login Settings tab (2026 Standard)
 *
 * @package Engagifii_SSO
 */

if (!defined('ABSPATH')) {
    exit;
}

$options = get_option('engagifii_sso_settings', []);
$plugin_url = ENGAGIFII_SSO_URL . 'assets/images/';
$pages = get_pages();
$menus = wp_get_nav_menus();
?>
<form method="post" action="options.php">
    <?php settings_fields('engagifii_sso_options'); ?>

    <!-- Login Behavior Card -->
    <div class="eso-card" style="margin-bottom: 24px;">
        <div class="eso-card-header">
            <div class="eso-card-header-title">
                <div class="eso-card-header-icon">
                    <span class="dashicons dashicons-admin-generic"></span>
                </div>
                <div>
                    <h2>Authentication & Redirect Behavior</h2>
                    <p class="eso-form-desc">Manage standard login form overrides and post-authentication page redirects.</p>
                </div>
            </div>
        </div>
        <div class="eso-card-body">
            <div class="eso-form-grid">
                <!-- Disable Login Form Switch -->
                <div class="eso-form-group">
                    <label class="eso-form-label">Disable Default WP Login Form</label>
                    <div class="form-check form-switch">
                        <input type="hidden" name="engagifii_sso_settings[disable_login_form]" value="0"> 
                        <input class="form-check-input" type="checkbox" id="disable_login_form" name="engagifii_sso_settings[disable_login_form]" value="1" <?php checked(!empty($options['disable_login_form'])); ?>> 
                        <label for="disable_login_form" style="font-weight: 500; font-size:14px; cursor:pointer;">
                            Force Engagifii SSO authentication for all user logins.
                        </label>
                    </div>
                </div>

                <!-- After Login Redirect -->
                <div class="eso-form-group">
                    <label class="eso-form-label" for="sso_after_login">After Login Redirect Page</label>
                    <select class="eso-select" id="sso_after_login" name="engagifii_sso_settings[sso_after_login]">
                        <option value="">-- Default (WordPress Dashboard) --</option>
                        <?php 
                        $selected_login = $options['sso_after_login'] ?? '';
                        foreach ($pages as $page) : ?>
                            <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($selected_login, $page->ID); ?>>
                                <?php echo esc_html($page->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="eso-form-desc">Select a page to redirect your users to after successful SSO login.</p>
                </div>

                <!-- After Logout Redirect -->
                <div class="eso-form-group">
                    <label class="eso-form-label" for="sso_after_logout">After Logout Redirect Page</label>
                    <select class="eso-select" id="sso_after_logout" name="engagifii_sso_settings[sso_after_logout]">
                        <option value="">-- Default (WordPress Login Page) --</option>
                        <?php 
                        $selected_logout = $options['sso_after_logout'] ?? '';
                        foreach ($pages as $page) : ?>
                            <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($selected_logout, $page->ID); ?>>
                                <?php echo esc_html($page->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="eso-form-desc">Select a page to redirect users after logging out (requires public access).</p>
                </div>
            </div>
        </div>
    </div>

    <!-- SSO Button Customization Card -->
    <div class="eso-card">
        <div class="eso-card-header">
            <div class="eso-card-header-title">
                <div class="eso-card-header-icon">
                    <span class="dashicons dashicons-format-image"></span>
                </div>
                <div>
                    <h2>SSO Button Appearance & Navigation Integration</h2>
                    <p class="eso-form-desc">Customize login button styles, upload custom logos, or embed into theme menus.</p>
                </div>
            </div>
        </div>
        <div class="eso-card-body">
            <div class="eso-form-grid">
                <!-- SSO Button Radio Options -->
                <div class="eso-form-group">
                    <label class="eso-form-label">SSO Button Style</label>
                    <?php 
                    $btn_val = $options['sso_login_button'] ?? '1';
                    $checked1 = ($btn_val === '1' || empty($btn_val)) ? 'checked' : '';
                    $checked2 = ($btn_val === '2') ? 'checked' : '';
                    ?>
                    <div class="sso_buttons">
                        <label>
                            <input type="radio" name="engagifii_sso_settings[sso_login_button]" value="1" <?php echo $checked1; ?> />
                            <img src="<?php echo esc_url($plugin_url . 'Engagifii-Login_1.png'); ?>" alt="SSO Option 1" />
                        </label>
                        <label>
                            <input type="radio" name="engagifii_sso_settings[sso_login_button]" value="2" <?php echo $checked2; ?> />
                            <img src="<?php echo esc_url($plugin_url . 'Engagifii-Login_2.png'); ?>" alt="SSO Option 2" />
                        </label>
                    </div>

                    <!-- Custom Logo Uploader -->
                    <?php 
                    $sso_logo = esc_attr($options['sso_logo'] ?? '');
                    $hidden_cls = $sso_logo ? '' : 'hidden';
                    $logo_url = $sso_logo ? wp_get_attachment_url($sso_logo) : '';
                    ?>
                    <div class="eso-logo-uploader">
                        <div style="font-weight:600; font-size:13px; color:var(--eso-text-main);">Custom SSO Logo Image:</div>
                        <div class="eso-logo-preview-box">
                            <img src="<?php echo esc_url($logo_url); ?>" class="<?php echo $hidden_cls; ?>" alt="Custom Logo Preview" />
                            <input type="hidden" name="engagifii_sso_settings[sso_logo]" class="postbox" value="<?php echo $sso_logo; ?>">
                            <button type="button" class="remove_sso_logo button <?php echo $hidden_cls; ?>">Remove Logo</button>
                            <button type="button" class="set_sso_logo button">Select / Upload Custom Logo</button>
                        </div>
                    </div>
                </div>

                <!-- Navigation Integration Switch -->
                <div class="eso-form-group">
                    <label class="eso-form-label">Navigation Menu Integration</label>
                    <div class="form-check form-switch">
                        <input type="hidden" name="engagifii_sso_settings[enable_nav_login]" value="0"> 
                        <input class="form-check-input" type="checkbox" id="enable_nav_login" name="engagifii_sso_settings[enable_nav_login]" value="1" <?php checked(!empty($options['enable_nav_login'])); ?>> 
                        <label for="enable_nav_login" style="font-weight: 500; font-size:14px; cursor:pointer;">
                            Automatically attach Engagifii login button to a theme navigation menu.
                        </label>
                    </div>
                </div>

                <!-- Select Navigation Menu -->
                <div class="eso-form-group">
                    <label class="eso-form-label" for="sso_nav_menu">Select Navigation Menu</label>
                    <select class="eso-select" id="sso_nav_menu" name="engagifii_sso_settings[sso_nav_menu]">
                        <option value="">-- Select Navigation Menu --</option>
                        <?php 
                        $selected_menu = $options['sso_nav_menu'] ?? '';
                        if (!empty($menus)) {
                            foreach ($menus as $menu) {
                                echo '<option value="' . esc_attr($menu->slug) . '" ' . selected($selected_menu, $menu->slug, false) . '>' . esc_html($menu->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <!-- Navigation Button Image Width -->
                <div class="eso-form-group">
                    <label class="eso-form-label" for="nav_button_width">Navigation Button Width</label>
                    <input class="eso-input-text" type="text" id="nav_button_width" name="engagifii_sso_settings[nav_button_width]" value="<?php echo esc_attr($options['nav_button_width'] ?? '110px'); ?>" placeholder="110px" style="max-width:260px;" />
                    <p class="eso-form-desc">Specify image width in pixels for navigation button (e.g. 110px).</p>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="eso-form-actions">
            <div>
                <?php submit_button('Save Login Settings', 'primary', 'submit', false, ['class' => 'button-primary eso-btn-primary']); ?>
            </div>
        </div>
    </div>
</form>
