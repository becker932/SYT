<?php
																	/* ************* */
																	/* HOOKS/FILTERS */
																	/* ************* */

// Do action after every alert is added
add_action( 'wps_alert_add_hook', 'wps_alert_add_hook_action', 10, 4 );
function wps_alert_add_hook_action($recipient_id, $new_alert_id, $url, $msg) {
	update_post_meta($new_alert_id, 'wps_alert_url', $url);
	update_post_meta($new_alert_id, 'wps_alert_msg', $msg);
}

// Add unsubscribe to all in wps_usermeta_change
add_filter('wps_usermeta_change_filter', 'wps_activity_subs_usermeta_extend', 10, 3);
function wps_activity_subs_usermeta_extend($form_html, $atts, $user_id) {

	global $current_user;

	if (!get_user_meta($current_user->ID, 'wps_activity_subscribe', true))
		update_user_meta($current_user->ID, 'wps_activity_subscribe', 'on');

	// Shortcode parameters
	extract( shortcode_atts( array(
		'activity_subs_subscribe' => __('Receive email notifications for activity', WPS2_TEXT_DOMAIN),
		'meta_class' => 'wps_usermeta_change_label',
	), $atts, 'wps_usermeta_change' ) );

	$form_html .= '<div id="wps_activity_subs_subscribe" class="wps_usermeta_change_item">';
	$form_html .= '<div class="'.$meta_class.'"><input type="checkbox" name="wps_activity_subscribe" ';
	if (get_user_meta($current_user->ID, 'wps_activity_subscribe', true) == 'on')
		$form_html .= ' CHECKED';
	$form_html .= '/> '.$activity_subs_subscribe.'</div>';
	$form_html .= '</div>';

	return $form_html;

}

// Extend wps_usermeta_change save
add_action( 'wps_usermeta_change_hook', 'wps_activity_subs_usermeta_extend_save', 10, 4 );
function wps_activity_subs_usermeta_extend_save($user_id, $atts, $the_form, $the_files) {

	global $current_user;

	// Double check logged in
	if (is_user_logged_in()):

		if (isset($_POST['wps_activity_subscribe'])):

			update_user_meta($current_user->ID, 'wps_activity_subscribe', 'on');

		else:

			update_user_meta($current_user->ID, 'wps_activity_subscribe', 'off');

		endif;

	endif;

}

																	/* ********* */
																	/* FUNCTIONS */
																	/* ********* */


function wps_pro_insert_alert($type, $subject, $content, $author_id, $recipient_id, $parameters, $url, $msg, $status) {
	
	$post = array(
		'post_title'		=> $subject,
	  	'post_excerpt'		=> $msg,
		'post_content'		=> $content,
	  	'post_status'   	=> $status,
	  	'post_type'     	=> 'wps_alerts',
	  	'post_author'   	=> $author_id,
	  	'ping_status'   	=> 'closed',
	  	'comment_status'	=> 'closed',
	);  
	$new_alert_id = wp_insert_post( $post );

	$recipient_user = get_user_by ('id', $recipient_id); // Get user by ID of email recipient
    if ($recipient_user):
        update_post_meta( $new_alert_id, 'wps_alert_recipient', $recipient_user->user_login );	
        update_post_meta( $new_alert_id, 'wps_alert_target', $type );
        update_post_meta( $new_alert_id, 'wps_alert_parameters', $parameters );	

        if ($status == 'publish'):
            update_post_meta( $new_alert_id, 'wps_alert_failed_datetime', current_time('mysql', 1) );
            update_post_meta( $new_alert_id, 'wps_alert_note', __('Chosen not to receive', WPS2_TEXT_DOMAIN) );
        endif;

        do_action( 'wps_alert_add_hook', $recipient_user->ID, $new_alert_id, $url, $msg );
    endif;

	return $new_alert_id;
	
}


																	/* ***** */
																	/* ADMIN */
																	/* ***** */


