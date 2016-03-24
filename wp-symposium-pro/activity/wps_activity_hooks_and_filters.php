<?php


// Hook into wps_activity_post_add to send alerts for new posts
// to target user, if not the current user

add_action( 'wps_activity_post_add_hook', 'wps_activity_post_add_alerts', 10, 3 );
function wps_activity_post_add_alerts($the_post, $the_files, $new_id) {

	if (post_type_exists('wps_alerts')):

		global $current_user;
	
		$recipients = array();
		$get_the_post = get_post($new_id);
		$get_recipient = get_user_by('id', $get_the_post->wps_target);
		if ($get_recipient && $get_the_post->wps_target != $the_post['wps_activity_post_author']) {
			// To a single person
			array_push($recipients, $get_the_post->wps_target);
		} else {
			// Default, ie. to all friends
			global $current_user;	
			$friends = wps_get_friends($current_user->ID);
			if ($friends):
				foreach ($friends as $friend):
					array_push($recipients, $friend['ID']);	
				endforeach;
			endif;
		}

		// alerts only added it activity type is default (ie. not set)
		// other types must add alerts themselves
		if (!$get_the_post->wps_target_type):

			// Any changes to recipients list?
			$recipients = apply_filters('wps_activity_post_add_alerts_recipients_filter', $recipients, $the_post, $the_files, $new_id);

			if (post_type_exists('wps_alerts') && count($recipients) > 0):

				$sent = array();
				foreach ($recipients as $target_id):

					if ( (int)$target_id != (int)$current_user->ID && !in_array($target_id, $sent) ):

						$status = 'publish';
						if (get_user_meta($target_id, 'wps_activity_subscribe', true) != 'off') $status = 'pending';

						array_push($sent, $target_id);

						$title = get_bloginfo('name').': '.__('New activity post', WPS2_TEXT_DOMAIN);
						$content = '';

						$content = apply_filters( 'wps_alert_before', $content );

						$recipient = get_user_by ('id', $target_id); // Get user by ID of post recipient
						$content .= '<h1>'.$recipient->display_name.'</h1>';

						$author = get_user_by('id', $the_post['wps_activity_post_author']);
						$msg = sprintf(__('You have a new post on your activity from %s.', WPS2_TEXT_DOMAIN), $author->display_name);
						$content .= '<p>'.$msg.'</p>';
						$content .= '<p><em>'.$the_post['wps_activity_post'].'</em></p>';
						
						if ( wps_using_permalinks() ):	
							$u = get_user_by('id', $the_post['wps_activity_post_author']);
							$parameters = sprintf('%s?view=%d', urlencode($u->user_login), $new_id);
							$permalink = get_permalink(get_option('wpspro_profile_page'));
							$url = $permalink.$parameters;
						else:
							$parameters = sprintf('user_id=%d&view=%d', urlencode($the_post['wps_activity_post_author']), $new_id);
							$permalink = get_permalink(get_option('wpspro_profile_page'));
							$url = $permalink.'&'.$parameters;
						endif;
						$content .= '<p><a href="'.$url.'">'.$url.'</a></p>';

						$content = apply_filters( 'wps_alert_after', $content );

						$post = array(
							'post_title'		=> $title,
							'post_excerpt'		=> $msg,
							'post_content'		=> $content,
						  	'post_status'   	=> $status,
						  	'post_type'     	=> 'wps_alerts',
						  	'post_author'   	=> $the_post['wps_activity_post_author'],
						  	'ping_status'   	=> 'closed',
						  	'comment_status'	=> 'closed',
						);  
						$new_alert_id = wp_insert_post( $post );

						update_post_meta( $new_alert_id, 'wps_alert_recipient', $recipient->user_login );	
						update_post_meta( $new_alert_id, 'wps_alert_target', 'profile' );
						update_post_meta( $new_alert_id, 'wps_alert_parameters', $parameters );	

						if ($status == 'publish'):
							update_post_meta( $new_alert_id, 'wps_alert_failed_datetime', current_time('mysql', 1) );
							update_post_meta( $new_alert_id, 'wps_alert_note', __('Chosen not to receive', WPS2_TEXT_DOMAIN) );
						endif;

						do_action( 'wps_alert_add_hook', $recipient->ID, $new_alert_id, $url, $msg );

					endif;

				endforeach;

			endif;

		endif;

	endif;

}

// Hook into wps_activity_comment_add_hook to send alerts for new comments
// excluding the current user

