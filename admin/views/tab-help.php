<?php
/**
 * View template for Help tab (2026 Standard)
 *
 * @package Engagifii_SSO
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="eso-card">
    <div class="eso-card-header">
        <div class="eso-card-header-title">
            <div class="eso-card-header-icon">
                <span class="dashicons dashicons-editor-help"></span>
            </div>
            <div>
                <h2>Help & Support Center</h2>
                <p class="eso-form-desc">Everything you need to successfully deploy and manage Engagifii Single Sign-On.</p>
            </div>
        </div>
    </div>
    <div class="eso-card-body">
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
            <!-- Documentation Card -->
            <div style="padding:20px; border:1px solid var(--eso-border); border-radius:var(--eso-radius-sm); background:#f8fafc; display:flex; flex-direction:column; justify-content:space-between;">
                <div>
                    <span class="dashicons dashicons-book-alt" style="font-size:28px; width:28px; height:28px; color:var(--eso-primary); margin-bottom:10px;"></span>
                    <h3 style="margin:0 0 6px 0; font-size:16px;">Documentation</h3>
                    <p style="margin:0 0 16px 0; font-size:13px; color:var(--eso-text-muted);">Explore full setup guides, OAuth endpoint specifications, and configuration parameters.</p>
                </div>
                <a href="https://engagifiiweb.com/engagifii_plugins/engagifii_sso/" target="_blank" class="eso-btn-primary" style="display:inline-block; text-align:center; text-decoration:none;">
                    View Documentation &rarr;
                </a>
            </div>

            <!-- Support Card -->
            <div style="padding:20px; border:1px solid var(--eso-border); border-radius:var(--eso-radius-sm); background:#f8fafc; display:flex; flex-direction:column; justify-content:space-between;">
                <div>
                    <span class="dashicons dashicons-email-alt" style="font-size:28px; width:28px; height:28px; color:var(--eso-primary); margin-bottom:10px;"></span>
                    <h3 style="margin:0 0 6px 0; font-size:16px;">Contact Support</h3>
                    <p style="margin:0 0 16px 0; font-size:13px; color:var(--eso-text-muted);">Need assistance with identity server integration? Get direct support from Engagifii team.</p>
                </div>
                <a href="https://www.engagifii.com/contact-engagifii-support" target="_blank" class="eso-btn-secondary" style="display:inline-block; text-align:center; text-decoration:none;">
                    Get Support &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