add_action('wps_admin_getting_started_hook', 'wps_admin_getting_started_alerts');
function wps_admin_getting_started_alerts() {

	// Show menu item	
  	echo '<div class="wps_admin_getting_started_menu_item" rel="wps_admin_getting_started_alerts">'.__('Alerts', WPS2_TEXT_DOMAIN).'</div>';

  	// Show setup/help content
  	$display = isset($_POST['wps_expand']) && $_POST['wps_expand'] == 'wps_admin_getting_started_alerts' ? 'block' : 'none';
  	echo '<div class="wps_admin_getting_started_content" id="wps_admin_getting_started_alerts" style="display:'.$display.';">';

	?>

	<?php echo __('WPS Pro alerts uses the internal WordPress wp_mail() function.', WPS2_TEXT_DOMAIN).' '; ?>
	<?php echo __('If you are experiencing high volumes, depending on your host, you may want to consider using an external mail server.', WPS2_TEXT_DOMAIN).' '; ?>
	<?php echo sprintf(__('There are several WordPress plugins available to support this, such as <a href="%s">WP Mail SMTP</a>.', WPS2_TEXT_DOMAIN), "https://wordpress.org/plugins/wp-mail-smtp/"); ?>


		<table class="form-table">

		<tr valign="top"> 
		<td scope="row">
			<label for="wps_disable_alerts"><?php echo __('Disable alerts', WPS2_TEXT_DOMAIN); ?></label>
		</td>
		<td>
			<input type="checkbox" name="wps_disable_alerts" <?php if (get_option('wps_disable_alerts')) echo 'CHECKED '; ?> />
			<span class="description">
				<?php echo __('Stops alerts being sent out via email.', WPS2_TEXT_DOMAIN); ?>
			</span></td> 
		</tr> 	

		<tr valign="top"> 
		<td scope="row">
			<label for="wps_alerts_cron_schedule"><?php echo __('Frequency of Email alert notifications', WPS2_TEXT_DOMAIN); ?></label>
		</td>
		<td>
			<div style="padding-left:55px">
			<input type="text" style="margin-left:-55px;width:50px" name="wps_alerts_cron_schedule" value="<?php echo get_option('wps_alerts_cron_schedule'); ?>" />
			<span class="description">
				<?php 
				echo __('Frequency in seconds, that alerts are sent out via email, defaults to 3600 (every 1 hour).', WPS2_TEXT_DOMAIN).'<br />';
				echo __('Remember, WordPress scheduled tasks are triggered by visits to your site.', WPS2_TEXT_DOMAIN).'<br />';
				echo '<strong>'.__('Do not make too frequent, or your server performance may significantly deteriorate.', WPS2_TEXT_DOMAIN).'</strong><br />';
				echo __('When you save this page, the next cycle will by triggered.', WPS2_TEXT_DOMAIN).'<br />';
				?>
			</span>
			</div>
			</td> 
		</tr> 

		<tr valign="top"> 
		<td scope="row">
			<label for="wps_alerts_cron_max"><?php echo __('Emails to send', WPS2_TEXT_DOMAIN); ?></label>
		</td>
		<td>
			<input type="text" style="width:50px" name="wps_alerts_cron_max" value="<?php echo get_option('wps_alerts_cron_max'); ?>" />
			<span class="description">
				<?php echo __('Maximum number of emails to send per scheduled cycle.', WPS2_TEXT_DOMAIN); ?>
			</span></td> 
		</tr> 

		<tr valign="top"> 
		<td scope="row">
			<label for="wps_alerts_summary_email"><?php echo __('Summary email', WPS2_TEXT_DOMAIN); ?></label>
		</td>
		<td>
			<input type="text" name="wps_alerts_summary_email" value="<?php echo get_option('wps_alerts_summary_email'); ?>" />
			<span class="description">
				<?php echo __('Optional email address to receive summary of scheduled alerts sent.', WPS2_TEXT_DOMAIN); ?>
			</span></td> 
		</tr> 	

		<tr valign="top"> 
		<td scope="row">
			<label for="wps_alerts_from_name"><?php echo __('From name', WPS2_TEXT_DOMAIN); ?></label>
		</td>
		<td>
			<input type="text" name="wps_alerts_from_name" value="<?php echo get_option('wps_alerts_from_name'); ?>" />
			<span class="description">
				<?php echo __('Name alert notifications are sent from.', WPS2_TEXT_DOMAIN); ?>
			</span></td> 
		</tr> 	

		<tr valign="top"> 
		<td scope="row">
			<label for="wps_alerts_from_email"><?php echo __('From email', WPS2_TEXT_DOMAIN); ?></label>
		</td>
		<td>
			<input type="text" name="wps_alerts_from_email" value="<?php echo get_option('wps_alerts_from_email'); ?>" />
			<span class="description">
				<?php echo __('Email address alert notifications are sent from.', WPS2_TEXT_DOMAIN); ?>
			</span></td> 
		</tr> 	

		<tr valign="top"> 
		<td scope="row">
			<label for="wps_alerts_check"><?php echo __('Test email', WPS2_TEXT_DOMAIN); ?></label>
		</td>
		<td>
			<input type="checkbox" name="wps_alerts_check" />
			<span class="description">
				<?php echo sprintf(__('Tick to directly send a test email to %s using WordPress.', WPS2_TEXT_DOMAIN), get_bloginfo('admin_email')); ?>
			</span></td> 
		</tr> 	

		<tr valign="top"> 
		<td scope="row">
			<label for="wps_add_alert_check"><?php echo __('Test alert', WPS2_TEXT_DOMAIN); ?></label>
		</td>
		<td>
			<input type="checkbox" name="wps_add_alert_check" />
			<span class="description">
				<?php echo sprintf(__('Tick to add a WP Symposium Pro <a href="%s">Alert</a> to yourself.', WPS2_TEXT_DOMAIN), admin_url( 'edit.php?post_type=wps_alerts' )); ?>
			</span></td> 
		</tr> 	

		<?php
		// Any more?
		do_action('wps_alerts_admin_setup_form_hook');
		?>


	</table>
	<?php

	echo '</div>';
}

