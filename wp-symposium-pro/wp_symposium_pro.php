<?php
/*
Plugin Name: WP Symposium Pro
Plugin URI: http://www.simongoodchild.com
Description: Quickly and easily add a social network to your WordPress website! For loads more extensions, please <a href="http://www.wpsymposiumpro.com">visit the WP Symposium Pro website</a>.
Version: 14.12.1
Author: Simon Goodchild
Author URI: http://www.wpsymposiumpro.com
License: GPLv2 or later
*/

if ( !defined('WPS2_TEXT_DOMAIN') ) define('WPS2_TEXT_DOMAIN', 'wp-symposium-pro');
if ( !defined('WPS_PREFIX') ) define('WPS_PREFIX', 'wps');

// Re-write rules
add_filter( 'rewrite_rules_array','wps_forum_insert_rewrite_rules' );
add_action( 'wp_loaded','wps_forum_flush_rewrite_rules' );
add_filter( 'query_vars','wps_forum_insert_query_vars' );

// Language
add_action('plugins_loaded', 'wps_languages');

// Permalink re-writes
function wps_show_rewrite() {
	global $wp_rewrite;
	echo wps_display_array($wp_rewrite->rewrite_rules());
}
// Uncomment following line to view what is in WordPress re-write rules (debugging only)
//add_action('wp_head', 'wps_show_rewrite', 10);

function wps_flush_rewrite_rules()
{
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}
// Uncomment the following line to force a re-write flush (debugging only)
//add_action( 'init', 'wps_flush_rewrite_rules');

// Add WPS Pro re-write rules
function wps_forum_insert_rewrite_rules( $rules )
{
	global $wp_rewrite;

	$newrules = array();

	// Protection
	$newrules['wps_alerts/?'] = '/';
	$newrules['wps_forum_post/?'] = '/';

	if (is_multisite()) {

        $current_blog = get_current_blog_id();

        if ($current_blog > 1):

			$blog_details = get_blog_details($current_blog);

			// Usernames ---------------------
			if ($page_id = get_option('wpspro_profile_page')):
				$profile_page = get_post($page_id);
				$profile_page_slug = $profile_page->post_name;
				$newrules[$profile_page_slug.'/([^/]+)/?'] = ltrim($blog_details->path,'/').'?pagename='.$profile_page_slug.'&user=$matches[1]';
			endif;

			// Forum slugs -------------------
			$terms = get_terms( "wps_forum", array( ) );
			if ( count($terms) > 0 ):	
				foreach ( $terms as $term ):
					// Add re-write for Forum slug
					$post = get_post( wps_get_term_meta($term->term_id, 'wps_forum_cat_page', true) );
					if ($post):
						$newrules[$term->slug.'/([^/]+)/?'] = ltrim($blog_details->path,'/').'?pagename='.$post->post_name.'&topic=$matches[1]';
					endif;
				endforeach;
			endif;

		endif;

	} else {	

		// Usernames ---------------------
		if ($page_id = get_option('wpspro_profile_page')):
			if ($profile_page = get_post($page_id)):
				$profile_page_slug = $profile_page->post_name;
				$newrules[$profile_page_slug.'/([^/]+)/?'] = 'index.php?pagename='.$profile_page_slug.'&user=$matches[1]';
			endif;
		endif;

		// Forum slugs -------------------
		$terms = get_terms( "wps_forum", array( ) );
		if ( count($terms) > 0 ):	
			foreach ( $terms as $term ):

				$post = get_post( wps_get_term_meta($term->term_id, 'wps_forum_cat_page', true) );
				if ($post):
					$newrules[$term->slug.'/([^/]+)(.*)'] = 'index.php?pagename='.$post->post_name.'&topic=$matches[1]&fpage=$matches[2]';
					//$newrules[$term->slug.'/([^/]+)/?'] = 'index.php?pagename='.$post->post_name.'&topic=$matches[1]';
				endif;

			endforeach;
		endif;

	}	

	return $newrules + $rules;
}

