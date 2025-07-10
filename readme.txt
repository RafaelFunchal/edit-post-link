=== Edit Post Link ===
Contributors: rafaelfunchal, ourvalley
Tags: link, edit, post, button
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 5.0
Tested up to: 6.8.1
Stable tag: 0.2.4.2

A plugin to insert a stylized link to edit posts and pages

== Description ==

A plugin to insert a stylized link to edit posts and pages

Now works with both classic and block (FSE) themes. In block themes, the edit link is injected after the post content using the Block Hooks API (WordPress 6.4+).

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

= 0.2.4.3 =
- Confirmed compatibility with PHP 8.3 and WordPress 6.8.1. Minimum required PHP version is now 7.4.

= 0.2.4.2 =
- Added compatibility with block themes (Full Site Editing) using the Block Hooks API (WordPress 6.4+). The edit link now appears after post content in both classic and block themes.

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