add_action( 'wps_admin_setup_form_save_hook', 'wps_alerts_admin_options_save', 10, 1 );
function wps_alerts_admin_options_save ($the_post) {

	if (isset($the_post['wps_disable_alerts'])):
		update_option('wps_disable_alerts', true);
	else:
		delete_option('wps_disable_alerts');
	endif;

	if ($value = $the_post['wps_alerts_cron_schedule']):
		$value = ($value >= 10) ? $value : 10; // Never less than 10 seconds
		update_option('wps_alerts_cron_schedule', $value);
	else:
		update_option('wps_alerts_cron_schedule', 3600); // Default to once an hour
	endif;

	if ($value = $the_post['wps_alerts_cron_max']):
		$value = ($value > 0) ? $value : 1; // Never less than 1
		update_option('wps_alerts_cron_max', $value);
	else:
		update_option('wps_alerts_cron_max', 25); // Default to 25
	endif;

	if ($value = $the_post['wps_alerts_summary_email']):
		update_option('wps_alerts_summary_email', $value);
	else:
		delete_option('wps_alerts_summary_email');
	endif;

	if ($value = $the_post['wps_alerts_from_name']):
		update_option('wps_alerts_from_name', $value);
	else:
		update_option('wps_alerts_from_name', get_bloginfo('name'));
	endif;

	if ($value = $the_post['wps_alerts_from_email']):
		update_option('wps_alerts_from_email', $value);
	else:
		update_option('wps_alerts_from_email', get_bloginfo('admin_email'));
	endif;

	if (isset($the_post['wps_alerts_check'])):
		$name = ($value = get_option('wps_alerts_from_name')) ? $value : get_bloginfo('name');
		$email = ($value = get_option('wps_alerts_from_email')) ? $value : get_bloginfo('admin_email');
		$headers = 'From: '.$name.' <'.$email.'>' . "\r\n";
		$content = __('Wahoo! It worked!', WPS2_TEXT_DOMAIN);
		$filtered_content = apply_filters('wps_alerts_scheduled_job_content_filter', $content, 0);		
		add_filter( 'wp_mail_content_type', 'wps_set_html_content_type' );
		if (wp_mail (get_bloginfo('admin_email'), __('Test email', WPS2_TEXT_DOMAIN), $filtered_content)):
			echo '<div class="updated"><p>'.sprintf(__('Test email sent to %s.', WPS2_TEXT_DOMAIN), get_bloginfo('admin_email')).'</p></div>';
		else:
			echo '<div class="error"><p>'.sprintf(__('Test email failed to send to %s.', WPS2_TEXT_DOMAIN), get_bloginfo('admin_email')).'</p></div>';
		endif;
	endif;

	if (isset($the_post['wps_add_alert_check'])):
        global $current_user;
		$name = ($value = get_option('wps_alerts_from_name')) ? $value : get_bloginfo('name');
		$email = ($value = get_option('wps_alerts_from_email')) ? $value : get_bloginfo('admin_email');
		$headers = 'From: '.$name.' <'.$email.'>' . "\r\n";
		$content = __('Test Alert', WPS2_TEXT_DOMAIN);
        $subject = __('Test Alert', WPS2_TEXT_DOMAIN);
        wps_pro_insert_alert('test_alert', $subject, $content, $current_user->ID, $current_user->ID, '', '', $content, 'pending');
		echo '<div class="updated"><p>'.__('Test alert added.', WPS2_TEXT_DOMAIN).'</p></div>';
	endif;

	// Clear existing schedule
	wp_clear_scheduled_hook( 'wps_symposium_pro_alerts_hook' );
	// Re-add as new schedule
	// Schedule the event for right now, then to repeat using the hook 'wps_symposium_pro_alerts_hook'
	wp_schedule_event( time(), 'wps_symposium_pro_alerts_schedule', 'wps_symposium_pro_alerts_hook' );

	// Any more to save?
	do_action( 'wps_alerts_admin_setup_form_save_hook', $the_post );

}


																	/* ******** */
																	/* CRON JOB */
																	/* ******** */

