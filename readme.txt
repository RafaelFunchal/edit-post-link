=== Edit Post Link ===
Contributors: rafaelfunchal, ourvalley
Tags: link, edit, post, button
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.0
Stable tag: 0.3.0.0

A plugin to insert a stylized link to edit posts and pages

== Description ==

A plugin to insert a stylized link to edit posts and pages

New in `0.3.0.0`:
- Link types: `Button`, `Circle`, and `Plain Text`
- Behavior controls: position (`Above`/`Below`) and open target (`New Tab`/`Same Tab`)
- Hover customization: animation presets and hover color options
- Live preview in settings that updates as options change
- Better translations support for option-driven UI behavior
- Security and accessibility hardening (validated settings, focus styles, larger circle hit area)

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

= 0.3.0.0 =
- Add a redesigned settings experience with grouped controls and a live preview panel.
- Add `Open Link In` behavior (`New Tab` or `Same Tab`) and keep position controls in the Behavior group.
- Add hover customization controls (animation and hover colors), with conditional visibility based on selected link type.
- Add translation-safe UI logic for settings interactions through localized config values.
- Improve security with nonce verification and strict server-side option sanitization/validation.
- Improve accessibility with keyboard focus-visible states and a larger compact circle hit area.

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
