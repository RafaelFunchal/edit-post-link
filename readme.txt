=== Edit Post Link ===
Contributors: rafaelfunchal, ourvalley
Tags: link, edit, post, button
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.0
Stable tag: 0.3.0.2

A plugin to insert a stylized link to edit posts and pages

== Description ==

**Edit Post Link** adds a highly customizable and stylized link or button to your posts, pages, and custom post types, allowing logged-in editors and administrators to quickly jump to the edit screen directly from the frontend.

No more hunting for the admin bar or navigating the dashboard. Just click the button on your post and start editing.

### Key Features:
* **Multiple Styles & Types:** Choose between a sleek **Button**, a modern **Circle**, or a clean **Plain Text** link.
* **Custom Layouts:** Position the link **Above** or **Below** your post content, or choose to open the link in a **New Tab** or the **Same Tab**.
* **Hover Customization:** Personalize the hover behavior with color selectors and smooth animations (Lift, Grow, Pulse, Glow, or None).
* **Live Settings Preview:** Preview your button style, colors, and layout in real-time on the settings page before saving.
* **Block Theme & FSE Support:** Fully compatible with classic themes as well as modern block themes (automatically hooks the edit link after the post content using the Block Hooks API on single templates).
* **Secure and Lightweight:** Built with performance and security in mind, utilizing WordPress settings validation, nonces, and lightweight CSS.

== Installation ==
- Uncompress the download package
- Upload folder including all files and sub directories to the /wp-content/plugins/ directory.
- Activate the plugin through the 'Plugins' menu in WordPress
- Select your colors in Settings -> Edit Post Link

== Frequently Asked Questions ==


== Screenshots ==
1. Settings page configured to don't load the plugin styles.
2. The Edit button uses theme's link styles in this case.
3. Settings page configured to load the plugin styles.
4. A button will be displayed using the colors set on the plugin's settings page.


== Changelog ==

= 0.3.0.2 =
- Fix PHP 8.4 deprecation warning in PHP version check.
- Hook translation loading and stylesheet enqueueing to proper WordPress hooks.
- Fix direct file access security issues.
- Clean up unused translation files.
- Declare compatibility and test up to WordPress 7.1.

= 0.3.0.1 =
- Add block theme (FSE) support: output the edit link after post content on single templates via the Block Hooks API (WordPress 6.4+), using the same markup as the classic output.
- Show the plugin icon next to the title on the settings page.
- Refresh the WordPress.org plugin directory assets (icon, banner, screenshots).

= 0.3.0.0 =
- Add a redesigned settings experience with grouped controls and a live preview panel.
- Add `Open Link In` behavior (`New Tab` or `Same Tab`) and keep position controls in the Behavior group.
- Add hover customization controls (animation and hover colors), with conditional visibility based on selected link type.
- Add translation-safe UI logic for settings interactions through localized config values.
- Improve security with nonce verification and strict server-side option sanitization/validation.
- Improve accessibility with keyboard focus-visible states and a larger compact circle hit area.

= 0.2.4.3 =
- Confirmed compatibility with PHP 8.3 and WordPress 6.8.1. Minimum required PHP version is now 7.4.

= 0.2.4.1 =
- Fixing a Fatal Error created when activating the plugin on PHP 8.0.

= 0.2.4.0 =
- Add link type options (button and circle).
- Load jQuery only on the plugin settings page.
- Animate the Circle Link with CSS instead of jQuery.

= 0.2.3.1 =
- Fix an issue with the pt-BR translations.

= 0.2.3 =
- Brings back styles and transition.
- Adds the ability to choose to load styles on the front-end.

= 0.2.2 =
- Removed the default styles to let theme style by default
- Removed javascript transition
- Added option to choose either above or below content to show link

= 0.2.1 =
- Fixed who can see the button.

= 0.2 =
- Fixed jQuery conflict.

= 0.1 =
- Initial Revision.
