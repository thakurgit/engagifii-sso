# Changelog

All notable changes to the **Engagifii SSO Login** plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [2.3.1] - 2026-10-21

### Added
- GitHub Actions CI/CD release workflow (`.github/workflows/release.yml`) to automatically build and attach `engagifii-sso.zip` to every release.
- Added comprehensive `README.md`, `CHANGELOG.md`, and `.gitignore`.

### Changed
- Migrated plugin update checker to use official GitHub Releases REST API.
- Enhanced WordPress update modal to display release notes from the last 2 releases with direct link to GitHub releases.
- Registered `upgrader_source_selection` filter to automatically normalize extracted plugin folder name.

---

## [2.3.0] - 2026-09-18

### Added / Changed
- UI enhancement for the SSO settings page with modern tabbed layout.
- Pass `access_token` parameter via the `engagifii_sso_loggedIn` action hook (`$user_id`, `$access_token`).

---

## [2.2.4] - 2026-09-07

### Added
- Added Attribute / Role Mapping settings tab to specify the default user role upon first-time SSO login.
- Added option to automatically attach Login/Logout button to theme navigation menus.
- Added menu selection dropdown to choose which navigation menu receives the SSO button.

### Fixed
- Fixed theme compatibility issues when rendering the SSO button inside navigation menus.

---

## [2.2.3] - 2025-07-08

### Added
- Added `engagifii_sso_loggedIn` action hook for already registered users upon successful authentication.

### Changed
- Delayed priority of logout function execution (`wp_logout` action hook priority set to 20) for cleaner session cleanup.

---

## [2.2.2] - 2025-06-26

### Added
- Added custom redirection settings after login and logout.

### Security / Fixed
- Sanitized email format while provisioning new user accounts (`sanitize_email`).

---

## [2.2.1] - 2025-06-09

### Added
- Stored `access_token` in secure HTTP-only cookie upon successful OAuth token exchange.

### Fixed
- Fixed issue with the "Test Configuration" button in the admin interface.
- Initial release enhancements.

---

## [1.0.0] - 2025-03-31

### Added
- Initial release of Engagifii SSO Login for WordPress.
- OAuth 2.0 / OpenID Connect authentication flow with Engagifii Identity Provider.
- Automatic WordPress user provisioning.
- `[login_with_engagifii]` shortcode support.
- Bug fixes and stability improvements.
