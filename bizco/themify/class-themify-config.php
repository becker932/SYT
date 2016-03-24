<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ThemifyConfig {
	var $theme_dir;

	function __construct() {
		$this->theme_dir = get_template_directory(); 
	}

	function get_config() {
		return $this->read_config();
	}

	function read_config() {
		$the_config_file = ( is_file( $this->theme_dir .'/custom-config.php' ) ) ? 'custom-config.php' : 'theme-config.php';
		$the_config_file = $this->theme_dir . '/' . $the_config_file;
		
		include( $the_config_file );
		
		return apply_filters( 'themify_theme_config_setup', $themify_theme_config );
	}

}

/**
 * Initializes Themify class
 */
function themify_theme_config_init(){
	/**
	 * Themify initialization class
	 */
	$GLOBALS['ThemifyConfig'] = new ThemifyConfig();
}
add_action( 'after_setup_theme', 'themify_theme_config_init', 8 );