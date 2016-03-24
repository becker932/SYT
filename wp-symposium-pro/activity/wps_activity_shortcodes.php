<?php

																	/* **** */
																	/* INIT */
																	/* **** */

function wps_activity_init() {
	// JS and CSS
	wp_enqueue_script('wps-activity-js', plugins_url('wps_activity.js', __FILE__), array('jquery'));	
	wp_localize_script( 'wps-activity-js', 'wps_activity_ajax', array( 
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'plugins_url' => plugins_url( '', __FILE__ )
	));		
	wp_enqueue_style('wps-activity-css', plugins_url('wps_activity.css', __FILE__), 'css');	
	// Select2 replacement drop-down list from core (ready for dependenent plugins like who-to that only uses hooks/filters)
	wp_enqueue_script('wps-select2-js', plugins_url('../js/select2.min.js', __FILE__), array('jquery'));	
	wp_enqueue_style('wps-select2-css', plugins_url('../js/select2.css', __FILE__), 'css');
	// Anything else?
	do_action('wps_activity_init_hook');
}


																	/* ********** */
																	/* SHORTCODES */
																	/* ********** */

function wps_activity_page($atts){

	// Init
	add_action('wp_footer', 'wps_activity_init');

    global $current_user;
	$html = '';

	// Shortcode parameters
	extract( shortcode_atts( array(
		'user_id' => false,
        'mimic_user_id' => false,
		'user_avatar_size' => '150',
		'map_style' => 'static',
		'map_size' => '150,150',
		'map_zoom' => '4',
		'town_label' => __('Town/City', WPS2_TEXT_DOMAIN),
		'country_label' => __('Country', WPS2_TEXT_DOMAIN),
	), $atts, 'wps_activity_page' ) );	

	if (!$user_id):
        $user_id = wps_get_user_id();
        $this_user = $current_user->ID;
    else:
        if ($mimic_user_id):
            $this_user = $user_id;
        else:
            $this_user = $current_user->ID;
        endif;
    endif;

	$html .= '<style>.wps_avatar img { border-radius:0px; }</style>';
	$html .= wps_display_name(array('user_id'=>$user_id, 'before'=>'<div id="wps_display_name" style="font-size:2.5em; margin-bottom:20px;">', 'after'=>'</div>'));
	$html .= '<div style="overflow:auto;overflow-y:hidden;margin-bottom:15px">';
	$html .= wps_avatar(array('user_id'=>$user_id, 'change_link'=>1, 'size'=>$user_avatar_size, 'before'=>'<div id="wps_display_avatar" style="float:left; margin-right:5px;">', 'after'=>'</div>'));
	$html .= wps_usermeta(array('user_id'=>$user_id, 'meta'=>'wpspro_map', 'map_style'=>$map_style, 'size'=>$map_size, 'zoom'=>$map_zoom, 'before'=>'<div id="wps_display_map" style="float:left;margin-right:15px;">', 'after'=>'</div>'));
	$html .= '<div style="float:left;margin-right:15px;">';
	$html .= wps_usermeta(array('user_id'=>$user_id, 'meta'=>'wpspro_home', 'before'=>'<strong>'.$town_label.'</strong><br />', 'after'=>'<br />'));
	$html .= wps_usermeta(array('user_id'=>$user_id, 'meta'=>'wpspro_country', 'before'=>'<strong>'.$country_label.'</strong><br />', 'after'=>'<br />'));
	$html .= wps_usermeta_change_link($atts);
	$html .= '</div>';
	$html .= '<div id="wps_display_friend_requests" style="margin-left:10px;float:left;min-width:200px;">';
	$html .= wps_friends_pending(array('user_id'=>$user_id, 'count' => 1, 'before'=>'<strong>'.__('Friend Requests', WPS2_TEXT_DOMAIN).'</strong><br />'));
	$html .= wps_friends_add_button(array());
	$html .= '</div>';
	$html .= '</div>';
	$html .= wps_activity_post($atts);
	$html .= wps_activity($atts);

	return $html;

}

