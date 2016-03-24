<?php
// AJAX functions for activity
add_action( 'wp_ajax_wps_forum_closed_switch', 'wps_forum_closed_switch' ); 
add_action( 'wp_ajax_wps_forum_comment_reopen', 'wps_forum_comment_reopen' ); 
add_action( 'wp_ajax_wps_forum_add_subcomment', 'wps_forum_add_subcomment' ); 

/* SAVE COMMENT (TO REPLY) */
function wps_forum_add_subcomment() {

	global $current_user;

	$the_comment = $_POST;

	$data = array(
	    'comment_post_ID' => $the_comment['post_id'],
	    'comment_content' => $the_comment['comment'],
	    'comment_type' => '',
	    'comment_parent' => $the_comment['comment_id'],
	    'comment_author' => $current_user->user_login,
	    'comment_author_email' => $current_user->user_email,
	    'user_id' => $current_user->ID,
	    'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
	    'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
	    'comment_approved' => 1,
	);

	$new_id = wp_insert_comment($data);

	if ($new_id):

        // Check if parent is private, and copy if so
        $private = get_comment_meta( $the_comment['comment_id'], 'wps_private_post', true );
        if ($private)
            update_comment_meta($new_id, 'wps_private_post', true);
    
        // Reset read flags (apart from current user)
		$read = array();
		$read[] = $current_user->user_login;
		update_post_meta ( $the_comment['post_id'], 'wps_forum_read', $read);

		// Any further actions?
		do_action( 'wps_forum_comment_add_hook', $the_comment, $_FILES, $the_comment['post_id'], $new_id );

		// HTML to show
		$sub_comment_html = '<div class="wps_forum_post_subcomment" style="padding-left: '.$the_comment['size'].'px;">';

			$sub_comment_html .= '<div class="wps_forum_post_comment_author" style="max-width: '.$the_comment['size'].'px; margin-left: -'.$the_comment['size'].'px;">';
				$sub_comment_html .= '<div class="wps_forum_post_comment_author_avatar">';
					$sub_comment_html .= user_avatar_get_avatar( $current_user->ID, $the_comment['size'] );
				$sub_comment_html .= '</div>';
			$sub_comment_html .= '</div>';

			$sub_comment_html .= '<div class="wps_forum_post_comment_content">';

				$sub_comment_content = wps_strip_tags($the_comment['comment']);
				$sub_comment_content = wps_bbcode_replace(convert_smilies(wps_make_clickable(wpautop($sub_comment_content))));

				$sub_comment_author = '<div class="wps_forum_post_comment_author_display_name">';
					$sub_comment_author .= wps_display_name(array('user_id'=>$current_user->ID, 'link'=>1));
				$sub_comment_author .= '</div>';

				$sub_comment_html .= $sub_comment_author . $sub_comment_content;

			$sub_comment_html .= '</div>';

		$sub_comment_html .= '</div>';

		echo $sub_comment_html;

	else:

		echo 0;

	endif;

	exit;
}

/* REOPEN COMMENT */
function wps_forum_comment_reopen() {

	global $current_user;
	$the_post = $_POST;

	$my_post = array(
	      'ID'           	=> $the_post['post_id'],
	      'comment_status' 	=> 'open',
	);
	wp_update_post( $my_post );

	// Add re-opened flag/datetime
	update_post_meta($the_post['post_id'], 'wps_reopened_date', date('Y-m-d H:i:s'));

	// Any further actions?
	do_action( 'wps_forum_post_reopen_hook', $the_post, $_FILES, $the_post['post_id'] );

}

/* SAVE CLOSED SWITCH STATE FOR USER */
function wps_forum_closed_switch() {

	global $current_user;
	if (is_user_logged_in()) update_user_meta($current_user->ID, 'forum_closed_switch', $_POST['state']);

}

?>
