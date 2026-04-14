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
						'edit-post-link-font-color' => array( __('Font color', 'edit-post-link' ) ),
						'edit-post-link-hover-bg-color' => array( __('Hover Background color', 'edit-post-link' ) ),
						'edit-post-link-hover-font-color' => array( __('Hover Font color', 'edit-post-link' ) ),
						'edit-post-link-position' => array( __('Position', 'edit-post-link'), __('Above Content', 'edit-post-link'), __('Below Content', 'edit-post-link') ),
						'edit-post-link-target' => array( __( 'Open Link In', 'edit-post-link' ), __('New Tab', 'edit-post-link'), __('Same Tab', 'edit-post-link') ),
						'edit-post-link-type' => array( __( 'Link Type', 'edit-post-link'), __('Button', 'edit-post-link'), __('Circle', 'edit-post-link'), __('Plain Text', 'edit-post-link') ),
						'edit-post-link-hover-animation' => array( __( 'Hover Animation', 'edit-post-link' ), __('None', 'edit-post-link'), __('Lift', 'edit-post-link'), __('Grow', 'edit-post-link'), __('Pulse', 'edit-post-link'), __('Glow', 'edit-post-link') )
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
			// Remove legacy option after replacing it with the Plain Text link type.
			$this->deleteOption( 'edit-post-link-styles' );
			// Remove legacy border color option.
			$this->deleteOption( 'edit-post-link-border-color' );
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

			if ( $this->shouldLoadPluginStyles() ) {
				$styles_path = plugin_dir_path( __FILE__ ) . 'css/styles.css';
				$styles_version = file_exists( $styles_path ) ? (string) filemtime( $styles_path ) : false;
				wp_enqueue_style( 'edit-post-link-style', plugins_url( '/css/styles.css', __FILE__ ), array(), $styles_version );
			}
		}

		public function enqueueEditPostLinkAdminAssets( $hook ) {
			if ( 'settings_page_EditPostLink_PluginSettings' !== $hook ) {
				return;
			}

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'color-picker-script', plugins_url( '/js/iris-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
			wp_localize_script(
				'color-picker-script',
				'editPostLinkConfig',
				array(
					'linkTypes' => array(
						'button' => __( 'Button', 'edit-post-link' ),
						'circle' => __( 'Circle', 'edit-post-link' ),
						'plainText' => __( 'Plain Text', 'edit-post-link' ),
						'link' => __( 'Link', 'edit-post-link' ),
					),
					'hoverAnimations' => array(
						'none' => __( 'None', 'edit-post-link' ),
						'lift' => __( 'Lift', 'edit-post-link' ),
						'grow' => __( 'Grow', 'edit-post-link' ),
						'pulse' => __( 'Pulse', 'edit-post-link' ),
						'glow' => __( 'Glow', 'edit-post-link' ),
					),
					'positions' => array(
						'above' => __( 'Above Content', 'edit-post-link' ),
						'below' => __( 'Below Content', 'edit-post-link' ),
					),
				)
			);
			$styles_path = plugin_dir_path( __FILE__ ) . 'css/styles.css';
			$styles_version = file_exists( $styles_path ) ? (string) filemtime( $styles_path ) : false;
			wp_enqueue_style( 'edit-post-link-preview-style', plugins_url( '/css/styles.css', __FILE__ ), array(), $styles_version );
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
			$link_type = $this->getOption( 'edit-post-link-type' );
			$target_option = $this->getOption( 'edit-post-link-target', __('New Tab', 'edit-post-link') );
			$target = $target_option === __('Same Tab', 'edit-post-link') ? '_self' : '_blank';
			$rel = $target === '_blank' ? 'noopener noreferrer' : '';

			if ( $link_type === __('Plain Text', 'edit-post-link') || $link_type === __('Link', 'edit-post-link') ) {
				return sprintf(
					'<p><a href="%1$s" target="%2$s" rel="%3$s">%4$s</a></p>',
					esc_url( $edit_link ),
					esc_attr( $target ),
					esc_attr( $rel ),
					esc_html__( 'Edit', 'edit-post-link' )
				);
			}

			$epl_type = $link_type === __('Circle', 'edit-post-link') ? 'epl-circle' : 'epl-button';
			$class_name = 'edit-post-link ' . $epl_type;

			// Hover animation is intentionally supported only for the Button link type.
			if ( $link_type === __('Button', 'edit-post-link') ) {
				$hover_animation = $this->getOption( 'edit-post-link-hover-animation', __('None', 'edit-post-link') );
				$hover_animation_class = $this->getHoverAnimationClass( $hover_animation );
				$class_name .= ' ' . $hover_animation_class;
			}

			return sprintf(
				'<p><a class="%1$s" href="%2$s" target="%3$s" rel="%4$s">%5$s</a></p>',
				esc_attr( trim( $class_name ) ),
				esc_url( $edit_link ),
				esc_attr( $target ),
				esc_attr( $rel ),
				esc_html__( 'Edit', 'edit-post-link' )
			);
		}

		private function getHoverAnimationClass( $hover_animation ) {
			if ( $hover_animation === __('None', 'edit-post-link') ) {
				return 'epl-anim-none';
			}

			if ( $hover_animation === __('Lift', 'edit-post-link') ) {
				return 'epl-anim-lift';
			}

			if ( $hover_animation === __('Grow', 'edit-post-link') ) {
				return 'epl-anim-grow';
			}

			if ( $hover_animation === __('Pulse', 'edit-post-link') ) {
				return 'epl-anim-pulse';
			}

			if ( $hover_animation === __('Glow', 'edit-post-link') ) {
				return 'epl-anim-glow';
			}

			// Default to no animation.
			return 'epl-anim-none';
		}

	private function shouldLoadPluginStyles() {
		$link_type = $this->getOption( 'edit-post-link-type' );

		return $link_type !== __('Plain Text', 'edit-post-link') && $link_type !== __('Link', 'edit-post-link');
	}

	public function settingsPage() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'edit-post-link' ) );
		}

		$optionMetaData = $this->getOptionMetaData();
		$settingsGroup = get_class( $this ) . '-settings-group';

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] && null !== $optionMetaData ) {
			check_admin_referer( $settingsGroup . '-options' );
			foreach ( $optionMetaData as $aOptionKey => $aOptionMeta ) {
				if ( isset( $_POST[ $aOptionKey ] ) ) {
					$this->updateOption( $aOptionKey, $this->sanitizeOptionValue( $aOptionKey, wp_unslash( $_POST[ $aOptionKey ] ) ) );
				}
			}
		}
		?>
		<div class="wrap edit-post-link-settings-page">
			<style>
				.edit-post-link-settings-page .epl-layout { display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 20px; align-items: start; }
				.edit-post-link-settings-page .epl-card { background: #fff; border: 1px solid #dcdcde; border-radius: 8px; box-shadow: 0 1px 1px rgba(0,0,0,.04); padding: 16px; }
				.edit-post-link-settings-page .epl-card h3 { margin-top: 0; margin-bottom: 10px; }
				.edit-post-link-settings-page .epl-group { margin-bottom: 16px; }
				.edit-post-link-settings-page .epl-group:last-child { margin-bottom: 0; }
				.edit-post-link-settings-page .epl-group-title { margin: 0 0 10px; font-size: 13px; text-transform: uppercase; letter-spacing: 0.04em; color: #646970; }
				.edit-post-link-settings-page .form-table { margin-top: 0; }
				.edit-post-link-settings-page .form-table tbody { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px 16px; }
				.edit-post-link-settings-page .form-table tr { display: block; margin: 0; padding: 10px 12px; border: 1px solid #f0f0f1; border-radius: 8px; background: #fcfcfd; }
				.edit-post-link-settings-page .form-table th,
				.edit-post-link-settings-page .form-table td { display: block; width: 100%; margin: 0; padding: 0; }
				.edit-post-link-settings-page .form-table th { margin-bottom: 8px; }
				.edit-post-link-settings-page .form-table th p { margin: 0; font-weight: 600; }
				.edit-post-link-settings-page .form-table td p { margin: 0; }
				.edit-post-link-settings-page .form-table select,
				.edit-post-link-settings-page .form-table input[type="text"] { width: 100%; max-width: 100%; }
				.edit-post-link-settings-page .epl-preview-surface { background: #f6f7f7; border: 1px dashed #c3c4c7; border-radius: 8px; padding: 14px; min-height: 140px; }
				.edit-post-link-settings-page .epl-preview-post { background: #fff; border: 1px solid #dcdcde; border-radius: 6px; padding: 12px; position: relative; min-height: 85px; }
				.edit-post-link-settings-page .epl-preview-text { margin: 0; color: #2c3338; line-height: 1.5; }
				.edit-post-link-settings-page .epl-preview-link-wrap-above { margin-bottom: 10px; position: relative; min-height: 30px; }
				.edit-post-link-settings-page .epl-preview-link-wrap-below { margin-top: 10px; position: relative; min-height: 30px; }
				.edit-post-link-settings-page #edit-post-link-live-preview a { text-decoration: none; }
				@media (max-width: 1100px) {
					.edit-post-link-settings-page .epl-layout { grid-template-columns: 1fr; }
				}
			</style>

			<h2><?php echo esc_html( $this->getPluginDisplayName() ); ?> <?php esc_html_e( 'Settings', 'edit-post-link' ); ?></h2>
			<form method="post" action="">
				<?php settings_fields( $settingsGroup ); ?>
				<div class="epl-layout">
					<div class="epl-card">
						<h3><?php esc_html_e( 'Appearance', 'edit-post-link' ); ?></h3>
						<?php
						$option_groups = array(
							'behavior' => array(
								'title' => __( 'Behavior', 'edit-post-link' ),
								'keys'  => array( 'edit-post-link-position', 'edit-post-link-target', 'edit-post-link-type', 'edit-post-link-hover-animation' ),
							),
							'normal_colors' => array(
								'title' => __( 'Default Colors', 'edit-post-link' ),
								'keys'  => array( 'edit-post-link-bg-color', 'edit-post-link-font-color' ),
							),
							'hover_colors' => array(
								'title' => __( 'Hover Colors', 'edit-post-link' ),
								'keys'  => array( 'edit-post-link-hover-bg-color', 'edit-post-link-hover-font-color' ),
							),
						);

						foreach ( $option_groups as $group_key => $group ) :
							?>
							<div class="epl-group" data-epl-group="<?php echo esc_attr( $group_key ); ?>">
								<h4 class="epl-group-title"><?php echo esc_html( $group['title'] ); ?></h4>
								<table class="form-table"><tbody>
								<?php
								foreach ( $group['keys'] as $option_key ) {
									if ( ! isset( $optionMetaData[ $option_key ] ) ) {
										continue;
									}
									$aOptionMeta = $optionMetaData[ $option_key ];
									$displayText = is_array( $aOptionMeta ) ? $aOptionMeta[0] : $aOptionMeta;
									?>
									<tr valign="top">
										<th scope="row"><p><label for="<?php echo esc_attr( $option_key ); ?>"><?php echo esc_html( $displayText ); ?></label></p></th>
										<td><?php $this->createFormControl( $option_key, $aOptionMeta, $this->getOption( $option_key ) ); ?></td>
									</tr>
									<?php
								}
								?>
								</tbody></table>
							</div>
							<?php
						endforeach;
						?>
					</div>

					<div class="epl-card">
						<h3><?php esc_html_e( 'Live Preview', 'edit-post-link' ); ?></h3>
						<p><?php esc_html_e( 'Preview updates automatically as you change settings.', 'edit-post-link' ); ?></p>
						<div id="edit-post-link-live-preview" class="epl-preview-surface">
							<div class="epl-preview-post">
								<p id="epl-preview-link-above" class="epl-preview-link-wrap-above">
									<a id="epl-preview-link" class="edit-post-link epl-button epl-anim-none" href="#" onclick="return false;"><?php esc_html_e( 'Edit', 'edit-post-link' ); ?></a>
								</p>
								<p class="epl-preview-text"><?php esc_html_e( 'This is sample content to preview the link placement and style.', 'edit-post-link' ); ?></p>
								<p id="epl-preview-link-below" class="epl-preview-link-wrap-below" style="display:none;">
									<a id="epl-preview-link-duplicate" class="edit-post-link epl-button epl-anim-none" href="#" onclick="return false;"><?php esc_html_e( 'Edit', 'edit-post-link' ); ?></a>
								</p>
							</div>
						</div>
					</div>
				</div>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'edit-post-link' ); ?>"/>
				</p>
			</form>
		</div>
		<?php
	}

	private function sanitizeOptionValue( $option_key, $raw_value ) {
		$value = sanitize_text_field( (string) $raw_value );

		$color_option_keys = array(
			'edit-post-link-bg-color',
			'edit-post-link-font-color',
			'edit-post-link-hover-bg-color',
			'edit-post-link-hover-font-color',
		);

		if ( in_array( $option_key, $color_option_keys, true ) ) {
			$color = sanitize_hex_color( $value );
			return $color ? $color : '';
		}

		$options = $this->getOptionMetaData();
		if ( ! isset( $options[ $option_key ] ) || ! is_array( $options[ $option_key ] ) || count( $options[ $option_key ] ) < 2 ) {
			return $value;
		}

		$allowed_values = array_slice( $options[ $option_key ], 1 );
		if ( in_array( $value, $allowed_values, true ) ) {
			return $value;
		}

		return $allowed_values[0];
	}

	public function stylesEditPostLink() {
		if ( $this->shouldLoadPluginStyles() ) {
			$hover_bg_color = trim( (string) $this->getOption( 'edit-post-link-hover-bg-color' ) );
			$hover_font_color = trim( (string) $this->getOption( 'edit-post-link-hover-font-color' ) );
			$hover_rules = '';

			if ( '' !== $hover_bg_color ) {
				$hover_rules .= 'background-color: ' . esc_html( $hover_bg_color ) . ' !important;';
			}

			if ( '' !== $hover_font_color ) {
				$hover_rules .= 'color: ' . esc_html( $hover_font_color ) . ' !important;';
			}

			$styles = sprintf( '<style type="text/css" media="screen">
			.edit-post-link { background-color: %1$s !important; color: %2$s !important; }
			%3$s
			</style>',
				esc_html( $this->getOption( 'edit-post-link-bg-color' ) ),
				esc_html( $this->getOption( 'edit-post-link-font-color' ) ),
				$hover_rules ? '.edit-post-link.epl-button:hover, .edit-post-link.epl-circle:hover { ' . $hover_rules . ' }' : ''
			);
			echo $styles;
		}
	}
}
