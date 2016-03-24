<?php
/**
 * @package themify
 */

class Themify_Editor_GFonts {

	/**
	 * list of available fonts in the editor
	 */
	var $fonts;

	/**
	 * a list of fonts to be queued on the page
	 */
	var $queue;
	var $setting_name = 'setting-editor-gfonts';

	public function __construct() {
		$data = themify_get_data();
		$this->fonts = isset( $data[$this->setting_name] ) ? (array) json_decode( $data[$this->setting_name] ) : array();

		if( ! empty( $this->fonts ) ) {
			add_action( 'wp_footer', array( $this, 'load_user_fonts' ), 1 );

			if ( current_user_can( 'publish_posts' ) && get_user_option( 'rich_editing' ) == 'true' ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'admin_enqueue' ) ); /* init vars on frontend as well, for Builder */
				add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ) );
				add_filter( 'mce_buttons_2', array( $this, 'mce_buttons' ) );
			}
		}

		add_action( 'admin_init', array( $this, 'setup_defaults' ) );
		add_filter( 'themify_theme_config_setup', array( $this, 'config_setup' ), 12 );
		add_shortcode( 'themify_gfont', array( $this, 'add_font_to_queue' ) );
	}

	public function setup_defaults() {
		$data = themify_get_data();
		if( isset( $data[$this->setting_name] ) && null === $data[$this->setting_name] ) {
			$this->fonts = array( 'Muli', 'Jura', 'Istok Web', 'Nunito', 'Lato', 'PT Sans', 'EB Garamond', 'Oranienbaum', 'Kameron', 'Lustria', 'Cinzel', 'Oswald', 'Open Sans' );
			$data[$this->setting_name] = json_encode( $this->fonts );
			update_option( 'themify_data', $data );
		}
	}

	public function add_font_to_queue( $atts ) {
		if( ! isset( $atts['id'] ) || ! themify_is_google_fonts( $atts['id'] ) ) {
			return '';
		}

		$this->queue[sanitize_title_with_dashes($atts['id'])] = $atts['id'];
		return '';
	}

	public function load_user_fonts() {
		if( ! empty( $this->queue ) ) {
			$url = $this->get_fonts_url( $this->queue );
			echo $this->get_fonts_classnames( $this->queue ) . "\n";
			printf( '<link rel="stylesheet" href="%s" type="text/css" id="themify-editor-google-fonts" />', $url );
		}
	}

	function get_fonts_list() {
		$fonts = array();
		foreach( $this->fonts as $font ) {
			$key = sanitize_title_with_dashes( $font );
			$fonts[$key] = $font;
		}

		return $fonts;
	}

	public function get_fonts_classnames( $fonts = null ) {
		if( empty( $fonts ) ) {
			$fonts = $this->get_fonts_list();
		}
		$output = '';
		if( ! empty( $fonts ) ) {
			$output .= "\n<!-- Google fonts -->\n<style>\n";
			foreach( $fonts as $key => $value ) {
				$output .= ".{$key} { font-family: '{$value}'; }\n";
			}
			$output .= "</style>";
		}

		return $output;
	}

	public function get_fonts_url( $fonts, $user_subsets = array() ) {
		$fonts = array_unique( $fonts );

		/* default font subsets */
		if ( empty( $user_subsets ) && themify_check( 'setting-webfonts_subsets' ) && '' != themify_get( 'setting-webfonts_subsets' ) ) {
			$user_subsets = explode( ',', str_replace( ' ', '', themify_get( 'setting-webfonts_subsets' ) ) );
		} else {
			$user_subsets = array();
		}

		$subsets = apply_filters( 'themify_google_fonts_subsets', array_merge( array( 'latin' ), $user_subsets ) );
		$query = null;
		$families = array();
		foreach ( $fonts as $font ) {
			$words = explode( '-', $font );
			$variant = themify_get_gfont_variant( $font );
			foreach ( $words as $key => $word ) {
				$words[$key] = ucwords( $word );
			}
			array_push( $families, implode( '+', $words ) . ':' . $variant );
		}

		if ( ! empty( $families ) ) {
			$query .= '?family=' . implode( '|', $families );
			$query .= '&subset=' . implode( ',', $subsets );

			$protocol = ( is_ssl() ) ? 'https' : 'http';
			$url = $protocol . '://fonts.googleapis.com/css';
			$url .= $query;
			return $url;
		}

		return false;
	}

	function config_setup( $themify_theme_config ) {
		$themify_theme_config['panel']['settings']['tab']['general']['custom-module'][] = array(
			'title' => __( 'Custom Google Fonts in WordPress Editor', 'themify' ),
			'function' => 'themify_editor_gfonts_config_view',
		);

		return $themify_theme_config;
	}

	function config_view( $data = array() ) {
		global $themify_gfonts;

		$output = '<p><span class="label">' . __( 'Fonts to load', 'themify' ) . '</span> 
					<select class="fontFamily width10" multiple style="min-height: 100px" id="'. $this->setting_name .'-list">';
		if ( sizeof( $themify_gfonts ) > 0 ) {
			foreach ( $themify_gfonts as $font ) {
				$selected = ( in_array( $font['family'], $this->fonts ) ) ? ' selected="selected"' : '';
				$output .= '<option value=\''.$font['family'].'\'' . $selected . '>'.$font['family'].'</option>';	
			}
		}

		$output .= '</select><input type="hidden" name="'. $this->setting_name .'" id="'. $this->setting_name .'" value=\''. json_encode( $this->fonts ) .'\' />';
		$output .= '<small>'. __( 'Note that every font you enable will add to the loading of the site, select only the ones you want to use on your website.', 'themify' ) .'</small></p>';

		return $output;
	}

	function mce_buttons( $buttons ) {
		array_unshift( $buttons, 'themifyCustomFonts' );

		return $buttons;
	}

	function mce_external_plugins( $plugin_array ) {
		global $wp_version;

		if ( false !== stripos( $wp_version, '3.9' ) ) {
			$plugin_array['themifyCustomFonts'] = THEMIFY_URI . '/tinymce/tinymce.customfonts.js';
		} else {
			$plugin_array['themifyCustomFonts'] = THEMIFY_URI . '/tinymce/tinymce.customfonts-compat.js';
		}

		return $plugin_array;
	}

	function admin_enqueue() {
		wp_localize_script( 'editor', 'themifyCustomFonts', array(
			'fonts' => $this->get_fonts_list(),
			'stylesheet' => $this->get_fonts_url( $this->fonts ), /* pass the URL of the Google fonts stylesheet and manually inject it into the editor */
			'styles' => $this->get_fonts_classnames(),
			'label' => __( 'Custom Font', 'themify' ),
			'default_text' => __( 'Themify says hi!', 'themify' ),
			'add_more' => __( 'Add more fonts', 'themify' ),
			'themify_page_url' => add_query_arg( array( 'page' => 'themify' ), admin_url( 'admin.php' ) ),
		) );
	}
}
$GLOBALS['themify_editor_gfonts'] = new Themify_Editor_GFonts();