// Flush re-write rules if need be
function wps_forum_flush_rewrite_rules(){
	
	$rules = get_option( 'rewrite_rules' );
	$flush = false;

	// Protection
	if ( ! isset( $rules['wps_alerts/?'] ) ) $flush = true;		
	if ( ! isset( $rules['wps_forum_post/?'] ) ) $flush = true;		

	if (is_multisite()) {

        $current_blog = get_current_blog_id();
		$blog_details = get_blog_details($current_blog);

		// Usernames ---------------------
		if ($page_id = get_option('wpspro_profile_page')):
			$profile_page = get_post($page_id);
			$profile_page_slug = $profile_page->post_name;
			if ( ! isset( $rules[$profile_page_slug.'/([^/]+)/?'] ) ) $flush = true;		
		endif;

		// Forum slugs -------------------
		$terms = get_terms( "wps_forum", array( ) );
		if ( count($terms) > 0 ):	
			foreach ( $terms as $term ):
				// Add re-write for Forum slug
				$post = get_post( wps_get_term_meta($term->term_id, 'wps_forum_cat_page', true) );
				if ($post):
					if ( ! isset( $rules[$term->slug.'/([^/]+)/?'] ) ) $flush = true;		
				endif;
			endforeach;
		endif;

	} else {	

		// Usernames ---------------------
		if ($page_id = get_option('wpspro_profile_page')):
			$profile_page = get_post($page_id);
			if ($profile_page):
				$profile_page_slug = $profile_page->post_name;
				if ( ! isset( $rules[$profile_page_slug.'/([^/]+)/?'] ) ) $flush = true;		
			endif;
		endif;

		// Forum slugs -------------------
		$terms = get_terms( "wps_forum", array( ) );
		if ( count($terms) > 0 ):	
			foreach ( $terms as $term ):

				$post = get_post( wps_get_term_meta($term->term_id, 'wps_forum_cat_page', true) );
				if ($post):
					if ( ! isset( $rules[$term->slug.'/([^/]+)/?'] ) ) $flush = true;		
				endif;

			endforeach;
		endif;

	}	

	// If required, flush re-write rules
	if ($flush) {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();			
	}

}

// Make re-write parameters available as query parameter
function wps_forum_insert_query_vars( $vars ){
    
    array_push($vars, 'topic');
    array_push($vars, 'fpage');
    array_push($vars, 'user');
    return $vars;

}

// Core functions
require_once('wps_core.php');

// Alerts
require_once('alerts/wps_custom_post_alerts.php');
require_once('alerts/wps_alerts_admin.php');
require_once('alerts/wps_alerts_shortcodes.php');
require_once('alerts/ajax_alerts.php');

// User meta
require_once('usermeta/wps_usermeta.php');
require_once('usermeta/wps_usermeta_help.php');
require_once('usermeta/wps_usermeta_shortcodes.php');
require_once('avatar/wps_avatar.php');

// Friendships
require_once('friendships/wps_friendships_core.php');
require_once('friendships/wps_custom_post_friendships.php');
require_once('friendships/wps_friendships_shortcodes.php');
require_once('friendships/wps_friendships_help.php');

// Activity
require_once('activity/wps_custom_post_activity.php');
require_once('activity/wps_activity_hooks_and_filters.php');
require_once('activity/ajax_activity.php');
require_once('activity/wps_activity_shortcodes.php');

// Forums
require_once('forums/wps_custom_post_forum.php');
require_once('forums/wps_custom_taxonomy_forum.php');
require_once('forums/wps_forum_shortcodes.php');
require_once('forums/ajax_forum.php');
require_once('forums/taxonomy-metadata.php');
require_once('forums/wps_forum_hooks_and_filters.php');
$taxonomy_metadata = new wps_Taxonomy_Metadata;
register_activation_hook( __FILE__, array($taxonomy_metadata, 'activate') );


// Admin
if (is_admin()):
	require_once('wps_admin.php');
	require_once('wps_setup_admin.php');
	require_once('usermeta/wps_usermeta_editor.php');
	require_once('forums/wps_forum_admin.php');
	require_once('forums/wps_forum_help.php');
endif;

// Enable shortcodes in text widgets.
add_filter('widget_text', 'do_shortcode');

// Init
add_action('init', 'wps_init');
add_action('init', 'wps_update_routine');
add_action('admin_menu', 'wps_menu'); // Located in wps_admin.php
add_action( 'wp_head', 'wps_add_custom_css' );
add_action( 'wp_footer', 'wps_add_wait_modal_box' );

// Handle update
function wps_update_routine() {
	if (is_admin() && current_user_can('manage_options')):
		$wps_ver = get_option('wp_symposium_pro_ver');
		if ($wps_ver != '14.12.1'):
			// Update groups last active flag
			// Placed here as this routine is the only place that is definitely run after update
			// Get all groups, and for each add a flag for active (set to 1, not date, specific value)
			// As can't set all as active with a date (that is unknown). Flag of 1 is recognised
			$args=array(
				'post_type' => 'wps_group',
				'posts_per_page' => -1,
				'post_status' => 'publish',
			);
			$groups = get_posts( $args );	
			if ($groups):
				foreach ($groups as $group):
					$group_updated = get_post_meta($group->ID, 'wps_group_updated', true);
					if (!$group_updated) update_post_meta( $group->ID, 'wps_group_updated', 1 );
				endforeach;
			endif;

			update_option('wp_symposium_pro_ver', '14.12.1');
			wpspro_release_notes();
		endif;
	endif;	
}


