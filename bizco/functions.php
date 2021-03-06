<?php
/***************************************************************************
 *
 * 	----------------------------------------------------------------------
 * 						DO NOT EDIT THIS FILE
 *	----------------------------------------------------------------------
 * 
 *  					Copyright (C) Themify
 * 						http://themify.me
 *
 ***************************************************************************/

$theme_includes = apply_filters( 'themify_theme_includes',
	array(	'themify/themify-database.php',
			'themify/class-themify-config.php',
			'themify/themify-utils.php',
			'themify/themify-config.php',
			'themify/themify-modules.php',
			'theme-options.php',
			'theme-modules.php',
			'theme-functions.php',
			'custom-modules.php',
			'custom-functions.php',
			'theme-class.php',
			'themify/themify-widgets.php' ));
			
foreach ( $theme_includes as $include ) { locate_template( $include, true ); }

function new_excerpt_length($length) {
    return 26;
}

function fb_disable_rss_feed() {
    wp_die( __('Feed is not available please return back to the <a href="'. get_bloginfo('url') .'">homepage</a>!') );
}

add_action('do_feed', 'fb_disable_rss_feed', 1);
add_action('do_feed_rdf', 'fb_disable_rss_feed', 1);
add_action('do_feed_rss', 'fb_disable_rss_feed', 1);
add_action('do_feed_rss2', 'fb_disable_rss_feed', 1);
add_action('do_feed_atom', 'fb_disable_rss_feed', 1);

add_filter('excerpt_length', 'new_excerpt_length');

/**********************************************************************************************************
 * 
 * Do not edit this file.
 * To add custom PHP functions to the theme, create a new 'custom-functions.php' file in the theme folder.
 * 
***********************************************************************************************************/
?>