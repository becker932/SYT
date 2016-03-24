<?php
// Hook into core get users AJAX function
add_action( 'wp_ajax_wps_get_users', 'wps_get_users_ajax' ); 

// AJAX functions for activity
add_action( 'wp_ajax_wps_activity_comment_add', 'wps_activity_comment_add' ); 
add_action( 'wp_ajax_wps_activity_settings_delete', 'wps_activity_settings_delete' ); 
add_action( 'wp_ajax_wps_activity_settings_sticky', 'wps_activity_settings_sticky' ); 
add_action( 'wp_ajax_wps_activity_settings_unsticky', 'wps_activity_settings_unsticky' ); 
add_action( 'wp_ajax_wps_comment_settings_delete', 'wps_comment_settings_delete' ); 
add_action( 'wp_ajax_wps_activity_unhide_all', 'wps_activity_unhide_all' ); 
add_action( 'wp_ajax_wps_activity_settings_hide', 'wps_activity_settings_hide' ); 

/* ADMIN - UNHIDE ALL POSTS */
function wps_activity_unhide_all() {
    
    global $wpdb;
    $post_id = $_POST['post_id'];
    $sql = "delete from ".$wpdb->prefix."postmeta where meta_key = 'wps_activity_hidden' and post_id = %d";
    $wpdb->query($wpdb->prepare($sql, $post_id));
    echo $post_id;
    exit;
    
}

/* HIDE POST */
function wps_activity_settings_hide() {

    global $current_user;
    
    $hidden = get_post_meta ($_POST['post_id'], 'wps_activity_hidden', true);
    if (!$hidden) $hidden = array();
    array_push($hidden, $current_user->ID);
    
	update_post_meta( $_POST['post_id'], 'wps_activity_hidden', $hidden );
    
	echo $_POST['post_id'];
    exit;

}

/* MAKE POST STICKY */
function wps_activity_settings_sticky() {

	if (update_post_meta( $_POST['post_id'], 'wps_sticky', true )) {
		echo $_POST['post_id'];
	} else {
		echo 0;
	}

}

/* MAKE POST UNSTICKY */
function wps_activity_settings_unsticky() {

	if (delete_post_meta( $_POST['post_id'], 'wps_sticky' )) {
		echo $_POST['post_id'];
	} else {
		echo 0;
	}

}

/* ADD COMMENT */
function wps_activity_comment_add() {

	global $current_user;
	$data = array(
	    'comment_post_ID' => $_POST['post_id'],
	    'comment_content' => $_POST['comment_content'],
	    'comment_type' => '',
	    'comment_parent' => 0,
	    'comment_author' => $current_user->user_login,
	    'user_id' => $current_user->ID,
	    'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
	    'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
	    'comment_approved' => 1,
	);

	$new_id = wp_insert_comment($data);

	if ($new_id):

		// Any further actions?
		do_action( 'wps_activity_comment_add_hook', $_POST, $new_id );

        $item_html = '<div class="wps_activity_comment" style="position:relative;padding-left: '.($_POST['size']+10).'px">';

            // Avatar
            $item_html .= '<div class="wps_activity_post_comment_avatar" style="float:left; margin-left: -'.($_POST['size']+10).'px">';
                $item_html .= user_avatar_get_avatar($current_user->ID, $_POST['size']);
            $item_html .= '</div>';

            // Name and date
            $item_html .= wps_display_name(array('user_id'=>$current_user->ID, 'link'=>$_POST['link']));
            $item_html .= '<br />';

            // The Comment
            $item_html .= wps_bbcode_replace(convert_smilies(wps_make_clickable(wpautop(esc_html($_POST['comment_content'])))));

        $item_html .= '</div>';

		echo $item_html;
		exit;
		
	else:
		echo 0;
	endif;

}

/* DELETE POST */
function wps_activity_settings_delete() {

	$id = $_POST['id'];
	if ($id):
		global $current_user;
		$post = get_post($id);
		if ($post->post_author == $current_user->ID || current_user_can('manage_options')):
			if (wp_delete_post($id, true)):
				echo 'success';
			else:
				echo 'failed to delete post '.$id;
			endif;
		else:
			echo 'not owner';
		endif;
	endif;

}

/* DELETE COMMENT */
function wps_comment_settings_delete() {

	$id = $_POST['id'];
	if ($id):
		global $current_user;
		$comment = get_comment($id);
		if ($comment->user_id == $current_user->ID || current_user_can('manage_options')):
			if (wp_delete_comment($id, true)):
				echo 'success';
			else:
				echo 'failed to delete comment '.$id;
			endif;
		else:
			echo 'not owner';
		endif;
	endif;

}

?>
