<?php

/* Create Friendships custom post type */


/* =========================== LABELS FOR ADMIN =========================== */


function wps_custom_post_friendship() {
	$labels = array(
		'name'               => __( 'Friendships', WPS2_TEXT_DOMAIN ),
		'singular_name'      => __( 'Friendship', WPS2_TEXT_DOMAIN ),
		'add_new'            => __( 'Add New', WPS2_TEXT_DOMAIN ),
		'add_new_item'       => __( 'Add New Friendship', WPS2_TEXT_DOMAIN ),
		'edit_item'          => __( 'Edit Friendship', WPS2_TEXT_DOMAIN ),
		'new_item'           => __( 'New Friendship', WPS2_TEXT_DOMAIN ),
		'all_items'          => __( 'Friendships', WPS2_TEXT_DOMAIN ),
		'view_item'          => __( 'View Friendship', WPS2_TEXT_DOMAIN ),
		'search_items'       => __( 'Search Friendships', WPS2_TEXT_DOMAIN ),
		'not_found'          => __( 'No friendships found', WPS2_TEXT_DOMAIN ),
		'not_found_in_trash' => __( 'No friendships found in the Trash', WPS2_TEXT_DOMAIN ), 
		'parent_item_colon'  => '',
		'menu_name'          => __( 'Friendships', WPS2_TEXT_DOMAIN ),
	);
	$args = array(
		'labels'        		=> $labels,
		'description'   		=> 'Holds our friendships specific data',
		'public'        		=> true,
		'exclude_from_search' 	=> true,
		'rewrite'				=> false,
		'show_in_menu' 			=> 'wps_pro',
		'supports'      		=> array( 'title' ),
		'has_archive'   		=> false,
	);
	register_post_type( 'wps_friendship', $args );
}
add_action( 'init', 'wps_custom_post_friendship' );

/* =========================== MESSAGES FOR ADMIN =========================== */

function wps_updated_friendship_messages( $messages ) {
	global $post, $post_ID;
	$messages['wps_friendship'] = array(
		0 => '', 
		1 => __('Friendship updated.'),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('Friendship updated.'),
		5 => isset($_GET['revision']) ? sprintf( __('Friendship restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => __('Friendships published.'),
		7 => __('Friendship saved.'),
		8 => __('Friendship submitted.'),
		9 => sprintf( __('Friendship scheduled for: <strong>%1$s</strong>.'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) ),
		10 => __('Friendship draft updated.'),
	);
	return $messages;
}
add_filter( 'post_updated_messages', 'wps_updated_friendship_messages' );


/* =========================== META FIELDS CONTENT BOX WHEN EDITING =========================== */

add_action( 'add_meta_boxes', 'friendship_info_box' );
function friendship_info_box() {
    add_meta_box( 
        'friendship_info_box',
        __( 'wps_friendship', WPS2_TEXT_DOMAIN ),
        'friendship_info_box_content',
        'wps_friendship',
        'normal',
        'high'
    );
}

function friendship_info_box_content( $post ) {
	global $wpdb;
	wp_nonce_field( 'friendship_info_box_content', 'friendship_info_box_content_nonce' );

	echo '<div style="margin-top:10px;font-weight:bold">'.__('User 1', WPS2_TEXT_DOMAIN).'</div>';
	$member = get_user_by( 'id', get_post_meta( $post->ID, 'wps_member1', true ) );
	$member_text = ($member) ? $member->user_login : '';
	echo '<input type="text" id="wps_member1" style="width:300px" name="wps_member1" placeholder="'.__('Select first user...', WPS2_TEXT_DOMAIN).'" value="'.$member_text.'" />';

	echo '<div style="margin-top:10px;font-style:italic;">'.__('is friends with...', WPS2_TEXT_DOMAIN).'</div>';

	echo '<div style="margin-top:10px;font-weight:bold">'.__('User 2', WPS2_TEXT_DOMAIN).'</div>';
	$member = get_user_by( 'id', get_post_meta( $post->ID, 'wps_member2', true ) );
	$member_text = ($member) ? $member->user_login : '';
	echo '<input type="text" id="wps_member2" style="width:300px" name="wps_member2" placeholder="'.__('Select second user...', WPS2_TEXT_DOMAIN).'" value="'.$member_text.'" />';

}

add_action( 'save_post', 'friendship_info_box_save' );
function friendship_info_box_save( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	return;

	if ( !isset($_POST['friendship_info_box_content_nonce']) || !wp_verify_nonce( $_POST['friendship_info_box_content_nonce'], 'friendship_info_box_content' ) )
	return;

	if ( !current_user_can( 'edit_post', $post_id ) ) return;

	$member1 = get_user_by( 'login', $_POST['wps_member1'] );
	$member2 = get_user_by( 'login', $_POST['wps_member2'] );

	if ($member1 && $member2) {

		global $wpdb;

		$status = wps_are_friends($member1->ID, $member2->ID);
		if (!$status['status']) {

			update_post_meta( $post_id, 'wps_member1', $member1->ID );
			update_post_meta( $post_id, 'wps_member2', $member2->ID );
			update_post_meta( $post_id, 'wps_friendship_since', date('Y-m-d H:i:s') );

			remove_action( 'save_post', 'friendship_info_box_save' );
			$my_post = array(
			      'ID'         	=> $post_id,
			      'post_title' 	=> $member1->user_login.' - '.$member2->user_login,
			      'post_name'	=> sanitize_title_with_dashes($member1->user_login.' '.$member2->user_login),
			      'post_type'	=> 'wps_friendship',
			      'post_status'	=> 'publish'
			);
			wp_update_post( $my_post );			
			add_action( 'save_post', 'friendship_info_box_save' );

		} else {

			// Already exists, delete newly created friendship
			wp_delete_post( $post_id, true );
			die(__('Friendship already exists.', WPS2_TEXT_DOMAIN));

		}

	}

}

/* =========================== COLUMNS WHEN VIEWING =========================== */

/* Columns for Posts list */
add_filter('manage_posts_columns', 'friendship_columns_head');
add_action('manage_posts_custom_column', 'friendship_columns_content', 10, 2);

// ADD NEW COLUMN
function friendship_columns_head($defaults) {
    global $post;
	if ($post->post_type == 'wps_friendship') {
		$defaults['col_friendship_member1'] = 'User 1 display name';
    	$defaults['col_friendship_member2'] = 'User 2 display name';
    	$defaults['col_friendship_status'] = 'Status';
    	$defaults['wps_friendship_since'] = 'Friends since';
    	unset($defaults['date']);
    }
    return $defaults;
}
 
// SHOW THE COLUMN CONTENT
function friendship_columns_content($column_name, $post_ID) {
    if ($column_name == 'col_friendship_member1') {
    	$post = get_post($post_ID); 
    	$user = get_user_by('id', $post->wps_member1);
    	echo $user->display_name;
    }
    if ($column_name == 'col_friendship_member2') {
    	$post = get_post($post_ID); 
    	$user = get_user_by('id', $post->wps_member2);
    	echo $user->display_name;
    }
    if ($column_name == 'col_friendship_status') {
    	$post = get_post($post_ID); 
    	if ($post->post_status == 'publish'):
    		echo __('Friends', WPS2_TEXT_DOMAIN);
    	else:
    		echo __('Pending', WPS2_TEXT_DOMAIN);
    	endif;
    }
    if ($column_name == 'wps_friendship_since') {
    	$post = get_post($post_ID); 
    	echo date("F j, Y h:m:s a", strtotime($post->wps_friendship_since));
    }
}




?>