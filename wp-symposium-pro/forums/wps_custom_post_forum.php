<?php

/* Create forum_post custom post type */

/* =========================== LABELS FOR ADMIN =========================== */


function wps_custom_post_forum_post() {
	$labels = array(
		'name'               => __( 'Posts', WPS2_TEXT_DOMAIN ),
		'singular_name'      => __( 'Post', WPS2_TEXT_DOMAIN ),
		'add_new'            => __( 'Add New', WPS2_TEXT_DOMAIN ),
		'add_new_item'       => __( 'Add New post', WPS2_TEXT_DOMAIN ),
		'edit_item'          => __( 'Edit post', WPS2_TEXT_DOMAIN ),
		'new_item'           => __( 'New post', WPS2_TEXT_DOMAIN ),
		'all_items'          => __( 'Forum Posts', WPS2_TEXT_DOMAIN ),
		'view_item'          => __( 'View Forum Post', WPS2_TEXT_DOMAIN ),
		'search_items'       => __( 'Search Forum Posts', WPS2_TEXT_DOMAIN ),
		'not_found'          => __( 'No forum post found', WPS2_TEXT_DOMAIN ),
		'not_found_in_trash' => __( 'No forum post found in the Trash', WPS2_TEXT_DOMAIN ), 
		'parent_item_colon'  => '',
		'menu_name'          => __('Forum Posts', WPS2_TEXT_DOMAIN),
	);
	$args = array(
		'labels'        		=> $labels,
		'description'   		=> 'Holds our forum post specific data',
		'public'        		=> true,
		'exclude_from_search' 	=> true,
		'show_in_menu' 			=> 'wps_pro',
		'publicly_queryable'	=> false,
		'has_archive'			=> false,
		'rewrite'				=> false,
		'supports'      		=> array( 'title', 'editor', 'comments', 'thumbnail' ),
		'has_archive'   		=> false,
	);
	register_post_type( 'wps_forum_post', $args );
}
add_action( 'init', 'wps_custom_post_forum_post' );

/* =========================== MESSAGES FOR ADMIN =========================== */

function wps_updated_forum_post_messages( $messages ) {
	global $post, $post_ID;
	$messages['wps_forum_post'] = array(
		0 => '', 
		1 => __('Post updated.', WPS2_TEXT_DOMAIN),
		2 => __('Custom field updated.', WPS2_TEXT_DOMAIN),
		3 => __('Custom field deleted.', WPS2_TEXT_DOMAIN),
		4 => __('Post updated.', WPS2_TEXT_DOMAIN),
		5 => isset($_GET['revision']) ? sprintf( __('Post restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => __('Post published.', WPS2_TEXT_DOMAIN),
		7 => __('Post saved.', WPS2_TEXT_DOMAIN),
		8 => __('Post submitted.', WPS2_TEXT_DOMAIN),
		9 => sprintf( __('Post scheduled for: <strong>%1$s</strong>.'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) ),
		10 => __('Post draft updated.', WPS2_TEXT_DOMAIN),
	);
	return $messages;
}
add_filter( 'post_updated_messages', 'wps_updated_forum_post_messages' );


/* =========================== META FIELDS CONTENT BOX WHEN EDITING =========================== */


add_action( 'add_meta_boxes', 'forum_post_info_box' );
function forum_post_info_box() {
    add_meta_box( 
        'forum_post_info_box',
        __( 'Post Details', WPS2_TEXT_DOMAIN ),
        'forum_post_info_box_content',
        'wps_forum_post',
        'side',
        'high'
    );
}

function forum_post_info_box_content( $post ) {
	global $wpdb;
	wp_nonce_field( 'forum_post_info_box_content', 'forum_post_info_box_content_nonce' );

	echo '<strong>'.__('Post author', WPS2_TEXT_DOMAIN).'</strong><br />';
	$author = get_user_by('id', $post->post_author);
	echo $author->display_name.'<br />';
	echo 'ID: '.$author->ID;

	echo '<br /><br >';
	echo '<input type="checkbox" name="wps_sticky"';
		if (get_post_meta($post->ID, 'wps_sticky', true)) echo ' CHECKED';
		echo '> '.__('Stick to top of posts?', WPS2_TEXT_DOMAIN);
}

add_action( 'save_post', 'forum_post_info_box_save' );
function forum_post_info_box_save( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	return;

	if ( !isset($_POST['forum_post_info_box_content_nonce']) || !wp_verify_nonce( $_POST['forum_post_info_box_content_nonce'], 'forum_post_info_box_content' ) )
	return;

	if ( !current_user_can( 'edit_post', $post_id ) ) return;

	if (isset($_POST['wps_sticky'])):
		update_post_meta($post_id, 'wps_sticky', true);
	else:
		delete_post_meta($post_id, 'wps_sticky', true);
	endif;


}

/* =========================== COLUMNS WHEN VIEWING =========================== */

/* Columns for Posts list */
add_filter('manage_posts_columns', 'forum_post_columns_head');
add_action('manage_posts_custom_column', 'forum_post_columns_content', 10, 2);

// ADD NEW COLUMN
function forum_post_columns_head($defaults) {
    global $post;
	if ($post->post_type == 'wps_forum_post') {
    }
    return $defaults;
}
 
// SHOW THE COLUMN CONTENT
function forum_post_columns_content($column_name, $post_ID) {

}

/* =========================== ALTER VIEW POST LINKS =========================== */

function wps_change_forum_link( $permalink, $post ) {

	if ($post->post_type == 'wps_forum_post'):

		$post_terms = get_the_terms( $post->ID, 'wps_forum' );
		if( $post_terms && !is_wp_error( $post_terms ) ):
		    foreach( $post_terms as $term ):
		    	if ( wps_using_permalinks() ):	
		        	$permalink = home_url( $term->slug.'/'.$post->post_name );	    	
	    	    	break;
	    	    else:
	    	    	$forum_page_id = wps_get_term_meta($term->term_id, 'wps_forum_cat_page', true);
					$permalink = home_url( "/?page_id=".$forum_page_id."&topic=".$post->post_name );
	    	    endif;
		    endforeach;
		endif;

	endif;

    return $permalink;

}
add_filter('post_type_link',"wps_change_forum_link",10,2);
?>