<?php
abstract class EditPostLink_ShortCodeLoader {

	/**
	 * @param  $shortcodeName mixed either string name of the shortcode
	 * (as it would appear in a post, e.g. [shortcodeName])
	 * or an array of such names in case you want to have more than one name
	 * for the same shortcode
	 * @return void
	 */
	public function register($shortcodeName) {
		$this->registerShortcodeToFunction($shortcodeName, 'handleShortcode');
	}

	/**
	 * @param  $shortcodeName mixed either string name of the shortcode
	 * (as it would appear in a post, e.g. [shortcodeName])
	 * or an array of such names in case you want to have more than one name
	 * for the same shortcode
	 * @param  $functionName string name of public function in this class to call as the
	 * shortcode handler
	 * @return void
	 */
	protected function registerShortcodeToFunction($shortcodeName, $functionName) {
		if (is_array($shortcodeName)) {
			foreach ($shortcodeName as $aName) {
				add_shortcode($aName, array($this, $functionName));
			}
		}
		else {
			add_shortcode($shortcodeName, array($this, $functionName));
		}
	}

	/**
	 * @abstract Override this function and add actual shortcode handling here
	 * @param  $atts shortcode inputs
	 * @return string shortcode content
	 */
	public abstract function handleShortcode($atts);

}