function wps_activity_post($atts) {

	// Init
	add_action('wp_footer', 'wps_activity_init');

	$html = '';

	global $current_user;

	// Shortcode parameters
	extract( shortcode_atts( array(
        'user_id' => false,
		'class' => '',
		'label' => __('Add Post', WPS2_TEXT_DOMAIN),
		'private_msg' => __('Only friends can post here', WPS2_TEXT_DOMAIN),
        'account_closed_msg' => __('Account closed.', WPS2_TEXT_DOMAIN),
		'before' => '',
		'after' => '',
	), $atts, 'wps_activity' ) );

	if (!$user_id) $user_id = wps_get_user_id();

	$friends = wps_are_friends($current_user->ID, $user_id);
	// By default same user, and friends of user, can see profile
	$user_can_see_activity = ($current_user->ID == $user_id || $friends['status'] == 'publish') ? true : false;
	$user_can_see_activity = apply_filters( 'wps_check_activity_security_filter', $user_can_see_activity, $user_id, $current_user->ID );

	if (is_user_logged_in() && $user_can_see_activity):

		$form_html = '';
        if (!wps_is_account_closed($user_id)):
            $form_html .= '<div id="wps_activity_post_div" style="display:none">';
                $form_html .= '<form id="theuploadform">';
                $form_html .= '<input type="hidden" id="wps_activity_post_action" name="action" value="wps_activity_post_add" />';
                $form_html .= '<input type="hidden" name="wps_activity_post_author" value="'.$current_user->ID.'" />';
                $form_html .= '<input type="hidden" name="wps_activity_post_target" value="'.$user_id.'" />';

                $form_html = apply_filters( 'wps_activity_post_pre_form_filter', $form_html, $atts, $user_id, $current_user->ID );

                $form_html .= '<textarea id="wps_activity_post" autocomplete="off" name="wps_activity_post"></textarea>';
                $form_html .= '<input id="wps_activity_post_button" type="submit" class="wps_submit '.$class.'" value="'.$label.'" />';
                $form_html .= '</form>';
            $form_html .= '</div>';
        else:
            $form_html .= '<div class="wps_account_closed">'.$account_closed_msg.'</div>';
        endif;

		$html .= $form_html;


	else:

		$html .= '<div id="wps_activity_post_private_msg">'.$private_msg.'</div>';

	endif;

	if ($html) $html = htmlspecialchars_decode($before).$html.htmlspecialchars_decode($after);

	return $html;
}

