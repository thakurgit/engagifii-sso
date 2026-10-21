# Engagifii SSO Login

[![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%20%7C%208.0%2B-purple.svg)](https://php.net/)
[![Version](https://img.shields.io/badge/version-2.3.1-green.svg)](https://github.com/thakurgit/engagifii-sso)
[![License](https://img.shields.io/badge/license-GPLv3-orange.svg)](https://www.gnu.org/licenses/gpl-3.0.html)

**Engagifii SSO Login** enables seamless Single Sign-On (SSO) authentication for WordPress websites using **Engagifii** credentials via OAuth 2.0 and OpenID Connect.

With this plugin, users can authenticate using their existing Engagifii organizational accounts, while administrators maintain granular control over user provisioning, role assignments, redirects, and login button appearance.

---

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
  - [1. Configure SSO Credentials](#1-configure-sso-credentials)
  - [2. Login & Redirect Settings](#2-login--redirect-settings)
  - [3. Role Mapping](#3-role-mapping)
  - [4. Testing the Configuration](#4-testing-the-configuration)
- [Shortcodes](#shortcodes)
- [Developer Hooks & API](#developer-hooks--api)
- [Directory Structure](#directory-structure)
- [Troubleshooting](#troubleshooting)
- [Changelog](CHANGELOG.md)
- [License & Support](#license--support)

---

## Features

- **OAuth 2.0 / OpenID Connect Protocol**: Securely authenticate against Engagifii Identity Provider (`identity.engagifii.com`).
- **Auto User Provisioning**: Automatically registers new WordPress users upon their initial SSO login using verified profile data (First Name, Last Name, Email).
- **Default Role Assignment**: Choose the default WordPress user role (e.g., Subscriber, Contributor) applied to newly provisioned users.
- **Login Form Enforcement**: Optionally disable the default WordPress username/password login form to enforce Engagifii SSO exclusively.
- **Custom Post-Login & Post-Logout Redirection**: Route authenticated users to specific landing pages or dashboard pages after login and logout.
- **Button Customization & Media Uploader**: Choose from preset button designs or upload custom brand logos.
- **Theme Navigation Integration**: Automatically inject the SSO Login/Logout link directly into any registered WordPress navigation menu.
- **Shortcode Integration**: Embed the login/logout button anywhere using `[login_with_engagifii]`.
- **Developer Action Hooks**: Extend authentication logic with hooks that pass the user ID and OAuth `access_token`.
- **Built-in Config Tester**: Verify identity server connectivity and view returned user attributes before deploying to production.
- **Automatic Updates**: Integrated update checker directly connected to Engagifii release channels.

---

## Requirements

- **WordPress**: 5.8 or higher
- **PHP**: 7.4 or higher (fully compatible with PHP 8.x)
- Active **Engagifii** application credentials (Client ID and Client Secret)
- HTTPS enabled on your WordPress site (recommended for OAuth callback and secure cookies)

---

## Installation

### Manual Installation via WordPress Admin

1. Download or clone this repository as a `.zip` file.
2. In your WordPress admin dashboard, navigate to **Plugins &rarr; Add New &rarr; Upload Plugin**.
3. Choose the `.zip` file and click **Install Now**.
4. Click **Activate Plugin**.

### Installation via FTP / Git

1. Clone or copy the `engagifii-sso` folder directly into your WordPress plugins directory:
   ```bash
   cd wp-content/plugins/
   git clone https://github.com/thakurgit/engagifii-sso.git
   ```
2. Navigate to **Plugins &rarr; Installed Plugins** in the WordPress admin panel.
3. Locate **Engagifii SSO Login** and click **Activate**.

---

## Configuration

Once activated, access the plugin settings by clicking **Engagifii SSO** in the WordPress admin menu or via **Configure** on the Plugins page.

### 1. Configure SSO Credentials

Go to **Engagifii SSO &rarr; Configure SSO**:

| Field | Description | Default Value |
|-------|-------------|---------------|
| **Client ID** | Unique application Client ID registered with Engagifii Identity Provider. | *(Your Client ID)* |
| **Client Secret** | Confidential secret key for token exchange. | *(Provided by Engagifii)* |
| **Requested Scope** | Space-separated OAuth scopes requested during login. | `openid profile email UsersAPI AccreditationAPI BilltrackingApi CommentApi NotesApi` |
| **Grant Type** | OAuth2 authorization flow. | `authorization_code` |
| **Authorize Endpoint** | Identity Provider authorization URL. | `https://identity.engagifii.com/connect/authorize` |
| **Access Token Endpoint** | Token exchange endpoint. | `https://identity.engagifii.com/connect/token` |
| **User Info Endpoint** | User profile data endpoint. | `https://identity.engagifii.com/connect/userinfo` |
| **IDP Logout URL** | Centralized Single Logout endpoint. | `https://identity.engagifii.com/Account/SignOut` |

> **Note:** Ensure your WordPress site URL (e.g., `https://example.com`) is whitelisted as a valid **Redirect URI** in your Engagifii Identity Provider client settings.

---

### 2. Login & Redirect Settings

Go to **Engagifii SSO &rarr; Login Settings**:

- **Disable Default WP Login Form**: Toggle to hide standard username/password inputs on `wp-login.php`, allowing authentication only through Engagifii SSO.
- **After Login Redirect Page**: Select the landing page users are redirected to upon successful SSO login (leave empty for WordPress default).
- **After Logout Redirect Page**: Select the page users are redirected to after logging out.
- **SSO Button Style**: Choose between preset styles or upload a custom button/logo via the WordPress Media Library.
- **Navigation Menu Integration**: Enable to automatically append the login/logout button to a specific theme menu, and configure the button width in pixels.

---

### 3. Role Mapping

Go to **Engagifii SSO &rarr; Attribute/Role Mapping**:

- **Default WordPress Role**: Select the role assigned to newly provisioned users when they sign in through Engagifii for the first time (default: `Subscriber`).
- *Note:* Existing WordPress users' roles are not overwritten on subsequent logins.

---

### 4. Testing the Configuration

1. In the **Configure SSO** tab, click **Test Configuration**.
2. If your credentials and redirect URIs are configured properly, an authentication prompt will appear.
3. Upon authentication as an administrator, a diagnostic table displaying returned claims (e.g., `email`, `given_name`, `family_name`, `sub`) will be rendered to verify correct data transmission.

---

## Shortcodes

You can display the Engagifii SSO button anywhere in your theme, pages, posts, or widget areas using the shortcode:

```text
[login_with_engagifii]
```

### Behavior:
- **Logged Out**: Renders the "Login with Engagifii" button linking to the SSO authentication URL.
- **Logged In**: Automatically toggles to a clean **Log Out** link pointing to the secure sign-out redirect flow.

---

## Developer Hooks & API

The plugin provides WordPress action hooks to allow developers to trigger custom business logic, sync user metadata, or interface with third-party APIs using the OAuth access token.

### 1. `engagifii_sso_loggedIn`

Triggered on every successful SSO authentication.

```php
add_action('engagifii_sso_loggedIn', function ($user_id, $access_token) {
    // Store or use access token for API requests
    update_user_meta($user_id, '_engagifii_access_token', sanitize_text_field($access_token));
    
    // Example: Log login event
    error_log("User {$user_id} logged in via Engagifii SSO.");
}, 10, 2);
```

### 2. `engagifii_sso_authenticated`

Triggered **only once**, immediately after a new WordPress user account is created.

```php
add_action('engagifii_sso_authenticated', function ($user_id, $access_token) {
    // Perform initial onboarding tasks
    update_user_meta($user_id, '_engagifii_first_sso_login', current_time('mysql'));
}, 10, 2);
```

---

## Directory Structure

```text
engagifii-sso/
├── admin/
│   ├── class-engagifii-sso-admin.php     # Admin menus, settings API, and script registration
│   └── views/
│       ├── admin-display.php             # Admin layout shell and navigation tabs
│       ├── tab-configure-sso.php         # OAuth2 credentials & endpoints tab
│       ├── tab-help.php                  # Help & support documentation view
│       ├── tab-login-settings.php        # Form behavior, button branding, and nav menu settings
│       └── tab-role-mapping.php          # User role provisioning settings
├── assets/
│   ├── css/                              # Admin stylesheets
│   ├── images/                           # Brand assets and button presets
│   └── js/                               # Admin scripts (media uploader, test configuration)
├── includes/
│   ├── class-engagifii-sso-auth.php      # OAuth2 handshake, token exchange, and user creation
│   ├── class-engagifii-sso-updater.php   # Remote plugin update checker
│   ├── class-engagifii-sso.php           # Main orchestrator initializing all hooks
│   └── helper-functions.php              # Utility functions (e.g., unique username generator)
├── public/
│   └── class-engagifii-sso-public.php    # Frontend login button, navigation hooks, and shortcode
├── engagifii-sso.php                     # Primary plugin header file
└── README.md                             # Documentation
```

---

## Troubleshooting

### "SSO settings are not configured" or "SSO settings are incomplete"
- Check that your **Client ID**, **Client Secret**, and **Endpoints** are populated in **Engagifii SSO &rarr; Configure SSO**.

### Redirect loop or redirect URI mismatch
- Verify that your WordPress site URL (`site_url()`) exactly matches the Authorized Redirect URI configured in your Engagifii identity management console (including `https://` and trailing slash formatting).

### Users are created but roles are not assigned as expected
- Ensure your default role is set under **Attribute/Role Mapping**. Note that existing users retain their existing role and will not be altered on re-login.

### Access Token not received
- Ensure your server has outbound cURL / HTTPS access to reach `identity.engagifii.com`. Check the PHP error logs for any `wp_remote_post` failures.

---

## Changelog

See the full release history and version notes in [CHANGELOG.md](CHANGELOG.md).

---

## Support & Resources

- **Official Documentation**: [https://engagifiiweb.com/engagifii_plugins/engagifii_sso/](https://engagifiiweb.com/engagifii_plugins/engagifii_sso/)
- **Engagifii Support**: [Contact Support](https://www.engagifii.com/contact-engagifii-support)
- **Author**: Engagifii / Crescerance ([https://crescerance.com/](https://crescerance.com/))
