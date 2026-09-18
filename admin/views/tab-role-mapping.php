<?php
/**
 * View template for Attribute/Role Mapping tab (2026 Standard)
 *
 * @package Engagifii_SSO
 */

if (!defined('ABSPATH')) {
    exit;
}

$options = get_option('engagifii_sso_settings', []);
$default_role = isset($options['default_role']) ? $options['default_role'] : 'subscriber';
$wp_roles = wp_roles()->get_names();
?>
<form method="post" action="options.php">
    <?php settings_fields('engagifii_sso_options'); ?>

    <!-- Role Mapping Card -->
    <div class="eso-card">
        <div class="eso-card-header">
            <div class="eso-card-header-title">
                <div class="eso-card-header-icon">
                    <span class="dashicons dashicons-groups"></span>
                </div>
                <div>
                    <h2>Default User Role Assignment</h2>
                    <p class="eso-form-desc">Specify the default WordPress role provisioned for newly authenticated SSO users.</p>
                </div>
            </div>
        </div>
        <div class="eso-card-body">
            <div class="eso-form-grid">
                <!-- Default Role Select -->
                <div class="eso-form-group">
                    <label class="eso-form-label" for="default_role">Default WordPress Role</label>
                    <select class="eso-select" id="default_role" name="engagifii_sso_settings[default_role]" style="max-width:320px;">
                        <?php foreach ($wp_roles as $role_key => $role_name) : ?>
                            <option value="<?php echo esc_attr($role_key); ?>" <?php selected($default_role, $role_key); ?>>
                                <?php echo esc_html($role_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="eso-form-desc">Newly provisioned users will automatically be assigned this role upon initial single sign-on.</p>
                </div>

                <!-- Info Alert Callout -->
                <div style="padding:14px 18px; background:rgba(32, 149, 243, 0.06); border-left:4px solid var(--eso-primary); border-radius:6px; font-size:13px; color:var(--eso-text-main);">
                    <strong>Note:</strong> Roles are assigned only when a user logs in via Engagifii SSO for the first time. Existing users' roles in WordPress will remain unchanged.
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="eso-form-actions">
            <div>
                <?php submit_button('Save Role Mapping', 'primary', 'submit', false, ['class' => 'button-primary eso-btn-primary']); ?>
            </div>
        </div>
    </div>
</form>