// Hook our function, wps_alerts_scheduled_job(), into the action wps_symposium_pro_alerts_hook
add_action( 'wps_symposium_pro_alerts_hook', 'wps_alerts_scheduled_job' );
function wps_alerts_scheduled_job() {

	if (!get_option('wps_disable_alerts')):

		$max = ($value = get_option('wps_alerts_cron_max')) ? $value : 25; // Defaults to 25 in one go

		$args = array (
			'post_type'              => 'wps_alerts',
			'posts_per_page'         => $max,
			'post_status'			 => 'pending',
			'order'                  => 'ASC',
			'orderby'                => 'ID',
		);

		$name = ($value = get_option('wps_alerts_from_name')) ? $value : get_bloginfo('name');
		$email = ($value = get_option('wps_alerts_from_email')) ? $value : get_bloginfo('admin_email');
		$headers = 'From: '.$name.' <'.$email.'>' . "\r\n";

		// Inform admin
		$admin_content = sprintf(__('Started scheduled alerts at %s.', WPS2_TEXT_DOMAIN), current_time('mysql', 1)).'<br /><br />';

		// Force HTML
		add_filter( 'wp_mail_content_type', 'wps_set_html_content_type' );

		$pending_posts = get_posts( $args );

		$admin_content .= sprintf(__('Pending alerts returned: %d.', WPS2_TEXT_DOMAIN), count($pending_posts)).'<br /><br />';

		$c = 0;
		foreach ( $pending_posts as $pending ): 
			if ($c < $max):
				$user_login = get_post_meta( $pending->ID, 'wps_alert_recipient', true );
				$user = get_user_by('login', $user_login);
				if ($user):
                    if (!wps_is_account_closed($user->ID)):

                        $recipient = get_post_meta( $pending->ID, 'wps_alert_recipient', true );
                        $content = wps_bbcode_replace(convert_smilies(wps_make_clickable(wpautop($pending->post_content))));
                        $filtered_content = apply_filters('wps_alerts_scheduled_job_content_filter', $content, $pending->ID);
                        if (wp_mail($user->user_email, $pending->post_title, $filtered_content, $headers)):
                            $admin_content .= '<strong>'.$pending->post_title.'</strong><br />';
                            $admin_content .= sprintf(__('Sent to: %s', WPS2_TEXT_DOMAIN), $user->user_email).'<br /><br />';
                            // Increase sent count
                            $c++;
                            // Update post
                            update_post_meta( $pending->ID, 'wps_alert_sent_datetime', current_time('mysql', 1) );
                        else:
                            update_post_meta( $pending->ID, 'wps_alert_failed_datetime', current_time('mysql', 1) );

                            $admin_content .= '<p style="color:red;font-weight:bold;">'.$pending->post_title.'</p>';
                            if ($user->user_email):
                                $admin_content .= sprintf(__('Failed to send to: %s', WPS2_TEXT_DOMAIN), $user->user_email).'<br />';
                            else:
                                $admin_content .= __('Failed to send, name email.', WPS2_TEXT_DOMAIN).'<br />';
                            endif;

                            // Get reason why
                            $msg = __('Mail function failed.', WPS2_TEXT_DOMAIN);
                            global $ts_mail_errors;
                            global $phpmailer;
                            $wps_mail_errors = array();
                            if (isset($phpmailer)) {
                                $msg .= '<br /><em>'.$phpmailer->ErrorInfo.' ('.$user->ID.'/'.$user_login.')</em>';
                                $admin_content .= '<em>'.$phpmailer->ErrorInfo.'</em><br />';
                            }
                            $admin_content .= '<br />';
                            update_post_meta( $pending->ID, 'wps_alert_note', $msg );
    
                        endif;

                    else:

                        update_post_meta( $pending->ID, 'wps_alert_failed_datetime', current_time('mysql', 1) );
                        update_post_meta( $pending->ID, 'wps_alert_note', __('Closed account', WPS2_TEXT_DOMAIN) );

                    endif;


				else:
    
				    update_post_meta( $pending->ID, 'wps_alert_failed_datetime', current_time('mysql', 1) );
				    update_post_meta( $pending->ID, 'wps_alert_note', __('No recipient', WPS2_TEXT_DOMAIN) );
    
				endif;

				// Set post to published, won't try and be sent again
				$this_post = array(
					'ID'           	=> $pending->ID,
					'post_status' 	=> 'publish'
				);
				wp_update_post( $this_post );

			endif;

		endforeach;

		// Inform admin
		$admin_content .= sprintf(__('Maximum alerts to send: %d.', WPS2_TEXT_DOMAIN), $max).'<br />';
		$admin_content .= sprintf(__('Alerts sent: %d.', WPS2_TEXT_DOMAIN), $c).'<br />';
		if ($value = get_option('wps_alerts_summary_email'))
			wp_mail($value, __('Scheduled Alerts', WPS2_TEXT_DOMAIN), $admin_content, $headers);

		remove_filter( 'wp_mail_content_type', 'wps_set_html_content_type' );

		wp_reset_query();

	endif;

}

