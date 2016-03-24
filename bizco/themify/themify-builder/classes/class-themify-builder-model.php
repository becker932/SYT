<?php

final class Themify_Builder_model {
	/**
	 * Feature Image
	 * @var array
	 */
	static public $post_image = array();
	
	/**
	 * Feature Image Size
	 * @var array
	 */
	static public $featured_image_size = array();

	/**
	 * Image Width
	 * @var array
	 */
	static public $image_width = array();

	/**
	 * Image Height
	 * @var array
	 */
	static public $image_height = array();

	/**
	 * External Link
	 * @var array
	 */
	static public $external_link = array();

	/**
	 * Lightbox Link
	 * @var array
	 */
	static public $lightbox_link = array();

	static public $modules = array();

	static public function register_module( $class, $settings ) {
		if ( class_exists( $class ) ) {

			$instance = new $class();

			self::$modules[ $instance->slug ] = $instance;

			if ( is_user_logged_in() ) {
				self::$modules[ $instance->slug ]->options = isset( $settings['options'] ) ? $settings['options'] : array();
				self::$modules[ $instance->slug ]->styling = isset( $settings['styling'] ) ? $settings['styling'] : array();
			}
			self::$modules[ $instance->slug ]->style_selectors = isset( $settings['styling_selector'] ) ? $settings['styling_selector'] : array();
		}
	}

	/**
	 * Check whether builder is active or not
	 * @return bool
	 */
	static public function builder_check() {
		$enable_builder = apply_filters( 'themify_enable_builder', themify_get('setting-page_builder_is_active') );
		if ( 'disable' == $enable_builder ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Check whether module is active
	 * @param $name
	 * @return boolean
	 */
	static public function check_module_active( $name ) {
		if ( isset( self::$modules[ $name ] ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check is frontend editor page
	 */
	static public function is_frontend_editor_page() {
		global $post;
		if ( is_user_logged_in() && current_user_can( 'edit_page', $post->ID ) ) {
			return true;
		} else{
			return false;
		}
	}

	/**
	 * Load general metabox fields
	 */
	static public function load_general_metabox() {
		// Feature Image
		self::$post_image = apply_filters( 'themify_builder_metabox_post_image', array(
			'name' 		=> 'post_image',	
			'title' 	=> __('Featured Image', 'themify'),
			'description' => '', 				
			'type' 		=> 'image',			
			'meta'		=> array()
		) );
		// Featured Image Size
		self::$featured_image_size = apply_filters( 'themify_builder_metabox_featured_image_size', array(
			'name'	=>	'feature_size',
			'title'	=>	__('Image Size', 'themify'),
			'description' => __('Image sizes can be set at <a href="options-media.php">Media Settings</a> and <a href="admin.php?page=themify_regenerate-thumbnails">Regenerated</a>', 'themify'),
			'type'		 =>	'featimgdropdown'
		) );
		// Image Width
		self::$image_width = apply_filters( 'themify_builder_metabox_image_width', array(
			'name' 		=> 'image_width',
			'title' 	=> __('Image Width', 'themify'),
			'description' => '',			
			'type' 		=> 'textbox',
			'meta'		=> array('size'=>'small')
		) );
		// Image Height
		self::$image_height = apply_filters( 'themify_builder_metabox_image_height', array(
			'name' 		=> 'image_height',
			'title' 	=> __('Image Height', 'themify'),
			'description' => '',
			'type' 		=> 'textbox',
			'meta'		=> array('size'=>'small')
		) );
		// External Link
		self::$external_link = apply_filters( 'themify_builder_metabox_external_link', array(
			'name' 		=> 'external_link',
			'title' 	=> __('External Link', 'themify'),
			'description' => __('Link Featured Image to external URL', 'themify'),
			'type' 		=> 'textbox',
			'meta'		=> array()
		) );
		// Lightbox Link
		self::$lightbox_link = apply_filters( 'themify_builder_metabox_lightbox_link', array(
			'name' 		=> 'lightbox_link',
			'title' 	=> __('Lightbox Link', 'themify'),
			'description' => __('Link Featured Image to lightbox image, video or external iframe', 'themify'),
			'type' 		=> 'textbox',
			'meta'		=> array()
		) );
	}

	/**
	 * Get module name by slug
	 * @param string $slug 
	 * @return string
	 */
	static public function get_module_name( $slug ) {
		if ( is_object( self::$modules[ $slug ] ) ) {
			return self::$modules[ $slug ]->name;
		} else {
			return $slug;
		}
	}
}