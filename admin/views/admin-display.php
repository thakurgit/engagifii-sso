<?php
/**
 * Main Settings Container & Tabs View Template
 *
 * @package Engagifii_SSO
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'configure_sso';
$options = get_option('engagifii_sso_settings', []);
$auth_endpoint = $options['auth_endpoint'] ?? 'identity.engagifii.com';
$parsed_url = parse_url($auth_endpoint, PHP_URL_HOST);
$idp_host = $parsed_url ? $parsed_url : 'identity.engagifii.com';
?>
<div class="engagifii-admin-wrap">
    <!-- Header Banner -->
    <div class="eso-header-banner">
        <div class="eso-header-content">
            <div class="eso-header-icon">
                <span class="dashicons dashicons-shield"></span>
            </div>
            <div class="eso-header-title">
                <h1>Engagifii SSO</h1>
                <p>Seamless OAuth 2.0 & OpenID Connect Single Sign-On Authentication using Engagifii Credentials</p>
            </div>
        </div>
        <div class="eso-header-badge-group">
            <div class="eso-status-pill">
                <span class="eso-status-dot"></span>
                <span>SSO Ready</span>
            </div>
        </div>
    </div>
    
    <!-- Navigation Tabs -->
    <div class="eso-nav-tabs">
        <a href="?page=engagifii-sso&tab=configure_sso" class="eso-nav-tab <?php echo ($current_tab === 'configure_sso') ? 'active' : ''; ?>">
            <span class="dashicons dashicons-admin-settings"></span>
            <span>Configure SSO</span>
        </a>
        <a href="?page=engagifii-sso&tab=login_settings" class="eso-nav-tab <?php echo ($current_tab === 'login_settings') ? 'active' : ''; ?>">
            <span class="dashicons dashicons-lock"></span>
            <span>Login Settings</span>
        </a>
        <a href="?page=engagifii-sso&tab=role_mapping" class="eso-nav-tab <?php echo ($current_tab === 'role_mapping') ? 'active' : ''; ?>">
            <span class="dashicons dashicons-groups"></span>
            <span>Attribute/Role Mapping</span>
        </a>
        <a href="?page=engagifii-sso&tab=help" class="eso-nav-tab <?php echo ($current_tab === 'help') ? 'active' : ''; ?>">
            <span class="dashicons dashicons-editor-help"></span>
            <span>Help</span>
        </a>
    </div>

    <!-- Dashboard Layout Grid -->
    <div class="eso-dashboard-layout">
        <!-- Main Content Area -->
        <div class="eso-main-content">
            <?php
            if ($current_tab === 'help') {
                include ENGAGIFII_SSO_PATH . 'admin/views/tab-help.php';
            } elseif ($current_tab === 'login_settings') {
                include ENGAGIFII_SSO_PATH . 'admin/views/tab-login-settings.php';
            } elseif ($current_tab === 'role_mapping') {
                include ENGAGIFII_SSO_PATH . 'admin/views/tab-role-mapping.php';
            } else {
                include ENGAGIFII_SSO_PATH . 'admin/views/tab-configure-sso.php';
            }
            ?>
        </div>

        <!-- Sidebar Info Column -->
        <div class="eso-sidebar">
            <!-- Shortcode Widget Card -->
            <div class="eso-card eso-sidebar-widget">
                <h3 class="eso-sidebar-title">
                    <span class="dashicons dashicons-shortcode"></span> Login Shortcode
                </h3>
                <p style="font-size:13px; color:var(--eso-text-muted); margin:0 0 10px 0;">Place this shortcode on any page, post, or widget area to render the SSO button:</p>
                <div class="eso-shortcode-box">
                    <span>[login_with_engagifii]</span>
                </div>
            </div>

            <!-- Documentation Quick Links -->
            <div class="eso-card eso-sidebar-widget">
                <h3 class="eso-sidebar-title">
                    <span class="dashicons dashicons-external"></span> Quick Resources
                </h3>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <a href="https://engagifiiweb.com/engagifii_plugins/engagifii_sso/" target="_blank" class="eso-quick-link-btn">
                        <span>Documentation</span>
                        <span class="dashicons dashicons-arrow-right-alt2"></span>
                    </a>
                    <a href="https://www.engagifii.com/contact-engagifii-support" target="_blank" class="eso-quick-link-btn">
                        <span>Engagifii Support</span>
                        <span class="dashicons dashicons-arrow-right-alt2"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
