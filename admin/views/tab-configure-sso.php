<?php
/**
 * View template for Configure SSO tab (2026 Standard)
 *
 * @package Engagifii_SSO
 */

if (!defined('ABSPATH')) {
    exit;
}

$options = get_option('engagifii_sso_settings', []);

$defaults = [
    'client_secret'     => 'qu4al1ty.Is.Our.G4m3',
    'scope'             => 'openid profile email UsersAPI AccreditationAPI BilltrackingApi CommentApi NotesApi',
    'auth_endpoint'     => 'https://identity.engagifii.com/connect/authorize',
    'token_endpoint'    => 'https://identity.engagifii.com/connect/token',
    'userinfo_endpoint' => 'https://identity.engagifii.com/connect/userinfo',
    'logout_url'        => 'https://identity.engagifii.com/Account/SignOut'
];

$get_val = function($key) use ($options, $defaults) {
    return $options[$key] ?? ($defaults[$key] ?? '');
};
?>
<form method="post" action="options.php">
    <?php settings_fields('engagifii_sso_options'); ?>

    <!-- OAuth2 Credentials Card -->
    <div class="eso-card" style="margin-bottom: 24px;">
        <div class="eso-card-header">
            <div class="eso-card-header-title">
                <div class="eso-card-header-icon">
                    <span class="dashicons dashicons-lock"></span>
                </div>
                <div>
                    <h2>OAuth 2.0 & Identity Credentials</h2>
                    <p class="eso-form-desc">Provide your Engagifii Client ID, Client Secret, and required scopes.</p>
                </div>
            </div>
        </div>
        <div class="eso-card-body">
            <div class="eso-form-grid">
                <!-- Client ID -->
                <div class="eso-form-group">
                    <label class="eso-form-label" for="client_id">Client ID</label>
                    <input class="eso-input-text" type="text" id="client_id" name="engagifii_sso_settings[client_id]" value="<?php echo esc_attr($get_val('client_id')); ?>" placeholder="e.g. engagifii_wp_client" />
                    <p class="eso-form-desc">Your unique application Client ID registered with Engagifii Identity Provider.</p>
                </div>

                <!-- Client Secret -->
                <div class="eso-form-group">
                    <label class="eso-form-label" for="ClientSecret">Client Secret</label>
                    <div class="eso-input-password-wrapper">
                        <input class="eso-input-text" id="ClientSecret" type="password" name="engagifii_sso_settings[client_secret]" value="<?php echo esc_attr($get_val('client_secret')); ?>" placeholder="Enter Client Secret" />
                        <i onclick="showClientSecret()" id="showClientSecret" class="dashicons dashicons-visibility" title="Toggle visibility"></i>
                    </div>
                    <p class="eso-form-desc">Keep your client secret confidential. Click the eye icon to preview.</p>
                </div>

                <!-- Scope -->
                <div class="eso-form-group">
                    <label class="eso-form-label" for="scope">Requested Scope</label>
                    <input class="eso-input-text" type="text" id="scope" name="engagifii_sso_settings[scope]" value="<?php echo esc_attr($get_val('scope')); ?>" />
                    <p class="eso-form-desc">Space-separated OAuth2 / OpenID scopes requested during authorization.</p>
                </div>

                <!-- Grant Type -->
                <div class="eso-form-group">
                    <label class="eso-form-label" for="grantType">Grant Type</label>
                    <?php $grant_val = $get_val('grant_type') ?: 'authorization_code'; ?>
                    <select class="eso-select" id="grantType" name="engagifii_sso_settings[grant_type]">
                        <option value="authorization_code" <?php selected($grant_val, 'authorization_code'); ?>>Authorization Code (Recommended)</option>
                        <option value="client_credentials" <?php selected($grant_val, 'client_credentials'); ?>>Client Credentials</option>
                        <option value="password" <?php selected($grant_val, 'password'); ?>>Password</option>
                        <option value="refresh_token" <?php selected($grant_val, 'refresh_token'); ?>>Refresh Token</option>
                    </select>
                    <p class="eso-form-desc">Select the OAuth2 workflow type supported by your Engagifii tenant.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Provider Endpoints Card -->
    <div class="eso-card">
        <div class="eso-card-header">
            <div class="eso-card-header-title">
                <div class="eso-card-header-icon">
                    <span class="dashicons dashicons-admin-links"></span>
                </div>
                <div>
                    <h2>Identity Provider Endpoints</h2>
                    <p class="eso-form-desc">Configure authorization, token exchange, and user info URLs.</p>
                </div>
            </div>
        </div>
        <div class="eso-card-body">
            <div class="eso-form-grid">
                <!-- Authorize Endpoint -->
                <div class="eso-form-group">
                    <label class="eso-form-label" for="auth_endpoint">Authorize Endpoint</label>
                    <input class="eso-input-text" type="text" id="auth_endpoint" name="engagifii_sso_settings[auth_endpoint]" value="<?php echo esc_attr($get_val('auth_endpoint')); ?>" />
                </div>

                <!-- Access Token Endpoint -->
                <div class="eso-form-group">
                    <label class="eso-form-label" for="token_endpoint">Access Token Endpoint</label>
                    <input class="eso-input-text" type="text" id="token_endpoint" name="engagifii_sso_settings[token_endpoint]" value="<?php echo esc_attr($get_val('token_endpoint')); ?>" />
                </div>

                <!-- User Info Endpoint -->
                <div class="eso-form-group">
                    <label class="eso-form-label" for="userinfo_endpoint">Get User Info Endpoint</label>
                    <input class="eso-input-text" type="text" id="userinfo_endpoint" name="engagifii_sso_settings[userinfo_endpoint]" value="<?php echo esc_attr($get_val('userinfo_endpoint')); ?>" />
                </div>

                <!-- IDP Logout URL -->
                <div class="eso-form-group">
                    <label class="eso-form-label" for="logout_url">IDP Logout URL</label>
                    <input class="eso-input-text" type="text" id="logout_url" name="engagifii_sso_settings[logout_url]" value="<?php echo esc_attr($get_val('logout_url')); ?>" />
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="eso-form-actions">
            <div>
                <?php submit_button('Save Settings', 'primary', 'submit', false, ['class' => 'button-primary eso-btn-primary']); ?>
            </div>
            <div>
                <button id="sso-test-btn" type="button" class="eso-btn-secondary">
                    <span class="dashicons dashicons-update" style="vertical-align: middle; margin-right:4px;"></span> Test Configuration
                </button>
            </div>
        </div>
    </div>
</form>
