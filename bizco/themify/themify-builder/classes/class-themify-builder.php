<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Themify_Builder' ) ) {

	/**
	 * Main Themify Builder class
	 * 
	 * @package default
	 */
	class Themify_Builder {

		/**
		 * @var string
		 */
		private $meta_key;

		/**
		 * @var string
		 */
		private $meta_key_transient;

		/**
		 * @var array
		 */
		var $builder_settings = array();

		/**
		 * @var array
		 */
		var $template_vars = array();

		/**
		 * @var array
		 */
		var $module_settings = array();

		/**
		 * @var array
		 */
		var $registered_post_types = array();

		/**
		 * Define builder grid active or not
		 * @var bool
		 */
		var $frontedit_active = false;

		/**
		 * Define load form
		 * @var string
		 */
		var $load_form = 'module';

		/**
		 * Themify Builder Constructor
		 */
		function __construct() {}

		/**
		 * Class Init
		 */
		function init() {
			// Include required files
			$this->includes();

			// Init
			Themify_Builder_Model::load_general_metabox(); // setup metabox fields
			$this->load_modules(); // load builder modules

			// Builder write panel
			add_filter( 'themify_do_metaboxes', array( &$this, 'builder_write_panels' ), 11 );

			// Filtered post types
			add_filter( 'themify_post_types', array( &$this, 'extend_post_types' ) );
			add_filter( 'themify_builder_module_content', array( &$this, 'the_module_content' ) );

			// Actions
			add_action( 'init', array( &$this, 'setup' ), 10 );
			add_action( 'themify_builder_metabox', array( &$this, 'add_builder_metabox' ), 10 );
			add_action( 'admin_enqueue_scripts', array( &$this, 'load_admin_js_css' ), 10 );
			add_action( 'wp_enqueue_scripts', array( &$this, 'load_front_js_css' ), 10 );
			add_action( 'wp_footer', array( &$this, 'load_builder_google_fonts' ), 10 );

			// Ajax Actions
			add_action( 'wp_ajax_tfb_add_element', array( &$this, 'add_element_ajaxify' ), 10 );
			add_action( 'wp_ajax_tfb_lightbox_options', array( &$this, 'module_lightbox_options_ajaxify' ), 10 );
			add_action( 'wp_ajax_tfb_add_wp_editor', array( &$this, 'add_wp_editor_ajaxify' ), 10 );
			add_action( 'wp_ajax_builder_import', array( &$this, 'builder_import_ajaxify' ), 10 );
			add_action( 'wp_ajax_builder_import_submit', array( &$this, 'builder_import_submit_ajaxify' ), 10 );
			add_action( 'wp_ajax_row_lightbox_options', array( &$this, 'row_lightbox_options_ajaxify' ), 10 );
			add_action( 'wp_ajax_update_row_style', array( &$this, 'update_row_style' ), 10 );


			// Builder Save Data
			add_action( 'wp_ajax_tfb_save_data', array( &$this, 'save_data_builder' ), 10 );

			// Duplicate page / post action
			add_action( 'wp_ajax_tfb_duplicate_page', array( &$this, 'duplicate_page_ajaxify' ), 10 );

			// Hook to frontend
			add_action( 'wp_head', array( &$this, 'load_inline_js_script' ), 10 );
			add_filter( 'the_content', array( &$this, 'builder_show_on_front' ), 11 );
			add_action( 'wp_ajax_tfb_toggle_frontend', array( &$this, 'load_toggle_frontend_ajaxify' ), 10 );
			add_action( 'wp_ajax_tfb_load_module_partial', array( &$this, 'load_module_partial_ajaxify' ), 10 );
			add_filter( 'body_class', array( &$this, 'body_class'), 10 );

			// Shortcode
			add_shortcode( 'themify_builder_render_content', array( &$this, 'do_shortcode_builder_render_content' ) );
			
			// Plupload Action
			add_action( 'admin_enqueue_scripts', array( &$this, 'plupload_admin_head' ), 10 );
			add_action( 'wp_head', array( &$this, 'plupload_front_head' ), 10 );

			add_action( 'wp_ajax_themify_builder_plupload_action', array( &$this, 'builder_plupload' ), 10 );

			add_action( 'admin_bar_menu', array( &$this, 'builder_admin_bar_menu' ), 100 );

			// Frontend editor
			add_action( 'themify_builder_edit_module_panel', array( &$this, 'module_edit_panel_front'), 10, 2 );

			// Switch to frontend
			add_action( 'save_post', array( &$this, 'switch_frontend' ), 999, 1 );

			// Flush permalink
			add_action( 'after_switch_theme', array( &$this, 'rewrite_flush' ), 10 );

			// Reset Builder Filter
			add_action( 'themify_builder_before_template_content_render', array( &$this, 'do_reset_before_template_content_render' ) );
			add_action( 'themify_builder_after_template_content_render', array( &$this, 'do_reset_after_template_content_render' ) );

			// WordPress Search
			add_filter( 'posts_where', array( &$this, 'do_search' ) );

			// Row Styling
			add_action( 'themify_builder_row_start', array( &$this, 'render_row_styling' ), 10, 2 );
		}

		/**
		 * Init function
		 */
		function setup() {
			// Define builder path
			$this->builder_settings = array(
				'template_url' => 'themify-builder/',
				'builder_path' => THEMIFY_BUILDER_TEMPLATES_DIR .'/'
			);

			// Define meta key name
			$this->meta_key = apply_filters( 'themify_builder_meta_key', '_themify_builder_settings' );
			$this->meta_key_transient = apply_filters( 'themify_builder_meta_key_transient', 'themify_builder_settings_transient' );

			// Check whether grid edit active
			$this->is_front_builder_activate();

			// Template variables
			require_once( THEMIFY_BUILDER_INCLUDES_DIR . '/themify-builder-template-vars.php' );
		}

		/**
		 * Include required files
		 */
		function includes() {
			include( THEMIFY_BUILDER_CLASSES_DIR . '/class-builder-duplicate-page.php' ); // Class duplicate page
		}

		/**
		 * Builder write panels
		 */
		function builder_write_panels( $meta_boxes ) {
			// Page builder Options
			$page_builder_options = apply_filters( 'themify_builder_write_panels_options', array(
				// Feature Image
				array(
						'name' 		=> 'page_builder',	
						'title' 	=> __( 'Themify Builder', 'themify' ),
						'description' => '',
						'type' 		=> 'page_builder',			
						'meta'		=> array()			
					),
				array(
					'name' 		=> 'builder_switch_frontend',	
					'title' 	=> false, 
					'type' 		=> 'textbox',
					'value'		=> 0,			
					'meta'		=> array( 'size' => 'small' )
				)
			) );
			
			$types = themify_post_types();
			$all_meta_boxes = array();
			foreach ( $types as $type ) {
				$all_meta_boxes[] = apply_filters( 'themify_builder_write_panels_meta_boxes', array(
					'name'		=> __( 'Themify Builder', 'themify' ),
					'id' 		=> 'page-builder',
					'options'	=> $page_builder_options,
					'pages'    	=> $type
				) );
			}

			return array_merge( $meta_boxes, $all_meta_boxes);
		}

		/**
		 * Load builder modules
		 */
		function load_modules() {
			// load modules
			$active_modules = $this->get_modules( 'active' );

			foreach ( $active_modules as $m ) {
				$path = $m['dirname'] . '/' . $m['basename'];
				require_once( $path );
			}
		}

		/**
		 * Get module php files data
		 * @param string $select
		 * @return array
		 */
		function get_modules( $select = 'all' ) {
			$files = array();
			$file_names = apply_filters( 'themify_builder_modules_list', array(
					'accordion',
					'box',
					'callout',
					'divider',
					'gallery',
					'highlight',
					'image',
					'map',
					'menu',
					'portfolio',
					'post',
					'slider',
					'tab',
					'testimonial',
					'text',
					'video',
					'widget',
					'widgetized'
				)
			);
			foreach( $file_names as $file_name ) {
				$tmp = THEMIFY_BUILDER_MODULES_DIR . '/module-'.$file_name.'.php';
				
				if( file_exists( $tmp ) )
					$files[ $file_name ] = $tmp;
			}
			$modules = array();
			if ( count( $files ) > 0 ) {
				foreach ( $files as $key => $value ) {
					$path_info = pathinfo( $value );
					$name = explode( '-', $path_info['filename'] );
					$name = $name[1];
					$modules[ $name ] = array(
						'name' => $name,
						'dirname' => $path_info['dirname'],
						'extension' => $path_info['extension'],
						'basename' => $path_info['basename'],
					);
				}
			}

			if ( 'active' == $select ) {
				$pre = 'setting-page_builder_';
				$data = themify_get_data();
				if ( count( $modules ) > 0 ) {
					foreach ( $modules as $key => $m ) {
						$exclude = $pre . 'exc_' . $m['name'];
						if( isset( $data[ $exclude ] ) )
							unset( $modules[ $m['name'] ] );
					}
				}
			}

			return $modules;
		}

		/**
		 * Check if builder frontend edit being invoked
		 */
		function is_front_builder_activate() {
			if( isset( $_POST['builder_grid_activate'] ) && $_POST['builder_grid_activate'] == 1 )
				$this->frontedit_active = true;
		}

		/**
		 * Add builder metabox
		 */
		function add_builder_metabox() {
			global $post, $pagenow;

			$builder_data = get_post_meta( $post->ID, $this->meta_key, true );
			$builder_data = stripslashes_deep( maybe_unserialize( $builder_data ) );

			if ( empty( $builder_data ) ) {
				$builder_data = array();
			}

			include THEMIFY_BUILDER_INCLUDES_DIR . '/themify-builder-meta.php';
		}

		/**
		 * Load admin js and css
		 * @param $hook
		 */
		function load_admin_js_css( $hook ) {
			global $pagenow, $current_screen;

			if ( in_array( $hook, array( 'post-new.php', 'post.php' ) ) && in_array( get_post_type(), themify_post_types() ) ) {

				wp_enqueue_style( 'themify-builder-main', THEMIFY_BUILDER_URI . '/css/themify-builder-main.css', array(), THEMIFY_VERSION );
				wp_enqueue_style( 'themify-builder-admin-ui', THEMIFY_BUILDER_URI . '/css/themify-builder-admin-ui.css', array(), THEMIFY_VERSION );
				if ( is_rtl() ) {
					wp_enqueue_style( 'themify-builder-admin-ui-rtl', THEMIFY_BUILDER_URI . '/css/themify-builder-admin-ui-rtl.css', array('themify-builder-admin-ui'), THEMIFY_VERSION );
				}
				
				// Enqueue builder admin scripts
				$enqueue_scripts = array( 'jquery-ui-accordion', 'jquery-ui-droppable', 'jquery-ui-sortable', 'jquery-ui-resizable', 'themify-builder-admin-ui-js' );

				foreach ( $enqueue_scripts as $script ) {
					switch ( $script ) {
						case 'themify-builder-admin-ui-js':
							wp_register_script( 'themify-builder-admin-ui-js', THEMIFY_BUILDER_URI . "/js/themify.builder.admin.ui.js", array('jquery'), THEMIFY_VERSION, true );
							wp_enqueue_script( 'themify-builder-admin-ui-js' );

							wp_localize_script( 'themify-builder-admin-ui-js', 'themifyBuilder', apply_filters( 'themify_builder_ajax_admin_vars', array(
								'ajaxurl' => admin_url( 'admin-ajax.php' ),
								'isTouch' => themify_is_touch()? 'true': 'false',
								'tfb_load_nonce' => wp_create_nonce( 'tfb_load_nonce' ),
								'tfb_url' => THEMIFY_BUILDER_URI,
								'dropPlaceHolder' => __( 'drop module here', 'themify' ),
								'newRowTemplate' => $this->template_vars['rows']['content'],
								'draggerTitleMiddle' => __( 'Drag left/right to change columns', 'themify' ),
								'draggerTitleLast' => __( 'Drag left to add columns', 'themify' ),
								'confirm_on_duplicate_page' => __('Save the Builder before duplicating this page?', 'themify'),
								'textRowStyling' => __('Row Styling', 'themify')
							)) );
						break;
						
						default:
							wp_enqueue_script( $script );
						break;
					}
				}
			}
		}

		/**
		 * Load inline js script
		 * Frontend editor
		 */
		function load_inline_js_script() {
			global $post;
			if ( Themify_Builder_Model::is_frontend_editor_page() ) {
			?>
			<script type="text/javascript">
			var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>',
					isRtl = <?php echo (int) is_rtl(); ?>;
			</script>
			<?php
			}
		}

		/**
		 * Load frontend js and css
		 */
		function load_front_js_css() {
			global $post;

			wp_enqueue_style( 'themify-builder-style', THEMIFY_BUILDER_URI . '/css/themify-builder-style.css', array(), THEMIFY_VERSION );

			// load only when editing and login
			if ( Themify_Builder_Model::is_frontend_editor_page() ) {
				wp_enqueue_style( 'themify-builder-main', THEMIFY_BUILDER_URI . '/css/themify-builder-main.css', array(), THEMIFY_VERSION );
				wp_enqueue_style( 'themify-builder-admin-ui', THEMIFY_BUILDER_URI . '/css/themify-builder-admin-ui.css', array(), THEMIFY_VERSION );
				wp_enqueue_style( 'colorpicker', THEMIFY_URI . '/css/jquery.minicolors.css' ); // from themify framework
			}
			
			// lib scripts
			if ( ! wp_script_is( 'themify-carousel-js' ) ) {
				wp_enqueue_script( 'themify-carousel-js', THEMIFY_URI . '/js/carousel.js', array('jquery') ); // grab from themify framework
			}

			// module scripts
			wp_register_script( 'themify-builder-module-plugins-js', THEMIFY_BUILDER_URI . "/js/themify.builder.module.plugins.js", array( 'jquery' ), THEMIFY_VERSION, true );
			wp_enqueue_script( 'themify-builder-module-plugins-js' );

			wp_register_script( 'themify-builder-script-js', THEMIFY_BUILDER_URI . "/js/themify.builder.script.js", array( 'jquery' ), THEMIFY_VERSION, true );
			wp_enqueue_script( 'themify-builder-script-js' );
			wp_localize_script( 'themify-builder-script-js', 'tbLocalScript', array( 
				'isTouch' => themify_is_touch() ? true : false,
				'animationInviewSelectors' => apply_filters( 'themify_builder_animation_inview_selectors', 
					array(
						'.fly-in > .post', '.fly-in .row_inner > .tb-column',
						'.fade-in > .post', '.fade-in .row_inner > .tb-column',
						'.slide-up > .post', '.slide-up .row_inner > .tb-column',
						'.col4-1.fly-in', '.col4-2.fly-in, .col4-3.fly-in',
						'.col3-1.fly-in', '.col3-2.fly-in', '.col2-1.fly-in', '.col-full.fly-in',
						'.col4-1.fade-in', '.col4-2.fade-in', '.col4-3.fade-in',
						'.col3-1.fade-in', '.col3-2.fade-in', '.col2-1.fade-in', '.col-full.fade-in',
						'.col4-1.slide-up', '.col4-2.slide-up', '.col4-3.slide-up',
						'.col3-1.slide-up', '.col3-2.slide-up', '.col2-1.slide-up', '.col-full.slide-up'
					)
				)
			) );
			
			if ( Themify_Builder_Model::is_frontend_editor_page() ) {

				// load module panel frontend
				add_action( 'wp_footer', array( &$this, 'builder_module_panel_frontedit' ), 10 );
				
				if( function_exists( 'wp_enqueue_media' ) ) {
					wp_enqueue_media();
				}
				$enqueue_scripts = array(
					'jquery-ui-core',
					'jquery-ui-accordion', 
					'jquery-ui-droppable', 
					'jquery-ui-sortable', 
					'jquery-ui-resizable',
					'media-upload',
					'jquery-ui-dialog',
					'wpdialogs',
					'wpdialogs-popup',
					'wplink',
					'editor',
					'quicktags',
					'admin-widgets',
					'colorpicker-js',
					'themify-builder-google-webfont',
					'themify-builder-front-ui-js'
				);

				// is mobile version
				if( $this->isMobile() ) {
					wp_register_script( 'themify-builder-mobile-ui-js', THEMIFY_BUILDER_URI . "/js/jquery.ui.touch-punch.js", array( 'jquery' ), THEMIFY_VERSION, true );
					wp_enqueue_script( 'jquery-ui-mouse' );
					wp_enqueue_script( 'themify-builder-mobile-ui-js' );
				}

				foreach ( $enqueue_scripts as $script ) {
					switch ( $script ) {
						case 'admin-widgets':
							wp_enqueue_script( $script, admin_url( '/js/widgets.min.js' ) ,array( 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable' ) );
						break;

						case 'colorpicker-js':
							wp_enqueue_script( $script, THEMIFY_URI . '/js/jquery.minicolors.js', array('jquery') ); // grab from themify framework
						break;

						case 'themify-builder-google-webfont':
							wp_enqueue_script( $script, themify_https_esc( 'http://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js' ) );
						break;

						case 'themify-builder-front-ui-js':
							// front ui js
							wp_register_script( $script, THEMIFY_BUILDER_URI . "/js/themify.builder.front.ui.js", array('jquery'), THEMIFY_VERSION, true );
							wp_enqueue_script( $script );

							wp_localize_script( $script, 'themifyBuilder', apply_filters( 'themify_builder_ajax_front_vars', array(
								'ajaxurl' => admin_url( 'admin-ajax.php' ),
								'isTouch' => themify_is_touch()? 'true': 'false',
								'tfb_load_nonce' => wp_create_nonce( 'tfb_load_nonce' ),
								'tfb_url' => THEMIFY_BUILDER_URI,
								'post_ID' => $post->ID,
								'dropPlaceHolder' => __('drop module here', 'themify'),
								'newRowTemplate' => $this->template_vars['rows']['content'],
								'draggerTitleMiddle' => __('Drag left/right to change columns','themify'),
								'draggerTitleLast' => __('Drag left to add columns','themify'),
								'moduleDeleteConfirm' => __('Press OK to remove this module','themify'),
								'toggleOn' => __('Turn On Builder', 'themify'),
								'toggleOff' => __('Turn Off Builder', 'themify'),
								'confirm_on_turn_off' => __('Do you want to save the changes made to this page?', 'themify'),
								'confirm_on_duplicate_page' => __('Save the Builder before duplicating this page?', 'themify'),
								'confirm_on_unload' => __('You have unsaved data.', 'themify'),
								'textImportBuilder' => __('Import From', 'themify'),
								'textRowStyling' => __('Row Styling', 'themify'),
								'importFileConfirm' => __( 'This import will override all current Builder data. Press OK to continue', 'themify')
							)) );
						break;
						
						default:
							wp_enqueue_script( $script );
						break;
					}	
				}

			}
		}

		/**
		 * Load Google Fonts Style
		 */
		function load_builder_google_fonts() {
			global $themify;
			if ( ! isset( $themify->builder_google_fonts ) || '' == $themify->builder_google_fonts ) return;
			$themify->builder_google_fonts = substr( $themify->builder_google_fonts, 0, -1 );
			wp_enqueue_style( 'builder-google-fonts', themify_https_esc( 'http://fonts.googleapis.com/css' ). '?family='.$themify->builder_google_fonts );
		}

		/**
		 * Add element via ajax
		 * Drag / drop / add + button
		 */
		function add_element_ajaxify() {
			check_ajax_referer( 'tfb_load_nonce', 'tfb_load_nonce' );

			$template_name = $_POST['tfb_template_name'];
			
			if ( 'module' == $template_name ) {
				$module_name = $_POST['tfb_module_name'];
				echo stripslashes( $this->template_vars[ $template_name ][ $module_name ]['content'] );
			} elseif( 'module_front' == $template_name ) {
				$mod = array( 'mod_name' => $_POST['tfb_module_name'] );
				$this->get_template_module( $mod );
			} else{
				echo stripslashes( $this->template_vars[ $template_name ]['content'] );
			}
			
			die();
		}

		/**
		 * Module settings modal lightbox
		 */
		function module_lightbox_options_ajaxify() {
			check_ajax_referer( 'tfb_load_nonce', 'tfb_load_nonce' );

			$module_name = $_POST['tfb_module_name'];
			$this->load_form = 'module';
			$module = Themify_Builder_Model::$modules[ $module_name ];

			require_once( THEMIFY_BUILDER_INCLUDES_DIR . '/themify-builder-options.php' );
			
			die();
		}

		/**
		 * Row Styling settings
		 */
		function row_lightbox_options_ajaxify() {
			check_ajax_referer( 'tfb_load_nonce', 'nonce' );
			$this->load_form = 'row';

			require_once( THEMIFY_BUILDER_INCLUDES_DIR . '/themify-builder-options.php' );
			die();
		}

		/**
		 * Duplicate page
		 */
		function duplicate_page_ajaxify() {
			global $themifyBuilderDuplicate;
			check_ajax_referer( 'tfb_load_nonce', 'tfb_load_nonce' );

			$post_id = (int) $_POST['tfb_post_id'];
			$post = get_post( $post_id );
			$themifyBuilderDuplicate->edit_link = $_POST['tfb_is_admin'];
			$themifyBuilderDuplicate->duplicate( $post );
			$response['status'] = 'success';
			$response['new_url'] = $themifyBuilderDuplicate->new_url;
			echo json_encode( $response );
			die();
		}

		/**
		 * Add wp editor element
		 */
		function add_wp_editor_ajaxify() {
			check_ajax_referer( 'tfb_load_nonce', 'tfb_load_nonce' );

			$txt_id = $_POST['txt_id'];
			$class = $_POST['txt_class'];
			$txt_name = $_POST['txt_name'];
			$txt_val = stripslashes_deep( $_POST['txt_val'] );
			wp_editor( $txt_val, $txt_id, array('textarea_name' => $txt_name, 'editor_class' => $class, 'textarea_rows' => 20) );
			
			die();
		}

		/**
		 * Load Editable builder grid
		 */
		function load_toggle_frontend_ajaxify() {
			check_ajax_referer( 'tfb_load_nonce', 'tfb_load_nonce' );

			$response = array();
			$post_ids = $_POST['tfb_post_ids'];
			global $post;
			
			foreach( $post_ids as $k => $id ) {
				$sanitize_id = (int)$id;
				$post = get_post( $sanitize_id );
				setup_postdata( $post );
				
				$builder_data = get_post_meta( $post->ID, $this->meta_key, true );
				$builder_data = stripslashes_deep( maybe_unserialize( $builder_data ) );

				if ( ! is_array( $builder_data ) ) {
					$builder_data = array();
				}

				$response[ $k ]['builder_id'] = $post->ID;
				$response[ $k ]['markup'] = $this->retrieve_template( 'builder-output.php', array( 'builder_output' => $builder_data, 'builder_id' => $post->ID ), '', '', false );
			} wp_reset_postdata();

			echo json_encode( $response );

			die();
		}

		/**
		 * Load module partial when update live content
		 */
		function load_module_partial_ajaxify() {
			check_ajax_referer( 'tfb_load_nonce', 'tfb_load_nonce' );
			global $post, $themify;
			
			$post_id = (int) $_POST['tfb_post_id'];
			$w_class = $_POST['tfb_w_class'];
			$selector = $_POST['tfb_mod_selector'];
			$mod = array();
			$identifier = array();
			$response = array();

			$post = get_post( $post_id );
			setup_postdata( $post );

			$transient = $this->meta_key_transient . '_' . $post_id;
			$builder_data = get_transient( $transient );

			if ( $builder_data !== false ) {
				$builder_data = stripslashes_deep( maybe_unserialize( $builder_data ) );
				$mod = $builder_data[ $selector['row'] ]['cols'][ $selector['col'] ]['modules'][ $selector['mod'] ];
				$identifier = array( $selector['row'], $selector['col'], $selector['mod'] );
			}

			$response['html'] = $this->get_template_module( $mod, $post_id, false, true, $w_class, $identifier );
			$response['gfonts'] = $this->get_custom_google_fonts();

			wp_reset_postdata();

			echo json_encode( $response );

			die();
		}

		/**
		 * Save builder main data
		 */
		function save_data_builder() {
			check_ajax_referer( 'tfb_load_nonce', 'tfb_load_nonce' );

			$post_data = array();
			if( count( $_POST['post_data'] ) > 0 ) {
				$post_data = $_POST['post_data'];
			}
			$saveto = $_POST['tfb_saveto'];
			$tfb_post_id = (int) $_POST['tfb_post_id'];
			
			if ( 'main' == $saveto ) {
				update_post_meta( $tfb_post_id, $this->meta_key, $post_data );
				do_action( 'themify_builder_save_data', $tfb_post_id, $this->meta_key, $post_data ); // hook save data
			} else {
				$transient = $this->meta_key_transient . '_' . $tfb_post_id;
				set_transient( $transient, $post_data, 60*60 );
			}
			
			die();
		}

		/**
		 * Hook to content filter to show builder output
		 * @param $content
		 * @return string
		 */
		function builder_show_on_front( $content ) {
			global $post, $wp_query;
			if ( ( is_post_type_archive() && ! is_post_type_archive( 'product' ) ) || post_password_required() || isset( $wp_query->query_vars['product_cat'] ) || is_tax( 'product_tag' ) ) return $content;

			if ( is_singular( 'product' ) && 'product' == get_post_type() ) return $content; // dont show builder on product single description

			if ( is_post_type_archive( 'product' ) && get_query_var( 'paged' ) == 0 && $this->builder_is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				$post = get_post( woocommerce_get_page_id( 'shop' ) );
			}
			
			$builder_data = get_post_meta( $post->ID, $this->meta_key, true );
			$builder_data = stripslashes_deep( maybe_unserialize( $builder_data ) );

			if ( ! is_array( $builder_data ) || strpos( $content, '#more-' ) ) {
				$builder_data = array();
			}

			$content .= $this->retrieve_template( 'builder-output.php', array( 'builder_output' => $builder_data, 'builder_id' => $post->ID ), '', '', false );
			return $content;
		}

		/**
		 * Display module panel on frontend edit
		 */
		function builder_module_panel_frontedit() {
			include_once THEMIFY_BUILDER_INCLUDES_DIR . '/themify-builder-module-panel.php';
		}

		/**
		 * Get initialization parameters for plupload. Filtered through themify_builder_plupload_init_vars.
		 * @return mixed|void
		 * @since 1.4.2
		 */
		function get_builder_plupload_init() {
			return apply_filters('themify_builder_plupload_init_vars', array(
				'runtimes'				=> 'html5,flash,silverlight,html4',
				'browse_button'			=> 'themify-builder-plupload-browse-button', // adjusted by uploader
				'container' 			=> 'themify-builder-plupload-upload-ui', // adjusted by uploader
				'drop_element' 			=> 'drag-drop-area', // adjusted by uploader
				'file_data_name' 		=> 'async-upload', // adjusted by uploader
				'multiple_queues' 		=> true,
				'max_file_size' 		=> wp_max_upload_size() . 'b',
				'url' 					=> admin_url('admin-ajax.php'),
				'flash_swf_url' 		=> includes_url('js/plupload/plupload.flash.swf'),
				'silverlight_xap_url' 	=> includes_url('js/plupload/plupload.silverlight.xap'),
				'filters' 				=> array( array(
					'title' => __('Allowed Files', 'themify'),
					'extensions' => 'jpg,jpeg,gif,png,zip,txt'
				)),
				'multipart' 			=> true,
				'urlstream_upload' 		=> true,
				'multi_selection' 		=> false, // added by uploader
				 // additional post data to send to our ajax hook
				'multipart_params' 		=> array(
					'_ajax_nonce' 		=> '', // added by uploader
					'action' 			=> 'themify_builder_plupload_action', // the ajax action name
					'imgid' 			=> 0 // added by uploader
				)
			));
		}

		/**
		 * Inject plupload initialization variables in Javascript
		 * @since 1.4.2
		 */
		function plupload_front_head() {
			$plupload_init = $this->get_builder_plupload_init();
			wp_localize_script( 'themify-builder-front-ui-js', 'themify_builder_plupload_init', $plupload_init );
		}

		/**
		 * Plupload initialization parameters
		 * @since 1.4.2
		 */
		function plupload_admin_head() {
			$plupload_init = $this->get_builder_plupload_init();
			wp_localize_script( 'themify-builder-admin-ui-js', 'themify_builder_plupload_init', $plupload_init );
		}

		/**
		 * Plupload ajax action
		 */
		function builder_plupload() {
			// check ajax nonce
			$imgid = $_POST['imgid'];
			check_ajax_referer( $imgid . 'themify-builder-plupload' );
			
			/** If post ID is set, uploaded image will be attached to it. @var String */
			$postid = $_POST['topost'];

			/** Handle file upload storing file|url|type. @var Array */
			$file = wp_handle_upload( $_FILES[$imgid . 'async-upload'], array('test_form' => true, 'action' => 'themify_builder_plupload_action') );

			//let's see if it's an image, a zip file or something else
			$ext = explode( '/', $file['type'] );

			// Import routines
			if( 'zip' == $ext[1] || 'rar' == $ext[1] || 'plain' == $ext[1] ){
				
				$url = wp_nonce_url( 'admin.php?page=themify' );
				$upload_dir = wp_upload_dir();
				
				if (false === ( $creds = request_filesystem_credentials( $url ) ) ) {
					return true;
				}
				if ( ! WP_Filesystem( $creds ) ) {
					request_filesystem_credentials( $url, '', true );
					return true;
				}
				
				global $wp_filesystem;
				
				if( 'zip' == $ext[1] || 'rar' == $ext[1] ) {
					$destination = wp_upload_dir();
					$destination_path = $destination['path'];

					unzip_file( $file['file'], $destination_path );
					if( $wp_filesystem->exists( $destination_path . '/builder_data_export.txt' ) ){
						$data = $wp_filesystem->get_contents( $destination_path . '/builder_data_export.txt' );
						
						// Set data here
						update_post_meta( $postid, $this->meta_key, maybe_unserialize( $data ) );

						$wp_filesystem->delete( $destination_path . '/builder_data_export.txt');
						$wp_filesystem->delete( $file['file'] );
					} else {
						_e('Data could not be loaded', 'themify');
					}
				} else {
					if( $wp_filesystem->exists( $file['file'] ) ){
						$data = $wp_filesystem->get_contents( $file['file'] );
						
						// set data here
						update_post_meta( $postid, $this->meta_key, maybe_unserialize( $data ) );

						$wp_filesystem->delete($file['file']);
					} else {
						_e('Data could not be loaded', 'themify');
					}
				}
				
			} else {
				// Insert into Media Library
				// Set up options array to add this file as an attachment
				$attachment = array(
					'post_mime_type' => sanitize_mime_type( $file['type'] ),
					'post_title' => str_replace( '-', ' ', sanitize_file_name( pathinfo( $file['file'], PATHINFO_FILENAME ) ) ),
					'post_status' => 'inherit'
				);
				
				if( $postid ) 
					$attach_id = wp_insert_attachment( $attachment, $file['file'], $postid );

				// Common attachment procedures
				require_once( ABSPATH . 'wp-admin' . '/includes/image.php' );
				$attach_data = wp_generate_attachment_metadata( $attach_id, $file['file'] );
				wp_update_attachment_metadata( $attach_id, $attach_data );

				if( $postid ) {		
					$large = wp_get_attachment_image_src( $attach_id, 'large' );		
					$thumb = wp_get_attachment_image_src( $attach_id, 'thumbnail' );
					
					//Return URL for the image field in meta box
					$file['large_url'] = $large[0];
					$file['thumb'] = $thumb[0];
				}
			}

			$file['type'] = $ext[1];
			// send the uploaded file url in response
			echo json_encode( $file );
			exit;
		}

		/**
		 * Display Toggle themify builder
		 * wp admin bar
		 */
		function builder_admin_bar_menu( $wp_admin_bar ) {
			global $wp_query, $post;
			
			if ( ( is_post_type_archive() && ! is_post_type_archive( 'product' ) ) || !is_admin_bar_showing() || is_admin() || !current_user_can( 'edit_page', $post->ID ) || isset( $wp_query->query_vars['product_cat'] ) || is_tax( 'product_tag' ) ) return;
			
			$args = array(
				array(
					'id'    => 'themify_builder',
					'title' => sprintf('<span class="themify_builder_front_icon"></span> %s', __('Themify Builder','themify')),
					'href'  => '#'
				),
				array(
					'id' => 'toggle_themify_builder',
					'parent' => 'themify_builder',
					'title' => __( 'Turn On Builder', 'themify' ),
					'href' => '#',
					'meta' => array( 'class' => 'toggle_tf_builder')
				),
				array(
					'id' => 'duplicate_themify_builder', 
					'parent' => 'themify_builder',
					'title' => __( 'Duplicate This Page', 'themify' ), 
					'href' => '#', 
					'meta' => array( 'class' => 'themify_builder_dup_link' )
				)
			);

			$help_args = array(
				array(
					'id' => 'help_themify_builder', 
					'parent' => 'themify_builder', 
					'title' => __( 'Help', 'themify' ), 
					'href' => 'http://themify.me/docs/builder',
					'meta' => array( 'target' => '_blank', 'class' => '' )
				)
			);

			if ( is_singular() || is_page() ) {
				$import_args = array(
					array(
						'id' => 'import_themify_builder',
						'parent' => 'themify_builder',
						'title' => __('Import From', 'themify'),
						'href' => '#'
					),
						// Sub Menu
						array(
							'id' => 'from_existing_pages_themify_builder',
							'parent' => 'import_themify_builder',
							'title' => __('Existing Pages', 'themify'),
							'href' => '#',
							'meta' => array( 'class' => 'themify_builder_import_page' )
						),
						array(
							'id' => 'from_existing_posts_themify_builder',
							'parent' => 'import_themify_builder',
							'title' => __('Existing Posts', 'themify'),
							'href' => '#',
							'meta' => array( 'class' => 'themify_builder_import_post' )
						),
					array(
						'id' => 'import_export_themify_builder',
						'parent' => 'themify_builder',
						'title' => __('Import / Export', 'themify'),
						'href' => '#'
					),	
						// Sub Menu
						array(
							'id' => 'import_file_themify_builder',
							'parent' => 'import_export_themify_builder',
							'title' => __('Import', 'themify'),
							'href' => '#',
							'meta' => array( 'class' => 'themify_builder_import_file' )
						),
						array(
							'id' => 'export_file_themify_builder',
							'parent' => 'import_export_themify_builder',
							'title' => __('Export', 'themify'),
							'href' => wp_nonce_url( '?themify_builder_export_file=true&postid=' . $post->ID, 'themify_builder_export_nonce' ),
							'meta' => array( 'class' => 'themify_builder_export_file' )
						),
				);
				$args = array_merge( $args, $import_args );
			}

			$args = array_merge( $args, $help_args );
			
			foreach ( $args as $arg ) {
				$wp_admin_bar->add_node( $arg );
			}
		}

		/**
		 * Switch to frontend
		 * @param int $post_id
		 */
		function switch_frontend( $post_id ) {
			//verify post is not a revision
			if ( ! wp_is_post_revision( $post_id ) ) {
				$redirect = isset( $_POST['builder_switch_frontend'] ) ? $_POST['builder_switch_frontend'] : 0;

				// redirect to frontend
				if( 1 == $redirect ) {
					$_POST['builder_switch_frontend'] = 0;
					$post_url = get_permalink( $post_id );
					wp_redirect( themify_https_esc( $post_url ) . '#builder_active' );
					exit;
				}
			}
		}

		/**
		 * Editing module panel in frontend
		 * @param $mod_name
		 * @param $mod_settings
		 */
		function module_edit_panel_front( $mod_name, $mod_settings ) {
			?>
			<div class="module_menu_front">
				<ul class="themify_builder_dropdown_front">
					<li><a href="#" title="<?php _e('Edit', 'themify') ?>" class="themify_module_options" data-module-name="<?php echo esc_attr( $mod_name ); ?>"><?php _e('Edit', 'themify') ?></a></li>
					<li><a href="#" title="<?php _e('Duplicate', 'themify') ?>" class="themify_module_duplicate"><?php _e('Duplicate', 'themify') ?></a></li>
					<li><a href="#" title="<?php _e('Delete', 'themify') ?>" class="themify_module_delete"><?php _e('Delete', 'themify') ?></a></li>
				</ul>
				<?php
					$mod_settings = $this->return_text_shortcode( $mod_settings );
					$mod_settings = json_encode( $mod_settings );
				?>
				<div class="front_mod_settings mod_settings_<?php echo $mod_name; ?>" data-settings="<?php echo esc_attr( $mod_settings ); ?>"></div>
			</div>
			<div class="themify_builder_data_mod_name" style="display:none;"><?php echo $mod_name; ?></div>
			<?php
		}

		/**
		 * Add Builder body class
		 * @param $classes
		 * @return mixed|void
		 */
		function body_class( $classes ) {
			if ( Themify_Builder_Model::is_frontend_editor_page() ) 
				$classes[] = 'frontend';

			// return the $classes array
			return apply_filters( 'themify_builder_body_class', $classes );
		}

		/**
		 * Just print the shortcode text instead of output html
		 * @param array $array
		 * @return array
		 */
		function return_text_shortcode( $array ) {
			if ( count( $array ) > 0 ) {
				foreach ( $array as $key => $value ) {
					if( is_array( $value ) ) {
						$this->return_text_shortcode( $value );
					} else {
						$array[ $key ] = str_replace( "[", "&#91;", $value );
						$array[ $key ] = str_replace( "]", "&#93;", $value ); 
					}
				}
			} else {
				$array = array();
			}
			return $array;
		}

		/**
		 * Retrieve builder templates
		 * @param $template_name
		 * @param array $args
		 * @param string $template_path
		 * @param string $default_path
		 * @param bool $echo
		 * @return string
		 */
		function retrieve_template( $template_name, $args = array(), $template_path = '', $default_path = '', $echo = true ) {
			ob_start();
			$this->get_template( $template_name, $args, $template_path = '', $default_path = '' );
			if ( $echo )
				echo ob_get_clean();
			else
				return ob_get_clean();
		}

		/**
		 * Get template builder
		 * @param $template_name
		 * @param array $args
		 * @param string $template_path
		 * @param string $default_path
		 */
		function get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
			if ( $args && is_array( $args ) )
				extract( $args );

			$located = $this->locate_template( $template_name, $template_path, $default_path );

			include( $located );
		}

		/**
		 * Locate a template and return the path for inclusion.
		 *
		 * This is the load order:
		 *
		 *		yourtheme		/	$template_path	/	$template_name
		 *		$default_path	/	$template_name
		 */
		function locate_template( $template_name, $template_path = '', $default_path = '' ) {
			if ( ! $template_path ) $template_path = $this->builder_settings['template_url'];
			if ( ! $default_path ) $default_path = $this->builder_settings['builder_path'];

			// Look within passed path within the theme - this is priority
			$template = locate_template(
				array(
					trailingslashit( $template_path ) . $template_name
				)
			);

			// Get default template
			if ( ! $template )
				$template = $default_path . $template_name;

			// Return what we found
			return apply_filters('themify_builder_locate_template', $template, $template_name, $template_path);
		}

		/**
		 * Get template for module
		 * @param $mod
		 * @param bool $echo
		 * @param bool $wrap
		 * @param null $class
		 * @param array $identifier
		 * @return bool|string
		 */
		function get_template_module( $mod, $builder_id = 0, $echo = true, $wrap = true, $class = null, $identifier = array() ) {
			$output = '';
			$mod['mod_name'] = isset( $mod['mod_name'] ) ? $mod['mod_name'] : '';
			$mod['mod_settings'] = isset( $mod['mod_settings'] ) ? $mod['mod_settings'] : array();
			
			$mod_id = $mod['mod_name'] . '-' . $builder_id . '-' . implode( '-', $identifier );
			$output .= PHP_EOL; // add line break

			// check whether module active or not
			if ( ! Themify_Builder_Model::check_module_active( $mod['mod_name'] ) ) 
				return false;

			if ( $wrap ) {
				ob_start(); ?>
				<div class="themify_builder_module_front clearfix module-<?php echo esc_attr( $mod['mod_name'] ); ?> active_module <?php echo $class; ?>" data-module-name="<?php echo esc_attr( $mod['mod_name'] ); ?>">
				<div class="themify_builder_module_front_overlay"></div>
				<?php themify_builder_edit_module_panel( $mod['mod_name'], $mod['mod_settings'] ); ?>
				<?php
				$output .= ob_get_clean();
			}
			$output .= $this->retrieve_template( 'template-'.$mod['mod_name'].'.php', array(
				'module_ID' => $mod_id,
				'mod_name' => $mod['mod_name'],
				'mod_settings' => ( isset( $mod['mod_settings'] ) ? $mod['mod_settings'] : '' )
			),'', '', false );
			$style_id = '.themify_builder .' . $mod_id;
			$output .= $this->get_custom_styling( $style_id, $mod['mod_name'], $mod['mod_settings'] );

			if ( $wrap ) 
				$output .= '</div>';

			// add line break
			$output .= PHP_EOL;

			if ( $echo ) {
				echo $output;
			} else {
				return $output;
			}
		}

		/**
		 * Check whether theme loop template exist
		 * @param string $template_name 
		 * @param string $template_path 
		 * @return boolean
		 */
		function is_loop_template_exist( $template_name, $template_path ) {
			$template = locate_template(
				array(
					trailingslashit( $template_path ) . $template_name
				)
			);

			if ( ! $template ) {
				return false;
			} else {
				return true;
			}
		}

		/**
		 * Get module excerpt
		 * @param array $module
		 */
		function get_module_excerpt( $module ) {
			switch ( $module['mod_name'] ) {
				case 'text':
					$content_text = isset( $module['mod_settings']['content_text'] ) ? $module['mod_settings']['content_text'] : '';
					$return = $this->split_words( $content_text, 100, '' );
					break;
				
				default:
					# code...
					$return = '';
					break;
			}

			return $return;
		}

		/**
		 * Split words function
		 * @param string $string
		 * @param int $nb_caracs
		 * @param string $separator
		 */
		function split_words( $string, $nb_caracs, $separator ) {
			$string = strip_tags( html_entity_decode( $string ) );
			if( strlen( $string ) <= $nb_caracs ) {
				$final_string = $string;
			} else {
				$final_string = "";
				$words = explode( " ", $string );
				foreach ( $words as $value ) {
					if( strlen( $final_string . " " . $value ) < $nb_caracs ) {
						if( ! empty( $final_string ) ) $final_string .= " ";
						$final_string .= $value;
					} else {
						break;
					}
				}
				$final_string .= $separator;
			}
			return $final_string;
		}

		/**
		 * Get checkbox data
		 * @param $setting
		 * @return string
		 */
		function get_checkbox_data( $setting ) {
			return implode( ' ', explode( '|', $setting ) );
		}

		/**
		 * Return only value setting
		 * @param $string 
		 * @return string
		 */
		function get_param_value( $string ) {
			$val = explode( '|', $string );
			return $val[0];
		}

		/**
		 * Get custom menus
		 * @param int $term_id
		 */
		function get_custom_menus( $term_id ) {
			$menu_list = '';
			ob_start();
			wp_nav_menu( array( 'menu' => $term_id ) );
			$menu_list .= ob_get_clean();

			return $menu_list;
		}

		/**
		 * Rewrite permalink for custom post type
		 */
		function rewrite_flush() {
			flush_rewrite_rules();
		}

		/**
		 * Display an additional column in categories list
		 * @since 1.1.8
		 */
		function taxonomy_header( $cat_columns ) {
			$cat_columns['cat_id'] = 'ID';
			return $cat_columns;
		}

		/**
		 * Display ID in additional column in categories list
		 * @since 1.1.8
		 */
		function taxonomy_column_id( $null, $column, $termid ){
			return $termid;
		}

		/**
		 * Includes this custom post to array of cpts managed by Themify
		 * @param Array
		 * @return Array
		 */
		function extend_post_types( $types ) {
			return array_merge( $types, $this->registered_post_types );
		}

		/**
		 * Push the registered post types to object class
		 * @param $type
		 */
		function push_post_types( $type ) {
			array_push( $this->registered_post_types, $type );
		}

		/**
		 * Detect mobile browser
		 */
		function isMobile() {
			return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
		}

		/**
		 * Get images from gallery shortcode
		 * @return object
		 */
		function get_images_from_gallery_shortcode( $shortcode ) {
			preg_match( '/\[gallery.*ids=.(.*).\]/', $shortcode, $ids );
			$image_ids = explode( ",", $ids[1] );
			$orderby = $this->get_gallery_param_option( $shortcode, 'orderby' );
			$orderby = $orderby != '' ? $orderby : 'post__in';
			$order = $this->get_gallery_param_option( $shortcode, 'order' );
			$order = $order != '' ? $order : 'ASC';

			// Check if post has more than one image in gallery
			return get_posts( array(
				'post__in' => $image_ids,
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'numberposts' => -1,
				'orderby' => $orderby,
				'order' => $order
			) );
		}

		/**
		 * Get gallery shortcode options
		 * @param $shortcode
		 * @param $param
		 */
		function get_gallery_param_option( $shortcode, $param = 'link' ) {
			if ( $param == 'link' ) {
				preg_match( '/\[gallery .*?(?=link)link=.([^\']+)./si', $shortcode, $out );
			} elseif ( $param == 'order' ) {
				preg_match( '/\[gallery .*?(?=order)order=.([^\']+)./si', $shortcode, $out );	
			} elseif ( $param == 'orderby' ) {
				preg_match( '/\[gallery .*?(?=orderby)orderby=.([^\']+)./si', $shortcode, $out );	
			} elseif ( $param == 'columns' ) {
				preg_match( '/\[gallery .*?(?=columns)columns=.([^\']+)./si', $shortcode, $out );	
			}
			
			$out = isset($out[1]) ? explode( '"', $out[1] ) : array('');
			return $out[0];
		}

		/**
		 * Reset builder query
		 * @param $action
		 */
		function reset_builder_query( $action = 'reset' ) {
			if ( 'reset' == $action ) {
				remove_filter( 'the_content', array( &$this, 'builder_show_on_front' ), 11 );
			} else if( $action == 'restore' ) {
				add_filter( 'the_content', array( &$this, 'builder_show_on_front' ), 11 );
			}
		}

		/**
		 * Check whether img.php is use or not
		 * @return boolean
		 */
		function is_img_php_disabled() {
			if ( themify_check( 'setting-img_settings_use' ) ) {
				return true;
			} else{
				return false;
			}
		}

		/**
		 * Checks whether the url is an img link, youtube, vimeo or not.
		 * @param string $url
		 * @return bool
		 */
		function is_img_link( $url ) {
			$parsed_url = parse_url( $url );
			$pathinfo = isset( $parsed_url['path'] ) ? pathinfo( $parsed_url['path'] ) : '';
			$extension = isset( $pathinfo['extension'] ) ? strtolower( $pathinfo['extension'] ) : '';

			$image_extensions = array('png', 'jpg', 'jpeg', 'gif');

			if ( in_array( $extension, $image_extensions ) || stripos( 'youtube', $url ) || stripos( 'vimeo', $url ) ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Get query page
		 */
		function get_paged_query() {
			global $wp;
			$page = 1;
			$qpaged = get_query_var( 'paged' );
			if ( ! empty( $qpaged ) ) {
				$page = $qpaged;
			} else {
				$qpaged = wp_parse_args( $wp->matched_query );
				if ( isset( $qpaged['paged'] ) && $qpaged['paged'] > 0 ) {
					$page = $qpaged['paged'];
				}
			}
			return $page;
		}

		/**
		 * Returns page navigation
		 * @param string Markup to show before pagination links
		 * @param string Markup to show after pagination links
		 * @param object WordPress query object to use
		 * @return string
		 */
		function get_pagenav( $before = '', $after = '', $query = false ) {
			global $wpdb, $wp_query;
			
			if( false == $query ){
				$query = $wp_query;
			}
			$request = $query->request;
			$posts_per_page = intval(get_query_var('posts_per_page'));
			$paged = intval($this->get_paged_query());
			$numposts = $query->found_posts;
			$max_page = $query->max_num_pages;
			$out = '';
		
			if(empty($paged) || $paged == 0) {
				$paged = 1;
			}
			$pages_to_show = apply_filters('themify_filter_pages_to_show', 5);
			$pages_to_show_minus_1 = $pages_to_show-1;
			$half_page_start = floor($pages_to_show_minus_1/2);
			$half_page_end = ceil($pages_to_show_minus_1/2);
			$start_page = $paged - $half_page_start;
			if($start_page <= 0) {
				$start_page = 1;
			}
			$end_page = $paged + $half_page_end;
			if(($end_page - $start_page) != $pages_to_show_minus_1) {
				$end_page = $start_page + $pages_to_show_minus_1;
			}
			if($end_page > $max_page) {
				$start_page = $max_page - $pages_to_show_minus_1;
				$end_page = $max_page;
			}
			if($start_page <= 0) {
				$start_page = 1;
			}
		
			if ($max_page > 1) {
				$out .=  $before.'<div class="pagenav clearfix">';
				if ($start_page >= 2 && $pages_to_show < $max_page) {
					$first_page_text = "&laquo;";
					$out .=  '<a href="'.get_pagenum_link().'" title="'.$first_page_text.'" class="number">'.$first_page_text.'</a>';
				}
				if($pages_to_show < $max_page)
					$out .= get_previous_posts_link('&lt;');
				for($i = $start_page; $i  <= $end_page; $i++) {
					if($i == $paged) {
						$out .=  ' <span class="number current">'.$i.'</span> ';
					} else {
						$out .=  ' <a href="'.get_pagenum_link($i).'" class="number">'.$i.'</a> ';
					}
				}
				if($pages_to_show < $max_page)
					$out .= get_next_posts_link('&gt;');
				if ($end_page < $max_page) {
					$last_page_text = "&raquo;";
					$out .=  '<a href="'.get_pagenum_link($max_page).'" title="'.$last_page_text.'" class="number">'.$last_page_text.'</a>';
				}
				$out .=  '</div>'.$after;
			}
			return $out;
		}

		/**
		 * Reset builder filter before template content render
		 */
		function do_reset_before_template_content_render(){
			$this->reset_builder_query();
		}

		/**
		 * Reset builder filter after template content render
		 */
		function do_reset_after_template_content_render(){
			$this->reset_builder_query('restore');
		}

		/**
		 * Check is plugin active
		 */
		function builder_is_plugin_active( $plugin ) {
			return in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
		}

		/**
		 * Include builder in search
		 * @param string $where 
		 * @return string
		 */
		function do_search( $where ){
			if( is_search() ) {
				global $wpdb;
				$query = get_search_query();
				$query = like_escape( $query );

				$where .=" OR {$wpdb->posts}.ID IN (SELECT {$wpdb->postmeta}.post_id FROM {$wpdb->posts}, {$wpdb->postmeta} WHERE {$wpdb->postmeta}.meta_key = '{$this->meta_key}' AND {$wpdb->postmeta}.meta_value LIKE '%$query%' AND {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id)";
			}
			return $where;
		}

		/**
		 * Builder Import Lightbox
		 * @return html
		 */
		function builder_import_ajaxify(){
			check_ajax_referer( 'tfb_load_nonce', 'nonce' );

			$type = $_POST['type'];
			$data = array();

			if ( 'post' == $type ) {
				$post_types = get_post_types( array('_builtin' => false) );
				$data[] = array(
					'post_type' => 'post',
					'label' => __('Post', 'themify'),
					'items' => get_posts( array( 'posts_per_page' => -1, 'post_type' => 'post' ) )
				);
				foreach( $post_types as $post_type ){
					$data[] = array(
						'post_type' => $post_type,
						'label' => ucfirst( $post_type ),
						'items' => get_posts( array( 'posts_per_page' => -1, 'post_type' => $post_type ) )
					);
				}

			} else if( 'page' == $type ){
				$data[] = array(
					'post_type' => 'page',
					'label' => __('Page', 'themify'),
					'items' => get_pages()
				);
			} else {
				die();
			}

			include_once THEMIFY_BUILDER_INCLUDES_DIR . '/themify-builder-import.php';
			die();
		}

		/**
		 * Process import builder
		 */
		function builder_import_submit_ajaxify(){
			check_ajax_referer( 'tfb_load_nonce', 'nonce' );
			parse_str( $_POST['data'], $imports );
			$import_to = (int) $_POST['importTo'];
			
			if ( count( $imports ) > 0 && is_array( $imports ) ) {
				$meta_values = array();

				// get current page builder data
				$current_builder_data = get_post_meta( $import_to, $this->meta_key, true );
				$current_builder_data = stripslashes_deep( maybe_unserialize( $current_builder_data ) );
				if ( count( $current_builder_data ) > 0 ) {
					$meta_values[] = $current_builder_data;
				}

				foreach( $imports as $post_type => $post_id ) {
					if ( empty( $post_id ) || $post_id == 0 ) continue;

					$builder_data = get_post_meta( $post_id, $this->meta_key, true );
					$builder_data = stripslashes_deep( maybe_unserialize( $builder_data ) );
					$meta_values[] = $builder_data;
				}

				if ( count( $meta_values ) > 0 ) {
					$result = array();
					foreach( $meta_values as $meta ) {
						$result = array_merge( $result, (array) $meta );
					}
					update_post_meta( $import_to, $this->meta_key, $result );
				}
			}

			die();
		}

		/**
		 * Output row styling style
		 * @param int $builder_id 
		 * @param array $row 
		 * @return string
		 */
		function render_row_styling( $builder_id, $row ) {
			$settings = $row['styling'];
			$style_id = '.themify_builder_content-' . $builder_id . ' .module_row_' . $row['row_order'];
			echo $this->get_custom_styling( $style_id, 'row', $settings );  
		}

		/**
		 * Update row style live preview
		 * @return json
		 */
		function update_row_style() {
			check_ajax_referer( 'tfb_load_nonce', 'nonce' );
			$settings = $_POST['postStyle'];
			$builder_id = (int) $_POST['builder_id'];
			$style_id = '.themify_builder_content-' . $builder_id . ' .current_selected_row';
			$response = array();

			$response['styles'] = $this->get_custom_styling( $style_id, 'row', $settings, true );
			$response['gfonts'] = $this->get_custom_google_fonts();
			$response['background_repeat'] = isset( $settings['background_repeat'] ) ? $settings['background_repeat'] : '';
			$response['animation_effect'] = isset( $settings['animation_effect'] ) ? $settings['animation_effect'] : '';
			$response['row_width'] = isset( $settings['row_width'] ) ? $settings['row_width'] : '';

			echo json_encode( $response );
			die();
		}

		/**
		 * Get custom style
		 * @param string $style_id 
		 * @param string $mod_name 
		 * @param array $settings 
		 * @param boolean $array 
		 * @return string|array
		 */
		function get_custom_styling( $style_id, $mod_name, $settings, $array = false ) {
			global $themify;

			if ( ! isset( $themify->builder_google_fonts ) ) {
				$themify->builder_google_fonts = '';
			}

			$rules_arr = array(
				'font_size' => array(
					'prop' => 'font-size',
					'key' => array('font_size', 'font_size_unit')
				),
				'font_family' => array(
					'prop' => 'font-family',
					'key' => 'font_family'
				),
				'line_height' => array(
					'prop' => 'line-height',
					'key' => array('line_height', 'line_height_unit')
				),
				'color' => array(
					'prop' => 'color',
					'key' => 'font_color'
				),
				'link_color' => array(
					'prop' => 'color',
					'key' => 'link_color'
				),
				'text_decoration' => array(
					'prop' => 'text-decoration',
					'key' => 'text_decoration'
				),
				'background_color' => array(
					'prop' => 'background-color',
					'key' => 'background_color'
				),
				'background_image' => array(
					'prop' => 'background-image',
					'key' => 'background_image'
				),
				'background_repeat' => array(
					'prop' => 'background-repeat',
					'key' => 'background_repeat'
				),
				'background_position' => array(
					'prop' => 'background-position',
					'key' => array( 'background_position_x', 'background_position_y' )
				),
				'padding' => array(
					'prop' => 'padding',
					'key' => array( 'padding_top', 'padding_right', 'padding_bottom', 'padding_left' )
				),
				'margin' => array(
					'prop' => 'margin',
					'key' => array( 'margin_top', 'margin_right', 'margin_bottom', 'margin_left' )
				),
				'border_top' => array(
					'prop' => 'border-top',
					'key' => array( 'border_top_color', 'border_top_width', 'border_top_style' )
				),
				'border_right' => array(
					'prop' => 'border-right',
					'key' => array( 'border_right_color', 'border_right_width', 'border_right_style' )
				),
				'border_bottom' => array(
					'prop' => 'border-bottom',
					'key' => array( 'border_bottom_color', 'border_bottom_width', 'border_bottom_style' )
				),
				'border_left' => array(
					'prop' => 'border-left',
					'key' => array( 'border_left_color', 'border_left_width', 'border_left_style' )
				)
			);
			

			if ( $mod_name != 'row' ) {
				$styles_selector = Themify_Builder_Model::$modules[ $mod_name ]->style_selectors;
			} else {
				$styles_selector = array(
					'.module_row' => array(
						'background_image', 'background_color', 'font_family', 'font_size', 'line_height', 'color', 'padding', 'margin', 'border_top', 'border_right', 'border_bottom', 'border_left'
					),
					'.module_row a' => array(
						'link_color', 'text_decoration'
					)
				);
			}
			$rules = array();
			$css = array();
			$style = '';

			foreach( $styles_selector as $selector => $properties ) {
				$property_arr = array();
				foreach( $properties as $property ) {
					array_push( $property_arr, $rules_arr[ $property ] );
				}
				$rules[ $style_id . $selector ] = $property_arr;
			}

			foreach ( $rules as $selector => $property ) {
				foreach ( $property as $val ) {
					$prop = $val['prop'];
					$key = $val['key'];

					if ( is_array( $key ) ) {
						if ( $prop == 'font-size' && isset( $settings[ $key[0] ] ) && '' != $settings[ $key[0] ] ) {
							$css[ $selector ][ $prop ] = $prop . ': ' . $settings[ $key[0] ] . $settings[ $key[1] ];
						} else if ( $prop == 'line-height' && isset( $settings[ $key[0] ] ) && '' != $settings[ $key[0] ] ) {
							$css[ $selector ][ $prop ] = $prop . ': ' . $settings[ $key[0] ] . $settings[ $key[1] ];
						} else if( $prop == 'background-position' && isset( $settings[ $key[0] ] ) && '' != $settings[ $key[0] ] ) {
							$css[ $selector ][ $prop ] = $prop . ': ' . $settings[ $key[0] ] . ' ' . $settings[ $key[1] ];
						} else if( $prop == 'padding' ) {
							$padding['top'] = isset( $settings[ $key[0] ]) && '' != $settings[ $key[0] ] ? $settings[ $key[0] ]  : '';
							$padding['right'] = isset( $settings[ $key[1] ]) && '' != $settings[ $key[1] ] ? $settings[ $key[1] ]  : '';
							$padding['bottom'] = isset( $settings[ $key[2] ]) && '' != $settings[ $key[2] ] ? $settings[ $key[2] ]  : '';
							$padding['left'] = isset( $settings[ $key[3] ]) && '' != $settings[ $key[3] ] ? $settings[ $key[3] ]  : '';
							
							foreach( $padding as $k => $v ) {
								if ( '' == $v ) continue;
								$css[ $selector ][ 'padding-' . $k ] = 'padding-'. $k .' : ' . $v . 'px' ;
							}

						} else if( $prop == 'margin' ) {
							$margin['top'] = isset( $settings[ $key[0] ]) && '' != $settings[ $key[0] ] ? $settings[ $key[0] ]  : '';
							$margin['right'] = isset( $settings[ $key[1] ]) && '' != $settings[ $key[1] ] ? $settings[ $key[1] ]  : '';
							$margin['bottom'] = isset( $settings[ $key[2] ]) && '' != $settings[ $key[2] ] ? $settings[ $key[2] ]  : '';
							$margin['left'] = isset( $settings[ $key[3] ]) && '' != $settings[ $key[3] ] ? $settings[ $key[3] ]  : '';
							
							foreach( $margin as $k => $v ) {
								if ( '' == $v ) continue;
								$css[ $selector ][ 'margin-' . $k ] = 'margin-'. $k .' : ' . $v . 'px' ;
							}

						} else if ( in_array( $prop, array('border-top', 'border-right', 'border-bottom', 'border-left' ) ) ) {
							$border['color'] = isset( $settings[ $key[0] ] ) && '' != $settings[ $key[0] ] ? '#' . $settings[ $key[0] ] : '' ;
							$border['width'] = isset( $settings[ $key[1] ] ) && '' != $settings[ $key[1] ] ? $settings[ $key[1] ] . 'px' : '';
							$border['style'] = isset( $settings[ $key[2] ] ) && '' != $settings[ $key[2] ] ? $settings[ $key[2] ] : '' ;
							$css[ $selector ][ $prop ] = $prop . ': ' . $border['color'] . ' ' . $border['width'] . ' ' . $border['style'];
							
							if ( empty( $border['color'] ) && empty( $border['width'] ) && empty( $border['style'] ) ) 
								unset( $css[ $selector ][ $prop ] );
						}
					} elseif ( isset( $settings[ $key ] ) && 'default' != $settings[ $key ] && '' != $settings[ $key ] ) {
						if ( $prop == 'color' || stripos( $prop, 'color' ) ) {
							$css[ $selector ][ $prop ] = $prop . ': #' . $settings[ $key ];
						}
						elseif ( $prop == 'background-image' && 'default' != $settings[ $key ] ) {
							$css[ $selector ][ $prop ] = $prop .': url(' . $settings[ $key ] . ')';
						}
						elseif ( $prop == 'font-family' ) {
							$font = $settings[ $key ];
							$css[ $selector ][ $prop ] = $prop .': '. $font;
							if ( ! in_array( $font, themify_get_web_safe_font_list( true ) ) ) {
								$themify->builder_google_fonts .= str_replace( ' ', '+', $font.'|' );
							}
						}
						else {
							$css[ $selector ][ $prop ] = $prop .': '. $settings[ $key ];
						}
					}

				}

				if ( ! empty( $css[ $selector ] ) ) {
					$style .= "$selector {\n\t" . implode( ";\n\t", $css[ $selector ] ) . "\n}\n";
				}
			}

			if ( ! $array ) {
				if ( '' != $style ) {
					return "\n<!-- $style_id Style -->\n<style>\n$style</style>\n<!-- End $style_id Style -->\n";
				}
			} else if ( $array ) {
				return $css;
			}

		}

		/**
		 * Get google fonts
		 */
		function get_custom_google_fonts() {
			global $themify;
			$fonts = array();

			if ( ! isset( $themify->builder_google_fonts ) || '' == $themify->builder_google_fonts ) return $fonts;
			$themify->builder_google_fonts = substr( $themify->builder_google_fonts, 0, -1 );
			$fonts = explode( '|', $themify->builder_google_fonts );
			return $fonts;
		}

		/**
		 * Add filter to module content
		 * @param string $content 
		 * @return string
		 */
		function the_module_content( $content ) {
			global $wp_embed;
			$content = $wp_embed->run_shortcode( $content );
			$content = do_shortcode( shortcode_unautop( $content ) );
			return $content;
		}

	}

} // class_exists check