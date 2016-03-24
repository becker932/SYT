<?php
																	/* ******** */
																	/*   AJAX   */
																	/* ******** */
if (is_admin()) add_action('init', 'wps_friendships_admin_init');
function wps_friendships_admin_init() {
	wp_enqueue_script('wps-friendship-js', plugins_url('wps_friends.js', __FILE__), array('jquery'));	
	wp_localize_script('wps-friendship-js', 'wps_friendships_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ));
}


add_action( 'wp_ajax_nopriv_wps_get_users', 'wps_get_users_ajax' ); // Logged out
add_action( 'wp_ajax_wps_get_users', 'wps_get_users_ajax' ); // Logged in 
function wps_get_users_ajax() {

	global $wpdb;
	$term = isset($_POST['term']) ? $_POST['term'] : '';
	$sql = "SELECT ID, user_login FROM ".$wpdb->base_prefix."users WHERE user_login like '%%%s%%' ORDER BY user_login";
	$rows = $wpdb->get_results($wpdb->prepare($sql, $term));

	$return_arr = array();
	foreach ($rows as $row) {
	    $row_array['value'] = $row->user_login;
	    $row_array['label'] = $row->user_login;
	    array_push($return_arr,$row_array);
	}
	echo json_encode($return_arr);	
	exit;

}

add_action( 'wp_ajax_wps_friends_add', 'wps_friends_add' ); // Logged in 
function wps_friends_add() {

	$user_id = $_POST['user_id'];
	global $current_user;

	if ($user_id != $current_user->ID):

		$friends = wps_are_friends($current_user->ID, $user_id);
		if (!$friends['status']):

			// Create post object
			$user = get_user_by('id', $user_id);
			$my_post = array(
		    	'post_title' 	=> $current_user->user_login.' - '.$user->user_login,
		    	'post_name'	=> sanitize_title_with_dashes($current_user->user_login.' '.$user->user_login),
		    	'post_type'	=> 'wps_friendship',
		    	'post_status'	=> 'pending'
			);

			// Insert the post into the database
			if ($post_id = wp_insert_post( $my_post )):

				// Update meta data
				update_post_meta( $post_id, 'wps_member1', $current_user->ID );
				update_post_meta( $post_id, 'wps_member2', $user_id );
				// Since date, as from pending (until accepted/rejected)
				update_post_meta( $post_id, 'wps_friendship_since', date('Y-m-d H:i:s') );

				// Add alert
				$subject = __('New friendship request', WPS2_TEXT_DOMAIN);
				$subject = get_bloginfo('name').': '.$subject;

				$content = '';

				$content = apply_filters( 'wps_alert_before', $content );
		
				$content .= '<h1>'.$user->display_name.'</h1>';

				$msg = sprintf(__('Friendship request from %s.', WPS2_TEXT_DOMAIN), $current_user->display_name);
				$content .= '<p>'.$msg.'</p>';

				$url = get_permalink(get_option('wpspro_profile_page'));

				$content .= '<p><a href="'.$url.'">'.$url.'</a></p>';

				$content = apply_filters( 'wps_alert_after', $content );

				wps_pro_insert_alert('friendship', $subject, $content, $current_user->ID, $user->ID, '', $url, $msg, 'pending');

				echo $post_id;

			else:

				echo false;

			endif;

		endif;

	endif;

}

// Reject friendship
add_action( 'wp_ajax_wps_friends_reject', 'wps_friends_reject' ); // Logged in 
function wps_friends_reject() {

	global $current_user;
	$post = get_post($_POST['post_id']);
	if ($post):
		$member1 = get_post_meta ($post->ID, 'wps_member1', true);
		$member2 = get_post_meta ($post->ID, 'wps_member2', true);

		if ($member1 == $current_user->ID || $member2 == $current_user->ID) {
			wp_delete_post( $_POST['post_id'], true );
		}
		echo 'ok';
	else:
		echo 'Post not found: '.$_POST['post_id'];
	endif;

}

// Accept friendship
add_action( 'wp_ajax_wps_friends_accept', 'wps_friends_accept' ); // Logged in 
function wps_friends_accept() {

	global $current_user;
	$post = get_post($_POST['post_id']);
	$member1 = get_post_meta ($post->ID, 'wps_member1', true);
	$member2 = get_post_meta ($post->ID, 'wps_member2', true);

	if ($member1 == $current_user->ID || $member2 == $current_user->ID):

		$my_post = array(
			'ID'           => $_POST['post_id'],
			'post_status' => 'publish',
		);

		wp_update_post( $my_post );

		// Add alert
		$subject = __('Friendship request accepted', WPS2_TEXT_DOMAIN);
		$subject = get_bloginfo('name').': '.$subject;

		$content = '';

		$content = apply_filters( 'wps_alert_before', $content );

		if ($member1 == $current_user->ID):
			$accepted = get_user_by('id', $member1);
			$sent = get_user_by('id', $member2);
		else:
			$sent = get_user_by('id', $member1);
			$accepted = get_user_by('id', $member2);
		endif;
		$content .= '<h1>'.$sent->display_name.'</h1>';

		$msg = sprintf(__('Friend request accepted by %s.', WPS2_TEXT_DOMAIN), $accepted->display_name);
		$content .= '<p>'.$msg.'</p>';

		$permalink = get_permalink(get_option('wpspro_profile_page'));
		if (get_option('wpspro_profile_permalinks')):
            $parameters = wps_query_mark($permalink).'user_id='.$accepted->ID;
        else:
            $parameters = sprintf('%s', $accepted->user_login);
        endif;
        
		$url = $permalink.$parameters;

		$content .= '<p><a href="'.$url.'">'.$url.'</a></p>';

		$content = apply_filters( 'wps_alert_after', $content );

		// Add alert
		wps_pro_insert_alert('friendship', $subject, $content, $accepted->ID, $sent->ID, '', $url, $msg, 'pending');

		// Any further actions?
		do_action( 'wps_friends_accept_hook', $sent->ID, $accepted->ID );

	endif;

}



																	/* ********* */
																	/* FUNCTIONS */
																	/* ********* */

function wps_friend_avatar($id, $avatar_size, $link) {

	if ($link):
		return '<a href="'.get_page_link(get_option('wpspro_profile_page')).'?user_id='.$id.'">'.user_avatar_get_avatar( $id, $avatar_size ).'</a>';
	else:
		return user_avatar_get_avatar( $id, $avatar_size );
	endif;

}

function wps_get_friends($user_id) {

    $friends = array();
    
    if (!get_option('wps_friendships_all')):
        $args = array (
            'post_type'              => 'wps_friendship',
            'post_status'			 => array( 'publish' ),
            'posts_per_page'         => '1000',
            'meta_query'             => array(
                'relation'		 => 'OR',
                array(
                    'key'       => 'wps_member1',
                    'compare'   => '=',
                    'value'     => $user_id,
                ),
                array(
                    'key'       => 'wps_member2',
                    'compare'   => '=',
                    'value'     => $user_id,
                ),
            ),
        );
        $loop = new WP_Query( $args );
        global $post;
        if ($loop->have_posts()) {
            while ( $loop->have_posts() ) : $loop->the_post();
                $member1 = get_post_meta( $post->ID, 'wps_member1', true );
                $member2 = get_post_meta( $post->ID, 'wps_member2', true );
                $other_member = ($member1 == $user_id) ? $member2 : $member1;
                array_push($friends, array('ID' => $other_member));
            endwhile;
        }
        wp_reset_query();
    else:
        global $wpdb;
        $sql = "SELECT ID FROM ".$wpdb->prefix."users WHERE ID != %d";
        $loop = $wpdb->get_results($wpdb->prepare($sql, $user_id));
        if ($loop) {
            foreach ($loop as $u):
                array_push($friends, array('ID' => $u->ID));
            endforeach;
        }
    endif;

	return $friends;

}

function wps_are_friends($user_id, $user_id_to_check) {

    if (!get_option('wps_friendships_all')):
    
        if ($user_id == $user_id_to_check):

            return array("ID"=>0, "status"=>'publish');

        else:

            global $wpdb;

            $sql = "SELECT p.ID, p.post_status, m1.meta_value as wps_member1, m2.meta_value as wps_member2 FROM ".$wpdb->prefix."posts p 
            LEFT JOIN ".$wpdb->prefix."postmeta m1 ON m1.post_id = p.ID
            LEFT JOIN ".$wpdb->prefix."postmeta m2 ON m2.post_id = p.ID
            WHERE p.post_type = 'wps_friendship'
              AND (p.post_status = 'pending' OR p.post_status = 'publish')
              AND (m1.meta_key = 'wps_member1' AND m2.meta_key = 'wps_member2')
              AND (m1.meta_value = %d OR m2.meta_value = %d)";

            $friendships = $wpdb->get_results($wpdb->prepare($sql, $user_id, $user_id));

            if ($friendships):
                foreach ($friendships as $friendship):
                    if (
                        ($friendship->wps_member1 == $user_id && $friendship->wps_member2 == $user_id_to_check) ||
                        ($friendship->wps_member2 == $user_id && $friendship->wps_member1 == $user_id_to_check)
                        ):
                        $direction = ($friendship->wps_member1 == $user_id) ? 'to' : 'from';
                        return array("ID"=>$friendship->ID, "status"=>$friendship->post_status, "direction"=>$direction);
                        break;
                    endif;				
                endforeach;
            endif;

            return array("ID"=>0, "status"=>false, "direction"=>false);

        endif;
    
    else:
    
        return array("ID"=>0, "status"=>"publish", "direction"=>"to");
    
    endif;

}


?>