function wps_init() {

    // CSS
    wp_enqueue_style('wps-css', plugins_url('css/wp_symposium_pro.css', __FILE__), 'css');
    if (is_admin()):
    	// Alerts admin
		wp_enqueue_script('wps-alerts-js', plugins_url('alerts/wps_alerts.js', __FILE__), array('jquery'));	
		wp_localize_script('wps-alerts-js', 'wps_alerts', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ));    	
    	// Activity admin
		wp_enqueue_script('wps-activity-js', plugins_url('activity/wps_activity.js', __FILE__), array('jquery'));	
		wp_localize_script( 'wps-activity-js', 'wps_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );		
		// Forums admin
		wp_enqueue_script('wps-forum-js', plugins_url('forums/wps_forum.js', __FILE__), array('jquery'));	
		wp_localize_script( 'wps-forum-js', 'wps_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );		
		// Friendships
		wp_enqueue_script('wps-friendship-js', plugins_url('friendships/wps_friends.js', __FILE__), array('jquery'));
		wp_localize_script( 'wps-friendship-js', 'wps_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );		
		// Select2 replacement drop-down list
		wp_enqueue_script('wps-select2-js', plugins_url('js/select2.min.js', __FILE__), array('jquery'));	
		wp_enqueue_style('wps-select2-css', plugins_url('js/select2.css', __FILE__), 'css');
	    wp_enqueue_script('wps-admin-js', plugins_url('js/wps.admin.js', __FILE__), array('jquery'));
    else:
        // Core WPS JS
		wp_enqueue_script('wps-js', plugins_url('js/wp_symposium_pro.js', __FILE__), array('jquery'));	
    endif;

}

// ****************** ALERTS ******************

// On plugin activation schedule our regular notifications for alerts
register_activation_hook( __FILE__, 'wps_create_alerts_schedule' );
function wps_create_alerts_schedule() {

  // Use wp_next_scheduled to check if the event is already scheduled
  $timestamp = wp_next_scheduled( 'wps_symposium_pro_alerts_schedule' );

  // If $timestamp == false schedule since it hasn't been done previously
  if( $timestamp == false ){
    // Schedule the event for right now, then to repeat using the hook 'wps_symposium_pro_alerts_hook'
    wp_schedule_event( time(), 'wps_symposium_pro_alerts_schedule', 'wps_symposium_pro_alerts_hook' );
  }

}

add_filter( 'cron_schedules', 'wps_add_alerts_schedule' ); 
function wps_add_alerts_schedule( $schedules ) {

	$seconds = ($value = get_option('wps_alerts_cron_schedule')) ? $value : 3600; // Defaults to every hour
	$schedules['wps_symposium_pro_alerts_schedule'] = array(
		'interval' => $seconds, // in seconds
		'display' => __( 'WP Symposium Pro alerts schedule', WPS2_TEXT_DOMAIN )
	);
	return $schedules;
	
}

// ****************** ACTIVITY ******************

// Over-ride profile title and canonical URL
//add_filter( 'wp_title', 'wps_activity_post_title', 100 );
function wps_activity_post_title($title) {
    
    $parts = explode('/', $_SERVER["REQUEST_URI"]);
    $p = get_page_by_path($parts[1],OBJECT,'page');
    if (wps_is_profile_page($p->ID)):
        global $current_user;
    // xxxx
        if (isset($parts[2])):
            return $parts[2].':'.$current_user->display_name;
        else:
            return $parts[2].':'.$current_user->display_name;
        endif;
    else:
        return $title;
    endif;
    
}

    
// ****************** FORUMS ******************

// Over-ride forum title and canonical URL
add_filter( 'wp_title', 'wps_forum_post_title', 100 );
function wps_forum_post_title($title) {

    $parts = explode('/', $_SERVER["REQUEST_URI"]);
    if ($parts && isset($parts[2]) && $parts[2]):
		$p = get_page_by_path($parts[2],OBJECT,'wps_forum_post');
		if( $p ):
			$post_terms = get_the_terms( $p->ID, 'wps_forum' );
			if ($post_terms):
				$return = '';
				foreach( $post_terms as $term ):
					$return = $p->post_title.' | '.$term->name.' | '.get_bloginfo('name');
					remove_action( 'wp_head', 'rel_canonical' ); // Remove WordPress canonical URL
					if (function_exists('__return_false')) add_filter( 'wpseo_canonical', '__return_false' ); // Disable Yoast SEO canonical URL
					add_action( 'wp_head', 'wps_rel_canonical_forum_override' ); // Replace with forum URL					
				endforeach;
				return $return ? $return : $title;
			else:
			    return $title;
			endif;
		else:
		    return $title;
		endif;
	else:
		return $title;
	endif;

}

function wps_rel_canonical_forum_override()
{
	$link = get_bloginfo('url').$_SERVER["REQUEST_URI"];
    echo "<link rel='canonical' href='" . esc_url( $link ) . "' />\n";
}

// Over-ride Yoast og:title with forum title
function wps_opengraph_title( $title ) {

    $parts = explode('/', $_SERVER["REQUEST_URI"]);
    if ($parts && isset($parts[2]) && $parts[2]):
		$p = get_page_by_path($parts[2],OBJECT,'wps_forum_post');
		if( $p ):
			$post_terms = get_the_terms( $p->ID, 'wps_forum' );
			if ($post_terms):
				$return = '';
				foreach( $post_terms as $term ):
					$return = $p->post_title.' | '.$term->name.' | '.get_bloginfo('name');
				endforeach;
				return $return ? $return : $title;
			else:
			    return $title;
			endif;
		else:
		    return $title;
		endif;
	else:
		return $title;
	endif;

}
add_filter( 'wpseo_opengraph_title', 'wps_opengraph_title' );

// Over-ride Yoast og:description with forum post
function wps_wpseo_metadesc( $title ) {

    $parts = explode('/', $_SERVER["REQUEST_URI"]);
    if ($parts && isset($parts[2]) && $parts[2]):
		$p = get_page_by_path($parts[2],OBJECT,'wps_forum_post');
		if( $p ):
			$post_terms = get_the_terms( $p->ID, 'wps_forum' );
			if ($post_terms):
				$return = '';
				foreach( $post_terms as $term ):
					$return = html_entity_decode(htmlspecialchars_decode(strip_tags($p->post_content), ENT_QUOTES));
					if (strlen($return) > 300) $return = substr($return, 0, 300);
				endforeach;
				return $return ? $return : $title;
			else:
			    return $title;
			endif;
		else:
		    return $title;
		endif;
	else:
		return $title;
	endif;

}
add_filter( 'wpseo_metadesc', 'wps_wpseo_metadesc' );


// Add menu items for forum
add_action( 'admin_menu', 'wps_add_forums_menu' );
function wps_add_forums_menu() {
	add_submenu_page('wps_pro', __('Forum Setup', WPS2_TEXT_DOMAIN), __('Forum Setup', WPS2_TEXT_DOMAIN), 'manage_options', 'edit-tags.php?taxonomy=wps_forum&post_type=wps_forum_post');
	add_submenu_page('wps_pro', __('All Forums', WPS2_TEXT_DOMAIN), __('All Forums', WPS2_TEXT_DOMAIN), 'manage_options', 'wpspro_forum_setup', 'wpspro_forum_setup');
}


// ****************** LANGUAGE FILES ******************

function wps_languages() {


	if ( file_exists(dirname(__FILE__).'/languages/') ) {
        load_plugin_textdomain(WPS2_TEXT_DOMAIN, false, dirname(plugin_basename(__FILE__)).'/languages/');
    } else {
        if ( file_exists(dirname(__FILE__).'/lang/') ) {
            load_plugin_textdomain(WPS2_TEXT_DOMAIN, false, dirname(plugin_basename(__FILE__)).'/lang/');
        } else {
        	$path = dirname(plugin_basename(__FILE__)).'/../';
	        if ( $path ) {
				load_plugin_textdomain(WPS2_TEXT_DOMAIN, false, $path);
			}
        }
    }

}

// ****************** MISCELLANEOUS FUNCTIONS ******************

// Check for applicable forum shortcodes in page
function wps_is_forum_page($id) {
    
    $ret = false;
    $p = get_post($id);
    if ( has_shortcode( $p->post_content, WPS_PREFIX.'-forum-page' ) ) $ret = true;
    if ( has_shortcode( $p->post_content, WPS_PREFIX.'-forum-post' ) ) $ret = true;
    if ( has_shortcode( $p->post_content, WPS_PREFIX.'-forum-comment' ) ) $ret = true;
    if ( has_shortcode( $p->post_content, WPS_PREFIX.'-forum' ) ) $ret = true;
    
    return $ret;
}

// Check for applicable profile shortcodes in page
function wps_is_profile_page($id) {
    
    $ret = false;
    $p = get_post($id);
    if ( has_shortcode( $p->post_content, WPS_PREFIX.'-activity-page' ) ) $ret = true;
    if ( has_shortcode( $p->post_content, WPS_PREFIX.'-activity' ) ) $ret = true;
    
    return $ret;
}


?>