add_action( 'wps_activity_comment_add_hook', 'wps_activity_comment_add_alerts', 10, 2 );
function wps_activity_comment_add_alerts($the_post, $new_id) {

	if (post_type_exists('wps_alerts')):

		// Get original post author
		$the_comment = get_comment($new_id);
		$post_id = $the_comment->comment_post_ID;
		$original_post = get_post($post_id);

		// alerts only added it activity type is default (ie. not set)
		// other types must add alerts themselves
		if (!$original_post->wps_target_type):

			$recipients = array();

			// Add target of original post
			$target = get_post_meta($post_id, 'wps_target', true);
			$get_recipient = get_user_by('id', $original_post->wps_target);
			if ($get_recipient) {
				$recipients['target'] = $target;
			}

			// Any changes to recipients target list?
			$recipients = apply_filters('wps_activity_comment_add_alerts_recipients_filter', $recipients, $original_post, $post_id, $new_id);

			// Add original post author and target
			$recipients['author'] = (int)$original_post->post_author;

			// Add all comment authors
			$args = array(
				'post_id' => $post_id
			);
			$comments = get_comments($args);
			if ($comments):
				foreach($comments as $comment):
					if ($comment->comment_author)
						$recipients['comment '.$comment->comment_ID] = (int)$comment->comment_author;
				endforeach;
			endif;

			$sent = array();
			global $current_user;
			get_currentuserinfo();

			if ($recipients):
				foreach ($recipients as $key=>$value):

					if ($value):
    
						if ( (int)$value != (int)$current_user->ID && !in_array($value, $sent) ):

							$status = 'publish';
							if (get_user_meta($value, 'wps_activity_subscribe', true) != 'off') $status = 'pending';

							array_push($sent, $value);

							if ($key == 'author'):
								$subject = __('New comment on your post', WPS2_TEXT_DOMAIN);
							else:
								$subject = __('New comment', WPS2_TEXT_DOMAIN);
							endif;
							$subject = get_bloginfo('name').': '.$subject;

							$content = '';

							$content = apply_filters( 'wps_alert_before', $content );

							$target = get_user_by('id', $value);
							$content .= '<h1>'.$target->display_name.'</h1>';

							$author = get_user_by('login', $the_comment->comment_author);
							$msg = sprintf(__('A new comment from %s.', WPS2_TEXT_DOMAIN), $author->display_name);
							$content .= '<p>'.$msg.'</p>';
							$content .= '<p><em>'.$the_comment->comment_content.'</em></p>';

							$parameters = sprintf('user_id=%d&view=%d', (int)$original_post->post_author, $post_id);
							$permalink = get_permalink(get_option('wpspro_profile_page'));
							$url = $permalink.wps_query_mark($permalink).$parameters;
							$content .= '<p><a href="'.$url.'">'.$url.'</a></p>';

							$content .= '<p><strong>'.__('Original Post', WPS2_TEXT_DOMAIN).'</strong></p>';
							$content .= '<p>'.$original_post->post_title.'</p>';

							$content = apply_filters( 'wps_alert_after', $content );

							$post = array(
								'post_title'		=> $subject,
							  	'post_excerpt'		=> $msg,
							  	'post_content'		=> $content,
							  	'post_status'   	=> $status,
							  	'post_type'     	=> 'wps_alerts',
							  	'post_author'   	=> (int)$the_comment->comment_author,
							  	'ping_status'   	=> 'closed',
							  	'comment_status'	=> 'closed',
							);  
							$new_alert_id = wp_insert_post( $post );

							$recipient_user = get_user_by ('id', $value); // Get user by ID of email recipient
							update_post_meta( $new_alert_id, 'wps_alert_recipient', $recipient_user->user_login );	
							update_post_meta( $new_alert_id, 'wps_alert_target', 'profile' );
							update_post_meta( $new_alert_id, 'wps_alert_parameters', $parameters );	

							if ($status == 'publish'):
								update_post_meta( $new_alert_id, 'wps_alert_failed_datetime', current_time('mysql', 1) );
								update_post_meta( $new_alert_id, 'wps_alert_note', __('Chosen not to receive', WPS2_TEXT_DOMAIN) );
							endif;

							do_action( 'wps_alert_add_hook', $target->ID, $new_alert_id, $url, $msg );

						endif;

					endif;

				endforeach;
				
			endif;

		endif;

	endif;

}

?>