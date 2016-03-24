<?php

/* Create Activity custom post type */


/* =========================== LABELS FOR ADMIN =========================== */


function wps_custom_post_activity() {
	$labels = array(
		'name'               => __( 'Activity', WPS2_TEXT_DOMAIN ),
		'singular_name'      => __( 'Activity',  WPS2_TEXT_DOMAIN ),
		'add_new'            => __( 'Add New',  WPS2_TEXT_DOMAIN ),
		'add_new_item'       => __( 'Add New Activity', WPS2_TEXT_DOMAIN ),
		'edit_item'          => __( 'Edit Activity', WPS2_TEXT_DOMAIN ),
		'new_item'           => __( 'New Activity', WPS2_TEXT_DOMAIN ),
		'all_items'          => __( 'Activity', WPS2_TEXT_DOMAIN ),
		'view_item'          => __( 'View Activity', WPS2_TEXT_DOMAIN ),
		'search_items'       => __( 'Search Activity', WPS2_TEXT_DOMAIN ),
		'not_found'          => __( 'No activity found', WPS2_TEXT_DOMAIN ),
		'not_found_in_trash' => __( 'No activity found in the Trash', WPS2_TEXT_DOMAIN ), 
		'parent_item_colon'  => '',
		'menu_name'          => __('Activity', WPS2_TEXT_DOMAIN),
	);
	$args = array(
		'labels'        		=> $labels,
		'description'   		=> 'Holds our activity specific data',
		'public'        		=> true,
		'exclude_from_search' 	=> true,
		'rewrite'				=> false,
		'show_in_menu' 			=> 'wps_pro',
		'supports'      		=> array( 'title', 'thumbnail' ),
		'has_archive'   		=> false,
	);
	register_post_type( 'wps_activity', $args );
}
add_action( 'init', 'wps_custom_post_activity' );

/* =========================== MESSAGES FOR ADMIN =========================== */

