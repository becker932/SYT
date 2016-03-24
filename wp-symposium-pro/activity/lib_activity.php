<?php
while(!is_file('wp-config.php')){
	if(is_dir('../')) chdir('../');
	else die('Could not find WordPress config file.');
}
include_once( 'wp-config.php' );

$action = isset($_POST['action']) ? $_POST['action'] : false;

if ($action) {

	global $current_user;
	get_currentuserinfo();

	if ( is_user_logged_in() ) {

		/* ADD POST */
		if ($action == 'wps_activity_post_add') {

			$post = array(
			  'post_title'     => $_POST['wps_activity_post'],
			  'post_status'    => 'publish',
			  'post_type'      => 'wps_activity',
			  'post_author'    => $_POST['wps_activity_post_author'],
			  'ping_status'    => 'closed',
			  'comment_status' => 'open',
			);  
			$new_id = wp_insert_post( $post );

			if ($new_id):

				update_post_meta( $new_id, 'wps_target', $_POST['wps_activity_post_target'] );
            
				// Any further actions?
				do_action( 'wps_activity_post_add_hook', $_POST, $_FILES, $new_id );

			endif;

		}

	}

}


?>
