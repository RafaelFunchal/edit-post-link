<?php
include_once('EditPostLink_LifeCycle.php');

class EditPostLink_Plugin extends EditPostLink_LifeCycle {

    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
        //  http://plugin.michael-simpson.com/?page_id=31
        return array(
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
            'edit-post-link-bg-color' => array( __('Background color', 'edit-post-link' ) ),
            'edit-post-link-border-color' => array( __('Border color', 'edit-post-link' ) ),
            'edit-post-link-font-color' => array( __('Font color', 'edit-post-link' ) )
        );
    }

//    protected function getOptionValueI18nString($optionValue) {
//        $i18nValue = parent::getOptionValueI18nString($optionValue);
//        return $i18nValue;
//    }

    protected function initOptions() {
        $options = $this->getOptionMetaData();
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr > 1)) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }

    public function getPluginDisplayName() {
        return 'Edit Post Link';
    }

    protected function getMainPluginFileName() {
        return 'edit-post-link.php';
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    protected function installDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
        //            `id` INTEGER NOT NULL");
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }


    /**
     * Perform actions when upgrading from version X to version Y
     * See: http://plugin.michael-simpson.com/?page_id=35
     * @return void
     */
    public function upgrade() {
    }

    public function addActionsAndFilters() {

        // Add options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));

        // Example adding a script & style just for the options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        //        if (strpos($_SERVER['REQUEST_URI'], $this->getSettingsSlug()) !== false) {
        //            wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));
        //            wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        }
		if (strpos( $_SERVER['REQUEST_URI'], $this->getSettingsSlug()) !== false ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'color-picker-script', plugins_url( '/js/iris-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
		}

        // Add Actions & Filters
        // http://plugin.michael-simpson.com/?page_id=37
		// add_filter( 'the_content', array( &$this, 'showEditPostLink' ) );
		add_filter( 'the_content', array( &$this, 'showEditPostLink' ) );
		add_action( 'wp_head', array( &$this, 'stylesEditPostLink' ) );

        // Adding scripts & styles to all pages
        // Examples:
        wp_enqueue_script( 'jquery' );
        wp_enqueue_style( 'edit-post-link-style', plugins_url( '/css/styles.css', __FILE__ ) );
        wp_enqueue_script( 'edit-post-link-scripts', plugins_url( '/js/scripts.js', __FILE__ ) );
    }
	
	public function showEditPostLink( $content ) {
	    $content = sprintf(
	                       '<a class="edit-post-link" href="%s" target="_blank">%s</a>%s',
	                       get_edit_post_link(),
	                       __( 'Edit', 'edit-post-link' ),
	                       $content
	                       );
	    return $content;
	}

	public function stylesEditPostLink() {
		$styles = sprintf( '<style type="text/css" media="screen">
		.edit-post-link { background-color: %s !important; border-color: %s !important; color: %s !important;
		</style>',
			$this->getOption( 'edit-post-link-bg-color' ),
			$this->getOption( 'edit-post-link-border-color' ),
			$this->getOption( 'edit-post-link-font-color' )
		);
		echo $styles;
	}

}
