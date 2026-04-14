<?php
include_once('EditPostLink_LifeCycle.php');

class EditPostLink_Plugin extends EditPostLink_LifeCycle {

		/**
		 * See: http://plugin.michael-simpson.com/?page_id=31
		 * @return array of option meta data.
		 */
		public function getOptionMetaData() {
				return array(
						//'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
						'edit-post-link-bg-color' => array( __('Background color', 'edit-post-link' ) ),
						'edit-post-link-border-color' => array( __('Border color', 'edit-post-link' ) ),
						'edit-post-link-font-color' => array( __('Font color', 'edit-post-link' ) ),
						'edit-post-link-position' => array( __('Position', 'edit-post-link'), __('Above Content', 'edit-post-link'), __('Below Content', 'edit-post-link') ),
						'edit-post-link-type' => array( __( 'Link Type', 'edit-post-link'), __('Button', 'edit-post-link'), __('Circle', 'edit-post-link') ),
						'edit-post-link-styles' => array( __( 'Load plugin styles?', 'edit-post-link'), __('Yes', 'edit-post-link'), __('No', 'edit-post-link') )
				);
		}

		protected function initOptions() {
				$options = $this->getOptionMetaData();
				if (!empty($options)) {
						foreach ($options as $key => $arr) {
								if (is_array($arr) && count($arr) > 1 ) {
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
			add_action('admin_menu', array($this, 'addSettingsSubMenuPage'));

			// Add Actions & Filters
			// http://plugin.michael-simpson.com/?page_id=37
			add_filter( 'the_content', array( $this, 'showEditPostLinkInContent' ) );
			add_filter( 'the_excerpt', array( $this, 'showEditPostLinkInExcerpt' ) );
			add_action( 'wp_head', array( $this, 'stylesEditPostLink' ) );

			// Adding scripts only when necessary.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueueEditPostLinkAdminAssets' ) );

			if ( $this->getOption( 'edit-post-link-styles' ) === __('Yes', 'edit-post-link') ) {
				wp_enqueue_style( 'edit-post-link-style', plugins_url( '/css/styles.css', __FILE__ ) );
			}
		}

		public function enqueueEditPostLinkAdminAssets( $hook ) {
			if ( 'settings_page_EditPostLink_PluginSettings' !== $hook ) {
				return;
			}

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'color-picker-script', plugins_url( '/js/iris-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
		}

		public function showEditPostLinkInContent( $content ) {
			return $this->showEditPostLink( $content, false );
		}

		public function showEditPostLinkInExcerpt( $excerpt ) {
			return $this->showEditPostLink( $excerpt, true );
		}

		private function showEditPostLink( $content, $is_excerpt ) {
			if ( is_admin() || is_feed() || is_search() ) {
				return $content;
			}

			$post = get_post();
			if ( ! $post || ! is_user_logged_in() || ! current_user_can( 'edit_post', $post->ID ) ) {
				return $content;
			}

			$edit_link = get_edit_post_link( $post->ID );
			if ( ! $edit_link ) {
				return $content;
			}

			// Excerpts should always append the link to keep summaries short and predictable.
			$position = $is_excerpt ? __('Below Content', 'edit-post-link') : $this->getOption( 'edit-post-link-position' );
			$link_html = $this->getEditPostLinkHtml( $edit_link );

			if ( $position === __('Above Content', 'edit-post-link') ) {
				return $link_html . $content;
			}

			return $content . $link_html;
		}

		private function getEditPostLinkHtml( $edit_link ) {
			$epl_type = $this->getOption( 'edit-post-link-type' ) === __('Circle', 'edit-post-link') ? 'epl-circle' : 'epl-button';

			return sprintf(
				'<p><a class="edit-post-link %1$s" href="%2$s" target="_blank" rel="noopener noreferrer">%3$s</a></p>',
				esc_attr( $epl_type ),
				esc_url( $edit_link ),
				esc_html__( 'Edit', 'edit-post-link' )
			);
		}

	public function stylesEditPostLink() {
		if ( $this->getOption( 'edit-post-link-styles' ) === __('Yes', 'edit-post-link') ) {
			$styles = sprintf( '<style type="text/css" media="screen">
			.edit-post-link { background-color: %s !important; border-color: %s !important; color: %s !important;
			</style>',
				esc_html( $this->getOption( 'edit-post-link-bg-color' ) ),
				esc_html( $this->getOption( 'edit-post-link-border-color' ) ),
				esc_html( $this->getOption( 'edit-post-link-font-color' ) )
			);
			echo $styles;
		}
	}
}