function wps_updated_activity_messages( $messages ) {
	global $post, $post_ID;
	$messages['wps_activity'] = array(
		0 => '', 
		1 => __('Activity updated.'),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('Activity updated.'),
		5 => isset($_GET['revision']) ? sprintf( __('Activity restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => __('Activity published.'),
		7 => __('Activity saved.'),
		8 => __('Activity submitted.'),
		9 => sprintf( __('Activity scheduled for: <strong>%1$s</strong>.'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) ),
		10 => __('Activity draft updated.'),
	);
	return $messages;
}
add_filter( 'post_updated_messages', 'wps_updated_activity_messages' );


/* =========================== META FIELDS CONTENT BOX WHEN EDITING =========================== */


add_action( 'add_meta_boxes', 'activity_info_box' );
function activity_info_box() {
    add_meta_box( 
        'activity_info_box',
        __( 'Activity Details', WPS2_TEXT_DOMAIN ),
        'activity_info_box_content',
        'wps_activity',
        'normal',
        'high'
    );
}

function activity_info_box_content( $post ) {
	global $wpdb;
	wp_nonce_field( 'activity_info_box_content', 'activity_info_box_content_nonce' );

	echo '<div style="margin-top:10px;font-weight:bold">'.__('Author', WPS2_TEXT_DOMAIN).'</div>';
	$author = get_user_by( 'id', $post->post_author );
	echo '<input type="text" id="wps_author" name="wps_author" placeholder="Select author..." value="'.$author->user_login.'" />';

	echo '<div style="margin-top:10px;font-weight:bold">'.__('Target(s)', WPS2_TEXT_DOMAIN).'</div>';
	$target_ids = get_post_meta( $post->ID, 'wps_target', true );
	$targets = array();
	if (is_array($target_ids)):
		foreach ($target_ids as $target):
			array_push($targets, $target);
			$member = get_user_by( 'id', $target );
			echo ($member) ? $member->user_login.'<br />' : '';
		endforeach;
	else:
		if (!get_post_meta( $post->ID, 'wps_target_type', true )): // Standard activity
			$member = get_user_by( 'id', $target_ids );
			$member_text = ($member) ? $member->user_login : '';
			echo '<input type="text" id="wps_target" name="wps_target" style="width:300px" placeholder="Select target user..." value="'.$member_text.'" />';
		else:
			echo $target_ids.' ('.get_post_meta( $post->ID, 'wps_target_type', true ).')';
		endif;
	endif;
    
	echo '<div style="margin-top:10px;font-weight:bold">'.__('Unhide', WPS2_TEXT_DOMAIN).'</div>';
    echo '<a id="wps_activity_unhide_all" rel="'.$post->ID.'" href="javascript:void(0);">'.__('Remove all hidden flags', WPS2_TEXT_DOMAIN).'</a>';

}

add_action( 'save_post', 'activity_info_box_save' );
function activity_info_box_save( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	return;

	if ( !isset($_POST['activity_info_box_content_nonce']) || !wp_verify_nonce( $_POST['activity_info_box_content_nonce'], 'activity_info_box_content' ) )
	return;

	if ( !current_user_can( 'edit_post', $post_id ) ) return;

	$target = get_user_by( 'login', $_POST['wps_target'] );
	if ($target) {
		update_post_meta( $post_id, 'wps_target', $target->ID );
	}

	$author = get_user_by( 'login', $_POST['wps_author'] );
	remove_action( 'save_post', 'activity_info_box_save' );
	$my_post = array(
	      'ID'         	=> $post_id,
	      'post_author' => $author->ID,
	);
	wp_update_post( $my_post );			
	add_action( 'save_post', 'activity_info_box_save' );	

}

/* =========================== COLUMNS WHEN VIEWING =========================== */

/* Columns for Posts list */
add_filter('manage_posts_columns', 'activity_columns_head');
add_action('manage_posts_custom_column', 'activity_columns_content', 10, 2);

// ADD NEW COLUMN
function activity_columns_head($defaults) {
    global $post;
	if ($post->post_type == 'wps_activity') {
		$defaults['activity_id'] = 'ID';
		$defaults['activity_post'] = 'Post';
		$defaults['col_author'] = 'Author';
		$defaults['col_target'] = 'Target';
		$defaults['col_image'] = 'Image';
    	unset($defaults['title']);
    }
    return $defaults;
}
 
// SHOW THE COLUMN CONTENT
function activity_columns_content($column_name, $post_ID) {
    if ($column_name == 'activity_id') {
    	echo $post_ID;
    }
    if ($column_name == 'activity_post') {
    	$post = get_post($post_ID);
    	echo '<a style="font-weight:bold" href="post.php?post='.$post_ID.'&action=edit">'.wp_trim_words($post->post_title, 30).'</a>';
    }
    if ($column_name == 'col_author') {
    	$post = get_post($post_ID);
    	$user = get_user_by ('id', $post->post_author );
    	echo $user->user_login.' ';
    	echo '('.$user->display_name.') &rarr;';
    }
    if ($column_name == 'col_target') {
    	$target_ids = get_post_meta( $post_ID, 'wps_target', true );
		if (!get_post_meta( $post_ID, 'wps_target_type', true )): // Standard activity
			if (is_array($target_ids)):
				foreach ($target_ids as $target):
					$member = get_user_by( 'id', $target );
					echo ($member) ? $member->user_login.' ('.$member->display_name.')<br />' : '';
				endforeach;
			else:
		    	$user = get_user_by ('id', $target_ids );
		    	echo $user->user_login.' ';
		    	echo '('.$user->display_name.')';			
			endif;
		else:
			echo $target_ids.' ('.get_post_meta( $post_ID, 'wps_target_type', true ).')';
		endif;
    }
    if ($column_name == 'col_image') {
		$image = @get_the_post_thumbnail($post_ID, array (30,30));
		if (is_string($image)) echo $image;
    }
}

/* =========================== ALTER VIEW POST LINKS =========================== */

function wps_change_activity_link( $permalink, $post ) {

	if ($post->post_type == 'wps_activity'):

		if ( wps_using_permalinks() ):	
			$u = get_user_by('id', $post->post_author);
			$parameters = sprintf('%s?view=%d', $u->user_login, $post->ID);
			$permalink = get_permalink(get_option('wpspro_profile_page'));
			$permalink = $permalink.$parameters;
		else:
			$parameters = sprintf('user_id=%d&view=%d', $post->post_author, $post->ID);
			$permalink = get_permalink(get_option('wpspro_profile_page'));
			$permalink = $permalink.'&'.$parameters;
		endif;

	endif;

    return $permalink;

}
add_filter('post_type_link',"wps_change_activity_link",10,2);

?>