function wps_activity($atts) {

	// Init
	add_action('wp_footer', 'wps_activity_init');

	$html = '';
	global $current_user, $wpdb;
    
	$html .= '<br style="clear:both" />';
	// Shortcode parameters
	extract( shortcode_atts( array(
		'user_id' => false,
        'mimic_user_id' => false,
		'post_id' => false,
		'include_self' => 1,
		'include_friends' => 1,
		'count' => 100,
		'get_max' => 100,
		'avatar_size' => 64,
		'more' => 50,
		'more_label' => 'more',
		'hide_until_loaded' => false,
		'type' => '',
		'comment_avatar_size' => 40,
		'comment_size' => 5,
		'comment_size_text_plural' => __('Show previous %d comments...', WPS2_TEXT_DOMAIN),
		'comment_size_text_singular' => __('Show previous comment...', WPS2_TEXT_DOMAIN),
		'label' => __('Comment', WPS2_TEXT_DOMAIN),
		'class' => '',
		'link' => true,
        'private_msg' => __('Activity is private', WPS2_TEXT_DOMAIN),
		'not_found' => __('Sorry, this activity post is not longer available.', WPS2_TEXT_DOMAIN),
		'delete_label' => __('Delete', WPS2_TEXT_DOMAIN), // blank to hide
		'sticky_label' => __('Stick', WPS2_TEXT_DOMAIN), // blank to hide
		'unsticky_label' => __('Unstick', WPS2_TEXT_DOMAIN),
		'hide_label' => __('Hide', WPS2_TEXT_DOMAIN), // blank to hide
		'allow_replies' => 1,
		'date_format' => __('%s ago', WPS2_TEXT_DOMAIN),
		'before' => '',
		'after' => '',
	), $atts, 'wps_activity' ) );

	if (!$user_id):
        $user_id = wps_get_user_id();
        $this_user = $current_user->ID;
    else:
        if ($mimic_user_id):
            $this_user = $user_id;
        else:
            $this_user = $current_user->ID;
        endif;
    endif;
    
	// Check for single post view
	if (!$post_id && isset($_GET['view'])) $post_id = $_GET['view'];

	$activity = array();

	$friends = wps_are_friends($this_user, $user_id);
	// By default same user, and friends of user, can see profile
	$user_can_see_activity = ($this_user == $user_id || $friends['status'] == 'publish') ? true : false;
	$user_can_see_activity = apply_filters( 'wps_check_activity_security_filter', $user_can_see_activity, $user_id, $this_user );

    // Pre activity filter
    $html = apply_filters( 'wps_activity_pre_filter', $html, $atts, $user_id, $this_user );
    
	if ($user_can_see_activity):

		if (!$post_id):

			if ($type == ''): // Activity only

				// Get user's activity (and posts targeted to user)
				if ($include_self):
					$sql = "SELECT p.ID, p.post_title, p.post_author, p.post_date_gmt as post_date, c.comment_date, m.meta_value AS target_ids FROM ".$wpdb->prefix."posts p 
						LEFT JOIN ".$wpdb->prefix."comments c ON p.ID = c.comment_post_ID
						LEFT JOIN ".$wpdb->prefix."postmeta m ON p.ID = m.post_id
						WHERE p.post_type = %s
						AND m.meta_key = 'wps_target'
						AND p.post_status = 'publish'
						AND (
						  	p.post_author = %d OR
						  	p.post_author = %d OR
							c.comment_author = %d OR
							c.comment_author = %d OR
							m.meta_value LIKE '%%\"%d\"%%' OR
							m.meta_value = %d OR 
							m.meta_value LIKE '%%\"%d\"%%' OR
							m.meta_value = %d
						)
						ORDER BY p.ID DESC
						LIMIT 0,%d";

					$results = $wpdb->get_results($wpdb->prepare($sql, 'wps_activity', $user_id, $this_user, $user_id, $this_user, $user_id, $user_id, $this_user, $this_user, $get_max));

					$added_count = 0;
					$added_sticked = 0;
					foreach ($results as $r):
    
                        // Check this is a normal activity post
                        $activity_type = get_post_meta($r->ID, 'wps_activity_type', true);
                        if (!$activity_type):

                            $target_users = array();
                            $target_ids = $r->target_ids;
                            // Make a note of any target users (excluding post author)
                            if ($target_ids):
                                if (is_array($target_ids) && $target_ids_array = unserialize($target_ids)):
                                    // Target is to multiple users
                                    $target_users = array_merge($target_users, $target_ids_array);
                                else:
                                    // Target is one user
                                    array_push($target_users, $target_ids);
                                endif;
                            endif;
                            $add = false;
                            if ($user_id == $this_user):		// ------------ On user's own page

                                // If author is this user
                                if ($r->post_author == $user_id) { $add = true; };
                                // If this user is a target (and a friend)
                                if (wps_are_friends($r->post_author, $user_id)) { $add = true; };
                                // Exclude if this is just a friend sharing to friends
                                if ($r->post_author != $user_id && (string)$r->post_author == $target_ids) { $add = false; };

                            else: 									// ------------ On a friends page

                                // If to a friend, and current user is a friend of this user
                                if ($r->post_author == $target_ids && wps_are_friends($r->post_author, $this_user)) { $add = true; };
                                // If from this user to current user
                                if ($r->post_author == $user_id && in_array((string)$this_user, $target_users)) { $add = true; };
                                // If from current user to this user
                                if ($r->post_author == $this_user && in_array((string)$user_id, $target_users)) { $add = true; };
                                // If to this user and from current user (handle array)
                                if ( preg_match( '/^a:\d+:{.*?}$/', $target_ids ) ): 
                                    $target_ids_array = unserialize($target_ids);
                                    if ($r->post_author == $this_user && in_array((int)$user_id, $target_ids_array)) { $add = true; };
                                endif;

                                // Exclude own posts to friends
                                if ($r->post_author != $user_id && $target_ids == $r->post_author) { $add = false; };

                            endif;

                            if ($add):
                                $is_sticky = get_post_meta( $r->ID, 'wps_sticky', true ) ? 2 : 1;
                                if ($r->post_date > $r->comment_date):
                                    array_push($activity, array('ID' => $r->ID, 'date' => $r->post_date, 'is_sticky' => $is_sticky));
                                else:
                                    array_push($activity, array('ID' => $r->ID, 'date' => $r->comment_date, 'is_sticky' => $is_sticky));
                                endif;
                                $added_count++;
                                if ($is_sticky == 2) $added_sticked++;
                            endif;
                        endif;
					endforeach;
				endif;

				// Get activity from all friends of this page user
				if ($include_friends):
					$friends = wps_get_friends($user_id);
					if ($friends):
						foreach ($friends as $friend):

							$sql = "SELECT p.ID, p.post_date_gmt as post_date, p.post_author, c.comment_date, m.meta_value AS target_ids FROM ".$wpdb->prefix."posts p 
								LEFT JOIN ".$wpdb->prefix."comments c ON p.ID = c.comment_post_ID
								LEFT JOIN ".$wpdb->prefix."postmeta m ON p.ID = m.post_id
								WHERE p.post_type = %s
								AND m.meta_key = 'wps_target'
								AND p.post_status = 'publish'
								AND (p.post_author = %d OR p.post_author = %d)
								ORDER BY p.ID DESC
								LIMIT 0, %d";
							
							$results = $wpdb->get_results($wpdb->prepare($sql, 'wps_activity', $friend['ID'], $this_user, $count));

							foreach ($results as $r):
								$add = false;
								$target_ids = $r->target_ids;
								if (is_array($target_ids)):
									// Show if this user is in the list of target user IDs
									$target_ids_array = unserialize($target_ids);
									if (in_array((string)$user_id, $target_ids_array)) { $add = true; };
								else:
									// Show if this user is the target, or the user is posting to all friends
									if ($user_id == $target_ids || $r->post_author == $r->target_ids):
										$add = true;
									endif;
								endif;
								// Check that author's permissions for their activity
								if ($add):
									$user_can_see_activity = apply_filters( 'wps_check_activity_security_filter', $add, $r->post_author, $this_user );
									if (!$user_can_see_activity) $add = false;
								endif;

								// Current user is the author, always show
								if ($r->post_author == $this_user) { $add = true; };

								if ($add):
									$is_sticky = get_post_meta( $r->ID, 'wps_sticky', true ) ? 2 : 1;
									if ($r->post_date > $r->comment_date):
										array_push($activity, array('ID' => $r->ID, 'date' => $r->post_date, 'is_sticky' => $is_sticky));
									else:
										array_push($activity, array('ID' => $r->ID, 'date' => $r->comment_date, 'is_sticky' => $is_sticky));
									endif;									
								endif;
							endforeach;

						endforeach;
					endif;
				endif;

			endif;

			// Any more activity?
			$activity = apply_filters( 'wps_activity_items_filter', $activity, $atts, $user_id, $this_user );

		else:

			// Single post view
			$single = get_post($post_id);

			if ($single):

				$target_ids = get_post_meta($post_id, 'wps_target', true);

				$add = false;
				if (is_array($target_ids)):
					// Show if this user is in the list of target user IDs
					if (in_array((string)$this_user, $target_ids)) { $add = true; };
				else:
					// Show if this user is the target, or the user is posting to all friends
					if ($this_user == $target_ids || $single->post_author == $target_ids) $add = true;
				endif;
				// Check that author's permissions for their activity
				if ($add):
					$user_can_see_activity = apply_filters( 'wps_check_activity_security_filter', $add, $single->post_author, $this_user );
					if (!$user_can_see_activity) $add = false;
				endif;

				// Current user is the author, always show
				if ($single->post_author == $this_user) { $add = true; };

				if ($add) array_push($activity, array('ID' => $post_id, 'date' => $single->post_date, 'is_sticky' => 0));														

				// Any more activity?
				$activity = apply_filters( 'wps_activity_single_item_filter', $activity, $atts, $user_id, $this_user );

			else:

				$html .= $not_found;

			endif;

		endif;

		if ($activity):

			$shown = array();
	
			/* PHP including older versions */
			usort($activity, "wps_sort_activity");

			$html .= '<div id="wps_activity_items"';
				if ($hide_until_loaded) $html .= 'style="display:none"';
				$html .= '>';

				$items = '';
				$shown_count = 0;

				foreach ($activity as $i):

					if (!in_array($i['ID'], $shown) && $i['ID']):
    
                        // Check not hidden
                        $hidden_list = get_post_meta ($i['ID'], 'wps_activity_hidden', true);    
                        $hidden = ($hidden_list && in_array((int)$user_id, $hidden_list)) ? true : false;

                        if (!$hidden):
    
                            array_push($shown, $i['ID']);
                            $item = get_post($i['ID']);

                            $item_html = '';
                            $is_sticky = get_post_meta( $item->ID, 'wps_sticky', true );
                            $is_sticky_css = $is_sticky ? ' wps_sticky' : '';

                            $item_html .= '<div class="wps_activity_item'.$is_sticky_css.'" id="wps_activity_'.$item->ID.'" style="position:relative;padding-left: '.($avatar_size+10).'px">';

                                $item_html .= '<div id="wps_activity_'.$item->ID.'_content" class="wps_activity_content">';

                                    // Settings
                                    $settings = '';
                                    if ($item->post_author == $this_user || current_user_can('manage_options')):
                                        $settings .= '<div class="wps_activity_settings" style="display:none">';
                                            $settings .= '<img style="height:15px;width:15px;" src="'.plugins_url('images/wrench'.get_option('wpspro_icon_colors').'.png', __FILE__).'" />';
                                        $settings .= '</div>';
                                        $settings .= '<div class="wps_activity_settings_options" style="display:none">';
                                            if (!$is_sticky && $sticky_label) $settings .= '<a class="wps_activity_settings_sticky" rel="'.$item->ID.'" href="javascript:void(0);">'.$sticky_label.'</a>';
                                            if ($is_sticky && $unsticky_label) $settings .= '<a class="wps_activity_settings_unsticky" rel="'.$item->ID.'" href="javascript:void(0);">'.$unsticky_label.'</a>';
                                            if ($delete_label) $settings .= '<a class="wps_activity_settings_delete" rel="'.$item->ID.'" href="javascript:void(0);">'.$delete_label.'</a>';
                                            $settings = apply_filters( 'wps_activity_item_setting_filter', $settings, $atts, $item, $user_id, $this_user);
                                        $settings .= '</div>';
                                    endif;
                                    $settings = apply_filters( 'wps_activity_item_settings_filter', $settings, $atts, $item, $user_id, $this_user);
                                    $item_html .= $settings;

                                    // Hide/Report
                                    if ($item->post_author != $this_user):
                                        $item_html .= '<div class="wps_activity_settings" style="display:none">';
                                            $item_html .= '<img style="height:15px;width:15px;" src="'.plugins_url('images/wrench'.get_option('wpspro_icon_colors').'.png', __FILE__).'" />';
                                        $item_html .= '</div>';
                                        $item_html .= '<div class="wps_activity_settings_options" style="display:none">';
                                            if ($hide_label) $item_html .= '<a class="wps_activity_settings_hide" rel="'.$item->ID.'" href="javascript:void(0);">'.$hide_label.'</a>';
                                        $item_html .= '</div>';
                                    endif;

                                    // Avatar
                                    $item_html .= '<div class="wps_activity_item_avatar" style="float: left; margin-left: -'.($avatar_size+10).'px">';
                                        $item_html .= user_avatar_get_avatar($item->post_author, $avatar_size);
                                    $item_html .= '</div>';

                                    // Meta
                                    $recipients = '';
                                    $item_html .= '<div class="wps_activity_item_meta">';
                                        $item_html .= wps_display_name(array('user_id'=>$item->post_author, 'link'=>$link));
                                        $target_ids = get_post_meta( $item->ID, 'wps_target', true );
                                        if (is_array($target_ids)):
                                            $c=0;
                                            $recipients = ' &rarr; ';
                                            foreach ($target_ids as $target_id):
                                                if ( $target_id != $item->post_author):
                                                    if ($c) $recipients .= ', ';
                                                    $recipients .= wps_display_name(array('user_id'=>$target_id, 'link'=>$link));
                                                    $c++;
                                                endif;
                                            endforeach;	
                                        else:
                                            if ( $target_ids != $item->post_author):
                                                $recipient_display_name = wps_display_name(array('user_id'=>$target_ids, 'link'=>$link));
                                                if ($recipient_display_name):
                                                    $recipients = ' &rarr; '.$recipient_display_name;
                                                endif;
                                            endif;
                                        endif;

                                        // In case of changes
                                        $recipients = apply_filters( 'wps_activity_item_recipients_filter', $recipients, $atts, $target_ids, $item->ID, $user_id, $this_user );
                                        $item_html .= $recipients;

                                        // Date
                                        $item_html .= '<br />';
                                        $item_html .= '<div class="wps_ago">'.sprintf($date_format, human_time_diff(strtotime($item->post_date), current_time('timestamp', 0)), WPS2_TEXT_DOMAIN).'</div>';

                                        // Any more meta?
                                        // Passes $item_html, shortcodes options ($atts), current post ID ($item->ID), user page ($user_id), current users ID ($this_user)
                                        $item_html = apply_filters( 'wps_activity_item_meta_filter', $item_html, $atts, $item->ID, $user_id, $this_user );

                                    $item_html .= '</div>';

                                    /* POST */

                                    // Shortern if necessary and applicable
                                    $post_words = wps_bbcode_replace(convert_smilies(wps_make_clickable(wpautop(esc_html($item->post_title)))));
                                    if (strpos($post_words, '[q]') === false && strpos($post_words, '[items]') === false):
                                        $words = explode(' ', $post_words, $more + 1);
                                        if (count($words)> $more) {
                                            array_pop($words);
                                            array_push($words, '... [<a class="activity_item_more" rel="'.$i['ID'].'" title="'.$more_label.'" href="javascript:void(0)">'.$more_label.'</a>]');
                                            $item_html .= '<div style="display:none" id="activity_item_full_'.$i['ID'].'">'.str_replace(': ', '<br />', $post_words).'</div>';
                                            $post_words = implode(' ', $words);
                                        }									
                                    endif;

                                    $post_words = str_replace('[a]', '<a', $post_words);
                                    $post_words = str_replace('[a2]', '>', $post_words);
                                    $post_words = str_replace('[/a]', '</a>', $post_words);


                                    if (strpos($post_words, '[q]') !== false && strpos($post_words, '[/q]') === false) $post_words .= '[/q]';
                                    $p = str_replace(': ', '<br />', $post_words);

                                    $p = str_replace('<p>', '', $p);
                                    $p = str_replace('</p>', '', $p);
                                    $p = '<div id="activity_item_'.$item->ID.'">'.$p.'</div>';

                                    // Check for any items (attachments)
                                    if ($i=strpos($p, '[items]')):
                                        $attachments_list = substr($p, $i+7, strlen($p)-($i+7));
                                        if (strpos($attachments_list, '[')) 
                                            $attachments_list = substr($attachments_list, 0, strpos($attachments_list, '['));
                                        $attachments = explode(',', $attachments_list);
                                        $attachment_html = '';
                                        foreach ($attachments as $attachment):
                                            $attachment_html .= '<div class="wps_activity_item_attachment wps_activity_item_attachment_item">'.wp_get_attachment_image($attachment, 'thumbnail');            
                                                $image_src = wp_get_attachment_image_src( $attachment, 'full' );
                                                $attachment_html .= '<div data-width="'.$image_src[1].'" data-height="'.$image_src[2].'" class="wps_activity_item_attachment_full">'.$image_src[0].'</div>';
                                            $attachment_html .= '</div>'; 
                                        endforeach;
                                        $attachment_html .= '<div style="clear:both"></div>';
                                        $p = str_replace('[items]', '', $p);
                                        $p = str_replace($attachments_list, '<div style="display:none">'.$attachments_list.'</div>', $p);
                                        $p .= $attachment_html;
                                    endif;

                                    $p = str_replace('[q]', '<div class="wps_quoted_content">', $p);
                                    $p = str_replace('[/q]', '</div>', $p);
                                    $p = str_replace('[p]', '<div class="wps_p_content">', $p);
                                    $p = str_replace('[/p]', '</div>', $p);
    
                                    $item_html .= '<div class="wps_activity_item_post" id="activity_item_'.$item->ID.'">'.$p.'</div>';

                                    // Filter for handling anything else
                                    // Passes $item_html, shortcodes options ($atts), current post ID ($item->ID), post title ($item->post_stitle), user page ($user_id), current users ID ($this_user)
                                    $item_html = apply_filters( 'wps_activity_item_filter', $item_html, $atts, $item->ID, $item->post_title, $user_id, $this_user );

                                    // Existing Comments
                                    $args = array(
                                        'post_id' => $item->ID,
                                        'orderby' => 'ID',
                                        'order' => 'ASC',
                                    );
                                    $comments = get_comments($args);
                                    if ($comments) {
                                    	$comment_count = sizeof($comments);
                                    	$item_html .= '<div id="wps_activity_comments">';

                                    	$comments_shown = 0;
                                        foreach($comments as $comment) :

                                            $item_html .= '<a name="wps_comment_'.$item->ID.'"></a>';

	                                    	if ($comment_count > $comment_size && $comments_shown == 0):
	                                    		$previous = $comment_count-$comment_size > 1 ? sprintf($comment_size_text_plural, ($comment_count-$comment_size)) : sprintf($comment_size_text_singular, ($comment_count-$comment_size));
	                                    		$item_html .= '<div rel="'.$item->ID.'" class="wps_activity_hidden_comments">'.$previous.'</div>';
	                                    	endif;

                                        	$hidden_style = ($comments_shown >= $comment_count - $comment_size) ? '' : 'display:none;';
                                            $item_html .= '<div id="wps_comment_'.$comment->comment_ID.'" class="wps_activity_comment wps_activity_item_'.$item->ID.'" style="'.$hidden_style.'position:relative;padding-left: '.($comment_avatar_size+10).'px">';

                                                // Settings
                                                if ($comment->user_id == $this_user || current_user_can('manage_options')):
                                                    $item_html .= '<div class="wps_comment_settings" style="display:none">';
                                                        $item_html .= '<img style="height:15px;width:15px;" src="'.plugins_url('images/wrench'.get_option('wpspro_icon_colors').'.png', __FILE__).'" />';
                                                    $item_html .= '</div>';
                                                    $item_html .= '<div class="wps_comment_settings_options" style="display:none">';
                                                        $item_html .= '<a class="wps_comment_settings_delete" rel="'.$comment->comment_ID.'" href="javascript:void(0);">'.__('Delete comment', WPS2_TEXT_DOMAIN).'</a>';
                                                    $item_html .= '</div>';
                                                endif;

                                                // Avatar
                                                $item_html .= '<div class="wps_activity_post_comment_avatar" style="float:left; margin-left: -'.($comment_avatar_size+10).'px">';
                                                    $item_html .= user_avatar_get_avatar($comment->user_id, $comment_avatar_size);
                                                $item_html .= '</div>';

                                                // Name and date
                                                $item_html .= wps_display_name(array('user_id'=>$comment->user_id, 'link'=>$link));
                                                $item_html .= '<br />';
                                                $item_html .= '<div class="wps_ago">'.sprintf($date_format, human_time_diff(strtotime($comment->comment_date), current_time('timestamp', 0)), WPS2_TEXT_DOMAIN).'</div>';

                                                // Any other meta
                                                // Passes $item_html, shortcodes options ($atts), current post ID ($item->ID), current comment ID ($comment->comment_ID), user page ($user_id), current users ID ($this_user)
                                                $item_html = apply_filters( 'wps_activity_comment_meta_filter', $item_html, $atts, $item->ID, $comment->comment_ID, $user_id, $this_user );                                    

                                                // The Comment
                                                $item_html .= wps_bbcode_replace(convert_smilies(wps_make_clickable(wpautop(esc_html($comment->comment_content)))));

		                                        // Filter to add anything to end of comment
		                                        $item_html = apply_filters( 'wps_activity_post_comment_filter', $item_html, $atts, $item->ID, $comment->comment_ID, $user_id, $this_user );                                                

                                            $item_html .= '</div>';

                                            $comments_shown++;

                                        endforeach;

                                        $item_html .= '</div>';
                                    }

                                $item_html .= '</div>';

                                // Add new comment	
                                if (is_user_logged_in() && $allow_replies && !wps_is_account_closed($user_id)):
                                    $add_form = '<div class="wps_activity_post_comment_div">';
                                        $add_form .= '<input type="hidden" id="wps_activity_plugins_url" value="'.plugins_url( '', __FILE__ ).'" />';
                                        $add_form .= '<textarea class="wps_activity_post_comment" id="post_comment_'.$item->ID.'"></textarea>';
                                        $add_form .= '<input class="wps_submit wps_activity_post_comment_button '.$class.'" data-link="'.$link.'" data-size="'.$comment_avatar_size.'" rel="'.$item->ID.'" type="submit" value="'.$label.'" />';
                                    $add_form .= '</div>';
                                    $add_form = apply_filters( 'wps_activity_new_comment_filter', $add_form, $atts, $item->ID, $user_id, $this_user );
                                    $item_html .= $add_form;
                                endif;

                            $item_html .= '</div>'; // end of post

                            $items .= $item_html;

                            $shown_count++;
                            if ($shown_count == $count) break;
    
                        endif;

					endif;

				endforeach;

				$html .= $items;

			$html .= '</div>';

		endif;

	else:

		$html .= '<div id="wps_activity_post_private_msg">'.$private_msg.'</div>';

	endif;

	if ($html) $html = htmlspecialchars_decode($before).$html.htmlspecialchars_decode($after);

	return $html;
}
if (!is_admin()) {
	add_shortcode(WPS_PREFIX.'-activity-page', 'wps_activity_page');
	add_shortcode(WPS_PREFIX.'-activity-post', 'wps_activity_post');
	add_shortcode(WPS_PREFIX.'-activity', 'wps_activity');
}

function wps_sort_activity($a, $b) {
    if ($a['is_sticky'] < $b['is_sticky']) return 1;
    if ($a['is_sticky'] > $b['is_sticky']) return -1;
    if ($a['date'] < $b['date']) return 1;
    if ($a['date'] > $b['date']) return -1;        
    return 0;
};


?>
