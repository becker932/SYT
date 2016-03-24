<?php

																	/* **** */
																	/* INIT */
																	/* **** */

function wps_friends_init() {
	wp_enqueue_script('wps-friendship-js', plugins_url('wps_friends.js', __FILE__), array('jquery'));	
	wp_localize_script('wps-friendship-js', 'wps_friendships_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ));
	wp_enqueue_style('wps-friends', plugins_url('wps_friends.css', __FILE__), 'css');
	// Anything else?
	do_action('wps_friends_init_hook');
}
																	/* ********** */
																	/* SHORTCODES */
																	/* ********** */
function wps_friends_status($atts) {

	// Init
	add_action('wp_footer', 'wps_friends_init');

	$html = '';
	global $current_user;

	if (is_user_logged_in()):

		// Shortcode parameters
		extract( shortcode_atts( array(
			'user_id' => '',
			'friends_yes' => __('You are friends', WPS2_TEXT_DOMAIN),
			'friends_pending' => __('You have requested to be friends', WPS2_TEXT_DOMAIN),
			'friend_request' => __('You have a friends request', WPS2_TEXT_DOMAIN),
			'friends_no' => __('You are not friends', WPS2_TEXT_DOMAIN),
			'before' => '',
			'after' => '',
		), $atts, 'wps_friends_status' ) );

		if (!$user_id) $user_id = wps_get_user_id();

		if ($user_id != $current_user->ID):

			$friends = wps_are_friends($current_user->ID, $user_id);

			if ($friends['status']):
				if ($friends['status'] == 'publish'):
					$html .= $friends_yes;
				else:
					if ($friends['direction'] == 'to'):
						$html .= $friends_pending;
					else:
						$html .= $friend_request;
					endif;
				endif;
			else:
				$html .= $friends_no;
			endif;

		endif;

	endif;

	if ($html) $html = htmlspecialchars_decode($before).$html.htmlspecialchars_decode($after);

	return $html;

}

function wps_friends_add_button($atts) {

	// Init
	add_action('wp_footer', 'wps_friends_init');

	$html = '';
	global $current_user;

	if (is_user_logged_in() && !get_option('wps_friendships_all')):

		// Shortcode parameters
		extract( shortcode_atts( array(
			'user_id' => 0,
			'label' => __('Make friends', WPS2_TEXT_DOMAIN),
			'cancel_label' => __('Cancel friendship', WPS2_TEXT_DOMAIN),
			'cancel_request_label' => __('Cancel friendship request', WPS2_TEXT_DOMAIN),
			'class' => '',
			'before' => '',
			'after' => '',
		), $atts, 'wps_friends_add' ) );

		if (!$user_id) $user_id = wps_get_user_id();

		if ($user_id != $current_user->ID):

			$html .= '<div class="wps_friends_add_button">';

				$html .= '<input type="hidden" id="plugins_url" value="'.plugins_url( '', __FILE__ ).'" />';

				$friends = wps_are_friends($current_user->ID, $user_id);
				if (!$friends['status']):

					$html .= '<input type="submit" rel="'.$user_id.'" class="wps_submit wps_friends_add '.$class.'" value="'.$label.'" />';

				else:

					if ($friends['status'] == 'publish'):
						$html .= '<input type="submit" rel="'.$friends['ID'].'" class="wps_submit wps_friends_cancel '.$class.'" value="'.$cancel_label.'" />';
					else:
						$html .= '<input type="submit" rel="'.$friends['ID'].'" class="wps_submit wps_friends_reject '.$class.'" value="'.$cancel_request_label.'" />';
					endif;

				endif;

			$html .= '</div>';

		endif;


	endif;

	if ($html) $html = htmlspecialchars_decode($before).$html.htmlspecialchars_decode($after);

	return $html;

}

function wps_friends($atts) {

	// Init
	add_action('wp_footer', 'wps_friends_init');

	$html = '';
	global $current_user;

	// Shortcode parameters
	extract( shortcode_atts( array(
		'user_id' => false,
		'count' => 10,
		'size' => 64,
		'link' => true,
		'show_last_active' => 1,
		'last_active_text' => __('Last seen:', WPS2_TEXT_DOMAIN),
		'last_active_format' => '%s ago',
		'private' => __('Private information', WPS2_TEXT_DOMAIN),
		'none' => __('No friends', WPS2_TEXT_DOMAIN),
		'layout' => 'list', // list|fluid
		'before' => '',
		'after' => '',
	), $atts, 'wps_friends' ) );

	if (!$user_id)
		$user_id = wps_get_user_id();

	$friends = wps_are_friends($current_user->ID, $user_id);
	// By default same user, and friends of user, can see profile
	$user_can_see_friends = ($current_user->ID == $user_id || $friends['status'] == 'publish') ? true : false;
	$user_can_see_friends = apply_filters( 'wps_check_friends_security_filter', $user_can_see_friends, $user_id, $current_user->ID );

	if ($user_can_see_friends):

		global $wpdb;
        if (!get_option('wps_friendships_all')):
            $sql = "SELECT p.ID, m1.meta_value as wps_member1, m2.meta_value as wps_member2
                FROM ".$wpdb->prefix."posts p 
                LEFT JOIN ".$wpdb->prefix."postmeta m1 ON p.ID = m1.post_id
                LEFT JOIN ".$wpdb->prefix."postmeta m2 ON p.ID = m2.post_id
                WHERE p.post_type='wps_friendship'
                  AND p.post_status='publish'
                  AND m1.meta_key = 'wps_member1'
                  AND m2.meta_key = 'wps_member2'
                  AND (m1.meta_value = %d OR m2.meta_value = %d)";
            $get_friends = $wpdb->get_results($wpdb->prepare($sql, $user_id, $user_id));
        else:
            $sql = "SELECT ID, ID as wps_member1, %d as wps_member2 
                    FROM ".$wpdb->prefix."users WHERE ID != %d";
            $get_friends = $wpdb->get_results($wpdb->prepare($sql, $user_id, $user_id));
        endif;
            
		if ($get_friends):

			// Put into array so they can be sorted
			$friends = array();
			foreach ($get_friends as $friend):
				$row_array = array();
			    $other_member = $friend->wps_member1 == $user_id ? $friend->wps_member2 : $friend->wps_member1;
                if (!wps_is_account_closed($other_member)):
                    $row_array['friend_id'] = $other_member;
                    $row_array['last_active'] = strtotime(get_user_meta($other_member, 'wpspro_last_active', true));
                    array_push($friends,$row_array);
                endif;
			endforeach;

			// Sort friends by when last active
			$sort = array();
			$order = 'last_active';
			$orderby = 'DESC';
			foreach($friends as $k=>$v) {
			    $sort[$order][$k] = $v[$order];
			}
			$orderby = $orderby == "ASC" ? SORT_ASC : SORT_DESC;
			array_multisort($sort[$order], $orderby, $friends);

			// Show $count number of friends
			$c=0;
			foreach ($friends as $friend):

				$the_friend = get_user_by('id', $friend['friend_id']);
				if ($the_friend):

					// Get profile_security of the_friend
					$user_can_see_friend = true;
					$user_can_see_friend = apply_filters( 'wps_check_friends_security_filter', $user_can_see_friend, $friend['friend_id'], $current_user->ID );

					if ($user_can_see_friend):

						$html .= '<div id="wps_friends"';
							if ($layout == 'fluid') $html .= ' style="min-width: 235px; float:left;"';
							$html .= '>';
					
							$html .= '<div class="wps_friends_friend" style="position:relative;padding-left: '.($size+10).'px">';
							if ($size):
								$html .= '<div class="wps_friends_friend_avatar" style="margin-left: -'.($size+10).'px">';
									$html .= wps_friend_avatar($friend['friend_id'], $size, $link);
								$html .= '</div>';
							endif;
							$html .= '<div class="wps_friends_friend_avatar_display_name">';
								$html .= wps_display_name(array('user_id'=>$friend['friend_id'], 'link'=>$link));
							$html .= '</div>';
							if ($show_last_active && $friend['last_active']):
								$html .= '<div class="wps_friends_friend_avatar_last_active">';
									$html .= $last_active_text.' ';
									$html .= sprintf($last_active_format, human_time_diff($friend['last_active'], current_time('timestamp', 1)), WPS2_TEXT_DOMAIN);
								$html .= '</div>';
							endif;
							$html .= '</div>';

						$html .= '</div>';

					endif;

				endif;

				$c++;
				if ($c == $count) break;		
			endforeach;
		else:
			$html .= $none;
		endif;

	else:

		$html .= '<div id="wps_friends_private_msg">'.$private.'</div>';

	endif;

	if ($html) $html = htmlspecialchars_decode($before).$html.htmlspecialchars_decode($after);

	return $html;
}

function wps_friends_pending($atts) {

	// Init
	add_action('wp_footer', 'wps_friends_init');

	$html = '';
	global $current_user;

	// Shortcode parameters
	extract( shortcode_atts( array(
        'user_id' => false,
		'count' => 10,
		'size' => 64,
		'link' => true,
		'class' => '',
		'accept_request_label' => __('Accept', WPS2_TEXT_DOMAIN),
		'reject_request_label' => __('Reject', WPS2_TEXT_DOMAIN),
		'none' => '',
		'before' => '',
		'after' => '',
	), $atts, 'wps_friends' ) );

    if (!$user_id) $user_id = wps_get_user_id();

	if (isset($_POST['wps_friends_pending'])):

		if ($_POST['wps_friends_pending'] == 'reject'):

			$post = get_post ($_POST['wps_friends_post_id']);
			if ($post):
				$member1 = get_post_meta($post->ID, 'wps_member1', true);
				$member2 = get_post_meta($post->ID, 'wps_member2', true);
				if ($member1 == $current_user->ID || $member2 == $current_user->ID)
					wp_delete_post( $post->ID, true );
			endif;

		endif;		

	endif;

	if ($current_user->ID == $user_id):

		$args = array (
			'post_type'              => 'wps_friendship',
			'posts_per_page'         => $count,
			'post_status'			 => 'pending',
			'meta_query' => array(
				array(
					'key'       => 'wps_member2', // recipient of request is second user meta field
					'compare'   => '=',
					'value'     => $user_id,
				),
			),		
		);


		global $post;
		$loop = new WP_Query( $args );
		if ($loop->have_posts()) {
			$html .= '<div id="wps_pending_friends">';
			while ( $loop->have_posts() ) : $loop->the_post();
				$member1 = get_post_meta( $post->ID, 'wps_member1', true );
                
                $html .= '<div class="wps_pending_friends_friend">';
                    if ($size):
                        $html .= '<div class="wps_pending_friends_friend_avatar">';
                            $html .= wps_friend_avatar($member1, $size, $link);
                        $html .= '</div>';
                    endif;
                    $html .= '<div class="wps_pending_friends_friend_display_name">';
                        $html .= wps_display_name(array('user_id'=>$member1, 'link'=>$link));
                    $html .= '</div>';
                    $html .= '<div class="wps_pending_friends_accept_reject">';
                    $html .= '<input type="submit" rel="'.$post->ID.'" class="wps_submit wps_friends_accept '.$class.'" value="'.$accept_request_label.'" />';
                    $html .= '<input type="submit" rel="'.$post->ID.'" class="wps_submit wps_friends_reject '.$class.'" value="'.$reject_request_label.'" />';
                    $html .= '<input type="hidden" id="plugins_url" value="'.plugins_url( '', __FILE__ ).'" />';
                    $html .= '</div>';
                $html .= '</div>';

			endwhile; 
			$html .= '</div>';		
		} else {
			$html .= $none;
		}
		wp_reset_query();

		if ($html) $html = htmlspecialchars_decode($before).$html.htmlspecialchars_decode($after);

	endif;

	return $html;

}

function wps_alerts_friends($atts) {

    // Init
    add_action('wp_footer', 'wps_friends_init');

    $html = '';
    global $current_user;

    if (is_user_logged_in()) {	
        
        // Shortcode parameters
        extract( shortcode_atts( array(
            'flag_size' => 24,
            'flag_pending_size' => 10,
            'flag_pending_top' => 6,
            'flag_pending_left' => 8,
            'flag_pending_radius' => 8,
            'flag_url' => false,
            'flag_src' => false,
            'before' => '',
            'after' => '',
        ), $atts, 'wps_alerts_friends' ) );

        $args = array (
            'post_type'              => 'wps_friendship',
            'posts_per_page'         => -1,
            'post_status'			 => 'pending',
            'meta_query' => array(
                array(
                    'key'       => 'wps_member2', // recipient of request is second user meta field
                    'compare'   => '=',
                    'value'     => $current_user->ID
                ),
            ),		
        );


        global $post;
        $loop = new WP_Query( $args );
        $unread_count = $loop->found_posts;

        wp_reset_query();

        $html .= '<div id="wps_alerts_friends_flag" style="width:'.$flag_size.'px; height:'.$flag_size.'px;" >';
        $html .= '<a href="'.$flag_url.'">';
        $src = (!$flag_src) ? plugins_url('images/friends'.get_option('wpspro_flag_colors').'.png', __FILE__) : $flag_src;
        $html .= '<img style="width:'.$flag_size.'px; height:'.$flag_size.'px;" src="'.$src.'" />';
        if ($unread_count):
            $html .= '<div id="wps_alerts_friends_flag_unread" style="position: absolute; padding-top: '.($flag_pending_size*0.2).'px; line-height:'.($flag_pending_size*0.8).'px; font-size:'.($flag_pending_size*0.8).'px; border-radius: '.$flag_pending_radius.'px; top:'.$flag_pending_top.'px; left:'.$flag_pending_left.'px; width:'.$flag_pending_size.'px; height:'.$flag_pending_size.'px;">'.$unread_count.'</div>';
        endif;
        $html .= '</a></div>';
        if (!$flag_url) $html .= '<div class="wps_error">'.__('Set flag_url in shortcode', WPS2_TEXT_DOMAIN).'</div>';
        
        if ($html) $html = htmlspecialchars_decode($before).$html.htmlspecialchars_decode($after);

    }

    return $html;
    
}

if (!is_admin()) add_shortcode(WPS_PREFIX.'-friends', 'wps_friends');
if (!is_admin()) add_shortcode(WPS_PREFIX.'-friends-status', 'wps_friends_status');
if (!is_admin()) add_shortcode(WPS_PREFIX.'-friends-pending', 'wps_friends_pending');
if (!is_admin()) add_shortcode(WPS_PREFIX.'-friends-add-button', 'wps_friends_add_button');
if (!is_admin()) add_shortcode(WPS_PREFIX.'-alerts-friends', 'wps_alerts_friends');


?>