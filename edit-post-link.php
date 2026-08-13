<?php
 /**
 * Plugin Name: Edit Post Link
 * Plugin URI: https://github.com/rafaelfunchal/edit-post-link
 * Description: A plugin to insert a stylized link to edit posts and pages
 * Author: rafaelfunchal
 * Author URI: http://rafaelfunchal.com/
 * Version: 0.3.0.1
 * License: GPLv2 or later
 * Text Domain: edit-post-link
 * Domain Path: /languages/
 * Requires at least: 6.0
 * Requires PHP: 7.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EDIT_POST_LINK_MIN_PHP_VERSION', '7.0' );

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version
 * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
 * an error message on the Admin page
 */
function EditPostLink_noticePhpVersionWrong() {
	echo '<div class="updated fade">' .
	  __('Error: plugin "Edit Post Link" requires a newer version of PHP to be running.',  'edit-post-link').
			'<br/>' . __('Minimal version of PHP required: ', 'edit-post-link') . '<strong>' . EDIT_POST_LINK_MIN_PHP_VERSION . '</strong>' .
			'<br/>' . __('Your server\'s PHP version: ', 'edit-post-link') . '<strong>' . phpversion() . '</strong>' .
		 '</div>';
}

function EditPostLink_PhpVersionCheck() {
	if (version_compare(phpversion(), EDIT_POST_LINK_MIN_PHP_VERSION) < 0) {
		add_action('admin_notices', 'EditPostLink_noticePhpVersionWrong');
		return false;
	}
	return true;
}

/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 * @return void
 */
function EditPostLink_i18n_init() {
	$pluginDir = dirname(plugin_basename(__FILE__));
	load_plugin_textdomain('edit-post-link', false, $pluginDir . '/languages/');
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

// First initialize i18n
add_action( 'init', 'EditPostLink_i18n_init' );

// Next, run the version check.
// If it is successful, continue with initialization for this plugin
if (EditPostLink_PhpVersionCheck()) {
	// Only load and run the init function if we know PHP version can parse it
	include_once('edit-post-link_init.php');
	EditPostLink_init(__FILE__);
}