function wps_set_html_content_type() {
	return 'text/html';
}


// Add to Core options
add_action('wps_admin_getting_started_core_hook', 'wps_admin_getting_started_core_hook_alerts');
function wps_admin_getting_started_core_hook_alerts($the_post) {
	?>
	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="wps_core_options"><?php _e('Content cleanup', WPS2_TEXT_DOMAIN); ?></label>
		</th>
		<td>
			<input type="checkbox" style="width:10px" name="wps_cleanup" /><span class="description"><?php _e('Run this if you delete users (one off operation, checkbox does not stay checked).', WPS2_TEXT_DOMAIN); ?></span>
		</td>
	</tr> 
	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="wps_filter_recent_comments"><?php _e('Recent Comments Widget', WPS2_TEXT_DOMAIN); ?></label>
		</th>
		<td>
			<input type="checkbox" style="width:10px" name="wps_filter_recent_comments" <?php if (get_option('wps_filter_recent_comments')) echo 'CHECKED '; ?>/><span class="description"><?php _e('Include forum and activity replies in Recent Comments widget (forum security not observed).', WPS2_TEXT_DOMAIN); ?></span>
		</td>
	</tr> 
	<?php
}

add_action('wps_admin_getting_started_core_save_hook', 'wps_admin_getting_started_core_save_hook_alerts', 10, 1);
function wps_admin_getting_started_core_save_hook_alerts($the_post) {

    if (isset($the_post['wps_cleanup'])):
    
		echo '<div class="wps_success"><p>';
			echo '<strong>'.__('Clean up results', WPS2_TEXT_DOMAIN).'</strong><br />';

			// Alerts with no recipient
			$alerts = get_posts( array(
				'post_type' => 'wps_alerts',
				'posts_per_page' => -1,
				'post_status' => 'any'

			) );
			if ( ! empty( $alerts ) ) {
				$alerts_c=0;
				$alerts_n=0;
				foreach ( $alerts as $alert ) {
					$target = get_post_meta($alert->ID, 'wps_alert_recipient', true);
					$alerts_n++;
					if ($target):
						$u = get_user_by('login', $target);
						if (!$u):
							wp_delete_post($alert->ID, true);
							echo sprintf(__('Alert deleted (%d does not exist).', WPS2_TEXT_DOMAIN), $target).'<br />';
							$alerts_c++;
						endif;
					else:
						wp_delete_post($alert->ID, true);
						// echo __('Alert deleted, no target.', WPS2_TEXT_DOMAIN).'<br />';
                        $alerts_c++;
					endif;
				}
				echo __('Alerts checked:', WPS2_TEXT_DOMAIN).' '.$alerts_n.', ';
				echo __('deleted:', WPS2_TEXT_DOMAIN).' '.$alerts_c.'<br />';

			} else {
				echo __('Alerts checked:', WPS2_TEXT_DOMAIN).' 0<br />';
            }

        echo '</p></div>';

	endif;

	if (isset($the_post['wps_filter_recent_comments'])):
		update_option('wps_filter_recent_comments', true);
	else:
		delete_option('wps_filter_recent_comments');
	endif;


}
?>