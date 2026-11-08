<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
include_once('EditPostLink_LifeCycle.php');

class EditPostLink_Plugin extends EditPostLink_LifeCycle {

		/**
		 * Track post IDs that have already rendered an edit link in the current singular request
		 * to prevent duplicate output between `the_content` and the Block Hooks API.
		 *
		 * @var array<int, bool>
		 */
		private $rendered_post_ids = array();

		/**
		 * See: http://plugin.michael-simpson.com/?page_id=31
		 * @return array of option meta data.
		 */
		public function getOptionMetaData() {
				return array(
						//'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
						'edit-post-link-text' => array( __( 'Link Text', 'edit-post-link' ) ),
						'edit-post-link-bg-color' => array( __('Background color', 'edit-post-link' ) ),
						'edit-post-link-font-color' => array( __('Font color', 'edit-post-link' ) ),
						'edit-post-link-hover-bg-color' => array( __('Hover Background color', 'edit-post-link' ) ),
						'edit-post-link-hover-font-color' => array( __('Hover Font color', 'edit-post-link' ) ),
						'edit-post-link-position' => array( __('Position', 'edit-post-link'), __('Above Content', 'edit-post-link'), __('Below Content', 'edit-post-link') ),
						'edit-post-link-alignment' => array( __( 'Alignment', 'edit-post-link' ), __( 'Left', 'edit-post-link' ), __( 'Center', 'edit-post-link' ), __( 'Right', 'edit-post-link' ) ),
						'edit-post-link-display-scope' => array( __( 'Display On', 'edit-post-link' ), __( 'All Views (Single & Archives)', 'edit-post-link' ), __( 'Single Posts & Pages Only', 'edit-post-link' ) ),
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
				$this->addOption( 'edit-post-link-text', __( 'Edit', 'edit-post-link' ) );
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

			// Block Hooks API for block themes (WordPress 6.4+).
			add_action( 'init', array( $this, 'register_edit_post_link_block' ) );
			add_filter( 'hooked_block_types', array( $this, 'hook_edit_post_link_block' ), 10, 4 );

			// Adding scripts only when necessary.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueueEditPostLinkAdminAssets' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueuePluginStyles' ) );
		}

		public function enqueuePluginStyles() {
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

			$script_path = plugin_dir_path( __FILE__ ) . 'js/iris-script.js';
			$script_version = file_exists( $script_path ) ? (string) filemtime( $script_path ) : false;

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'color-picker-script', plugins_url( '/js/iris-script.js', __FILE__ ), array( 'wp-color-picker' ), $script_version, true );
			wp_localize_script(
				'color-picker-script',
				'editPostLinkConfig',
				array(
					'defaultLinkText' => __( 'Edit', 'edit-post-link' ),
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
					'alignments' => array(
						'left' => __( 'Left', 'edit-post-link' ),
						'center' => __( 'Center', 'edit-post-link' ),
						'right' => __( 'Right', 'edit-post-link' ),
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

		private function shouldDisplayInCurrentView() {
			$display_scope = $this->getOption( 'edit-post-link-display-scope', __( 'All Views (Single & Archives)', 'edit-post-link' ) );
			if ( $display_scope === __( 'Single Posts & Pages Only', 'edit-post-link' ) && ! is_singular() ) {
				return false;
			}
			return true;
		}

		private function showEditPostLink( $content, $is_excerpt ) {
			if ( is_admin() || is_feed() || is_search() ) {
				return $content;
			}

			if ( ! $this->shouldDisplayInCurrentView() ) {
				return $content;
			}

			$post = get_post();
			if ( ! $post || ! is_user_logged_in() || ! current_user_can( 'edit_post', $post->ID ) ) {
				return $content;
			}

			if ( ! $is_excerpt && is_singular() && ! empty( $this->rendered_post_ids[ $post->ID ] ) ) {
				return $content;
			}

			$edit_link = get_edit_post_link( $post->ID );
			if ( ! $edit_link ) {
				return $content;
			}

			if ( ! $is_excerpt && is_singular() ) {
				$this->rendered_post_ids[ $post->ID ] = true;
			}

			// Excerpts should always append the link to keep summaries short and predictable.
			$position = $is_excerpt ? __('Below Content', 'edit-post-link') : $this->getOption( 'edit-post-link-position' );
			$link_html = $this->getEditPostLinkHtml( $edit_link, $post->ID );

			if ( $position === __('Above Content', 'edit-post-link') ) {
				return $link_html . $content;
			}

			return $content . $link_html;
		}

		private function getLinkText() {
			$custom_text = trim( (string) $this->getOption( 'edit-post-link-text', '' ) );
			return '' !== $custom_text ? $custom_text : __( 'Edit', 'edit-post-link' );
		}

		private function getAlignmentClass() {
			$alignment = $this->getOption( 'edit-post-link-alignment', __( 'Left', 'edit-post-link' ) );
			if ( $alignment === __( 'Center', 'edit-post-link' ) ) {
				return 'epl-align-center';
			}
			if ( $alignment === __( 'Right', 'edit-post-link' ) ) {
				return 'epl-align-right';
			}
			return 'epl-align-left';
		}

		private function getEditPostLinkHtml( $edit_link, $post_id = 0 ) {
			$link_type = $this->getOption( 'edit-post-link-type' );
			$target_option = $this->getOption( 'edit-post-link-target', __('New Tab', 'edit-post-link') );
			$target = $target_option === __('Same Tab', 'edit-post-link') ? '_self' : '_blank';
			$rel_attr = $target === '_blank' ? ' rel="noopener noreferrer"' : '';
			$link_text = $this->getLinkText();
			$wrap_class = 'edit-post-link-wrap ' . $this->getAlignmentClass();

			$aria_label_attr = '';
			if ( $post_id > 0 ) {
				$post_title = wp_strip_all_tags( get_the_title( $post_id ) );
				if ( '' !== $post_title ) {
					$aria_label_attr = sprintf( ' aria-label="%s"', esc_attr( sprintf( '%1$s "%2$s"', $link_text, $post_title ) ) );
				}
			}

			if ( $link_type === __('Plain Text', 'edit-post-link') || $link_type === __('Link', 'edit-post-link') ) {
				return sprintf(
					'<p class="%1$s"><a href="%2$s" target="%3$s"%4$s%5$s>%6$s</a></p>',
					esc_attr( $wrap_class ),
					esc_url( $edit_link ),
					esc_attr( $target ),
					$rel_attr,
					$aria_label_attr,
					esc_html( $link_text )
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
				'<p class="%1$s"><a class="%2$s" href="%3$s" target="%4$s"%5$s%6$s>%7$s</a></p>',
				esc_attr( $wrap_class ),
				esc_attr( trim( $class_name ) ),
				esc_url( $edit_link ),
				esc_attr( $target ),
				$rel_attr,
				$aria_label_attr,
				esc_html( $link_text )
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
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'edit-post-link' ) );
		}

		$optionMetaData = $this->getOptionMetaData();
		$settingsGroup = get_class( $this ) . '-settings-group';
		$settings_saved = false;

		if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && null !== $optionMetaData ) {
			check_admin_referer( $settingsGroup . '-options' );
			foreach ( $optionMetaData as $aOptionKey => $aOptionMeta ) {
				if ( isset( $_POST[ $aOptionKey ] ) ) {
					$this->updateOption( $aOptionKey, $this->sanitizeOptionValue( $aOptionKey, wp_unslash( $_POST[ $aOptionKey ] ) ) );
				}
			}
			$settings_saved = true;
		}

		$preview_text = $this->getLinkText();
		$preview_align_class = $this->getAlignmentClass();
		?>
		<div class="wrap edit-post-link-settings-page">
			<style>
				.edit-post-link-settings-page .epl-title { display: flex; align-items: center; gap: 12px; margin: 0 0 14px; }
				.edit-post-link-settings-page .epl-title-icon { width: 32px; height: 32px; flex: 0 0 32px; }
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
				.edit-post-link-settings-page .epl-preview-link-wrap-above { margin: 0 0 10px; position: relative; min-height: 30px; }
				.edit-post-link-settings-page .epl-preview-link-wrap-below { margin: 10px 0 0; position: relative; min-height: 30px; }
				.edit-post-link-settings-page #edit-post-link-live-preview a { text-decoration: none; }
				@media (max-width: 1100px) {
					.edit-post-link-settings-page .epl-layout { grid-template-columns: 1fr; }
				}
			</style>

			<h2 class="epl-title">
				<img class="epl-title-icon" src="<?php echo esc_url( plugins_url( '/assets/icon.svg', __FILE__ ) ); ?>" alt="" aria-hidden="true" />
				<span><?php echo esc_html( $this->getPluginDisplayName() ); ?> <?php esc_html_e( 'Settings', 'edit-post-link' ); ?></span>
			</h2>
			<?php if ( $settings_saved ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><strong><?php esc_html_e( 'Settings saved.', 'edit-post-link' ); ?></strong></p>
				</div>
			<?php endif; ?>
			<form method="post" action="">
				<?php settings_fields( $settingsGroup ); ?>
				<div class="epl-layout">
					<div class="epl-card">
						<h3><?php esc_html_e( 'Appearance', 'edit-post-link' ); ?></h3>
						<?php
						$option_groups = array(
							'behavior' => array(
								'title' => __( 'Behavior', 'edit-post-link' ),
								'keys'  => array(
									'edit-post-link-text',
									'edit-post-link-type',
									'edit-post-link-position',
									'edit-post-link-alignment',
									'edit-post-link-display-scope',
									'edit-post-link-target',
									'edit-post-link-hover-animation',
								),
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
									$savedValue = $this->getOption( $option_key );
									if ( 'edit-post-link-text' === $option_key && ( false === $savedValue || null === $savedValue || '' === $savedValue ) ) {
										$savedValue = __( 'Edit', 'edit-post-link' );
									}
									?>
									<tr valign="top">
										<th scope="row"><p><label for="<?php echo esc_attr( $option_key ); ?>"><?php echo esc_html( $displayText ); ?></label></p></th>
										<td><?php $this->createFormControl( $option_key, $aOptionMeta, $savedValue ); ?></td>
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
								<p id="epl-preview-link-above" class="epl-preview-link-wrap-above edit-post-link-wrap <?php echo esc_attr( $preview_align_class ); ?>">
									<a id="epl-preview-link" class="edit-post-link epl-button epl-anim-none" href="#" onclick="return false;"><?php echo esc_html( $preview_text ); ?></a>
								</p>
								<p class="epl-preview-text"><?php esc_html_e( 'This is sample content to preview the link placement and style.', 'edit-post-link' ); ?></p>
								<p id="epl-preview-link-below" class="epl-preview-link-wrap-below edit-post-link-wrap <?php echo esc_attr( $preview_align_class ); ?>" style="display:none;">
									<a id="epl-preview-link-duplicate" class="edit-post-link epl-button epl-anim-none" href="#" onclick="return false;"><?php echo esc_html( $preview_text ); ?></a>
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

		if ( 'edit-post-link-text' === $option_key ) {
			$trimmed = trim( $value );
			return '' !== $trimmed ? $trimmed : __( 'Edit', 'edit-post-link' );
		}

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
			$bg_color = trim( (string) $this->getOption( 'edit-post-link-bg-color' ) );
			$font_color = trim( (string) $this->getOption( 'edit-post-link-font-color' ) );
			$hover_bg_color = trim( (string) $this->getOption( 'edit-post-link-hover-bg-color' ) );
			$hover_font_color = trim( (string) $this->getOption( 'edit-post-link-hover-font-color' ) );

			$default_rules = '';
			if ( '' !== $bg_color ) {
				$default_rules .= 'background-color: ' . esc_html( $bg_color ) . ' !important;';
			}
			if ( '' !== $font_color ) {
				$default_rules .= 'color: ' . esc_html( $font_color ) . ' !important;';
			}

			$hover_rules = '';
			if ( '' !== $hover_bg_color ) {
				$hover_rules .= 'background-color: ' . esc_html( $hover_bg_color ) . ' !important;';
			}
			if ( '' !== $hover_font_color ) {
				$hover_rules .= 'color: ' . esc_html( $hover_font_color ) . ' !important;';
			}

			if ( '' === $default_rules && '' === $hover_rules ) {
				return;
			}

			$css_output = '';
			if ( '' !== $default_rules ) {
				$css_output .= '.edit-post-link { ' . $default_rules . ' }' . "\n";
			}
			if ( '' !== $hover_rules ) {
				$css_output .= '.edit-post-link.epl-button:hover, .edit-post-link.epl-circle:hover { ' . $hover_rules . ' }' . "\n";
			}

			printf(
				"<style type=\"text/css\" media=\"screen\">\n%s</style>\n",
				$css_output // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Values escaped with esc_html above.
			);
		}
	}

	/**
	 * Register a dynamic block for the edit post link (block themes).
	 */
	public function register_edit_post_link_block() {
		if ( function_exists( 'register_block_type' ) ) {
			register_block_type(
				'edit-post-link/edit-link',
				array(
					'render_callback' => array( $this, 'render_edit_post_link_block' ),
				)
			);
		}
	}

	/**
	 * Render callback for the dynamic edit post link block.
	 */
	public function render_edit_post_link_block( $attributes = array(), $content = '', $block = null ) {
		if ( is_admin() || is_feed() || is_search() ) {
			return '';
		}

		if ( ! $this->shouldDisplayInCurrentView() ) {
			return '';
		}

		$post_id = get_the_ID();
		if ( ! $post_id || ! is_user_logged_in() || ! current_user_can( 'edit_post', $post_id ) ) {
			return '';
		}

		if ( is_singular() && ! empty( $this->rendered_post_ids[ $post_id ] ) ) {
			return '';
		}

		$edit_link = get_edit_post_link( $post_id );
		if ( ! $edit_link ) {
			return '';
		}

		if ( is_singular() ) {
			$this->rendered_post_ids[ $post_id ] = true;
		}

		return $this->getEditPostLinkHtml( $edit_link, $post_id );
	}

	/**
	 * Hook the edit post link block before or after the post content block in block themes.
	 */
	public function hook_edit_post_link_block( $hooked_block_types, $relative_position, $anchor_block_type, $context ) {
		$expected_position = ( $this->getOption( 'edit-post-link-position' ) === __( 'Above Content', 'edit-post-link' ) ) ? 'before' : 'after';

		if (
			$relative_position === $expected_position &&
			$anchor_block_type === 'core/post-content' &&
			$context instanceof WP_Block_Template &&
			property_exists( $context, 'slug' ) &&
			in_array( $context->slug, array( 'single', 'page', 'singular' ), true )
		) {
			$hooked_block_types[] = 'edit-post-link/edit-link';
		}

		return $hooked_block_types;
	}
}
