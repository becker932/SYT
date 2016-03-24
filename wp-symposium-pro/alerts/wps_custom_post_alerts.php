<?php

/* Create Alerts custom post type */

/* =========================== LABELS FOR ADMIN =========================== */


function wps_custom_post_alerts() {
	$labels = array(
		'name'               => __( 'Alerts', WPS2_TEXT_DOMAIN ),
		'singular_name'      => __( 'Alerts', WPS2_TEXT_DOMAIN ),
		'add_new'            => __( 'Add New', WPS2_TEXT_DOMAIN ),
		'add_new_item'       => __( 'Add New alert', WPS2_TEXT_DOMAIN ),
		'edit_item'          => __( 'Edit alert', WPS2_TEXT_DOMAIN ),
		'new_item'           => __( 'New alert', WPS2_TEXT_DOMAIN ),
		'all_items'          => __( 'Alerts', WPS2_TEXT_DOMAIN ),
		'view_item'          => __( 'View alerts', WPS2_TEXT_DOMAIN ),
		'search_items'       => __( 'Search alerts', WPS2_TEXT_DOMAIN ),
		'not_found'          => __( 'No alerts found', WPS2_TEXT_DOMAIN ),
		'not_found_in_trash' => __( 'No alerts found in the Trash', WPS2_TEXT_DOMAIN ), 
		'parent_item_colon'  => '',
		'menu_name'          => __('Alerts', WPS2_TEXT_DOMAIN),
	);
	$args = array(
		'labels'        		=> $labels,
		'description'   		=> 'Holds our alerts specific data',
		'public'        		=> true,
		'rewrite'				=> false,
		'exclude_from_search' 	=> true,
		'show_in_menu' 			=> 'wps_pro',
		'supports'      		=> array( 'title', 'editor', 'excerpt' ),
		'has_archive'   		=> false,
	);
	register_post_type( 'wps_alerts', $args );
}
add_action( 'init', 'wps_custom_post_alerts' );

/* =========================== MESSAGES FOR ADMIN =========================== */

function wps_updated_alerts_messages( $messages ) {
	global $post, $post_ID;
	$messages['wps_alerts'] = array(
		0 => '', 
		1 => __('Alert updated.', WPS2_TEXT_DOMAIN),
		2 => __('Custom field updated.', WPS2_TEXT_DOMAIN),
		3 => __('Custom field deleted.', WPS2_TEXT_DOMAIN),
		4 => __('Alert updated.', WPS2_TEXT_DOMAIN),
		5 => isset($_GET['revision']) ? sprintf( __('Alerts restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => __('Alert published.', WPS2_TEXT_DOMAIN),
		7 => __('Alert saved.', WPS2_TEXT_DOMAIN),
		8 => __('Alert submitted.', WPS2_TEXT_DOMAIN),
		9 => sprintf( __('Alert scheduled for: <strong>%1$s</strong>.'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) ),
		10 => __('Alerts draft updated.', WPS2_TEXT_DOMAIN),
	);
	return $messages;
}
add_filter( 'post_updated_messages', 'wps_updated_alerts_messages' );


/* =========================== META FIELDS CONTENT BOX WHEN EDITING =========================== */


add_action( 'add_meta_boxes', 'alerts_info_box' );
function alerts_info_box() {
    add_meta_box( 
        'alerts_info_box',
        __( 'Alert Details', WPS2_TEXT_DOMAIN ),
        'alerts_info_box_content',
        'wps_alerts',
        'side',
        'high'
    );
}

function alerts_info_box_content( $post ) {
	global $wpdb;
	wp_nonce_field( 'alerts_info_box_content', 'alerts_info_box_content_nonce' );

	if ($sent_datetime = get_post_meta( $post->ID, 'wps_alert_sent_datetime', true ) ):
		echo '<div style="margin-top:10px;font-weight:bold">'.__('Sent date and time', WPS2_TEXT_DOMAIN).'</div>';
		echo $sent_datetime;
	endif;

	if ($failed_datetime = get_post_meta( $post->ID, 'wps_alert_failed_datetime', true ) ):
		echo '<div style="margin-top:10px;font-weight:bold">'.__('Failed to send date and time', WPS2_TEXT_DOMAIN).'</div>';
		echo $failed_datetime.'<br />';
		echo get_post_meta( $post->ID, 'wps_alert_failed_reason', true );	    		
	endif;

	echo '<div style="margin-top:10px;font-weight:bold">'.__('Recipient', WPS2_TEXT_DOMAIN).'</div>';
	echo '<input type="text" id="wps_alert_recipient" style="width:100%" name="wps_alert_recipient" placeholder="'.__('User login', WPS2_TEXT_DOMAIN).'" value="'.get_post_meta( $post->ID, 'wps_alert_recipient', true ).'" />';
	$user = get_user_by('login', get_post_meta( $post->ID, 'wps_alert_recipient', true ));
	if ($user):
		echo '<br />'.$user->display_name;
		echo '<br />'.$user->user_email;
	endif;

	echo '<div style="margin-top:10px;font-weight:bold">'.__('Page slug', WPS2_TEXT_DOMAIN).'</div>';
	echo '<input type="text" id="wps_alert_target" name="wps_alert_target" placeholder="'.__('Page slug', WPS2_TEXT_DOMAIN).'" value="'.get_post_meta( $post->ID, 'wps_alert_target', true ).'" />';

	echo '<div style="margin-top:10px;font-weight:bold">'.__('Parameters', WPS2_TEXT_DOMAIN).'</div>';
	echo '<input type="text" id="wps_alert_parameters" name="wps_alert_parameters" placeholder="'.__('Querystring parameters', WPS2_TEXT_DOMAIN).'" value="'.get_post_meta( $post->ID, 'wps_alert_parameters', true ).'" />';

	echo '<div style="margin-top:10px;font-weight:bold">'.__('URL', WPS2_TEXT_DOMAIN).'</div>';
	echo '<input type="text" id="wps_alert_url" name="wps_alert_url" value="'.get_post_meta( $post->ID, 'wps_alert_url', true ).'" />';
}

add_action( 'save_post', 'alerts_info_box_save' );
function alerts_info_box_save( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	return;

	if ( !isset($_POST['alerts_info_box_content_nonce']) || !wp_verify_nonce( $_POST['alerts_info_box_content_nonce'], 'alerts_info_box_content' ) )
	return;

	if ( !current_user_can( 'edit_post', $post_id ) ) return;

	update_post_meta( $post_id, 'wps_alert_recipient', $_POST['wps_alert_recipient'] );	
	update_post_meta( $post_id, 'wps_alert_target', $_POST['wps_alert_target'] );	
	update_post_meta( $post_id, 'wps_alert_url', $_POST['wps_alert_url'] );	
	update_post_meta( $post_id, 'wps_alert_parameters', $_POST['wps_alert_parameters'] );	

}

/* =========================== COLUMNS WHEN VIEWING =========================== */

/* Columns for Posts list */
add_filter('manage_posts_columns', 'alerts_columns_head');
add_action('manage_posts_custom_column', 'alerts_columns_content', 10, 2);

// ADD NEW COLUMN
function alerts_columns_head($defaults) {
    global $post;
	if ($post->post_type == 'wps_alerts') {
		//$defaults['col_id'] = 'ID';
		$defaults['col_content'] = 'Content';
		$defaults['col_sent'] = 'Sent';
		$defaults['col_recipient'] = 'Recipient';
		$defaults['col_recipient_email'] = 'Email';
    }
    return $defaults;
}
 
// SHOW THE COLUMN CONTENT
function alerts_columns_content($column_name, $post_ID) {
    if ($column_name == 'col_id') {
		echo $post_ID;
    }
    if ($column_name == 'col_content') {
    	$post = get_post($post_ID);
		$content = preg_replace('#<[^>]+>#', ' ', $post->post_content);
		$max_len = 100;
		if (strlen($content) > $max_len) $content = substr($content, 0, $max_len).'...';
		echo $content;
    }
    if ($column_name == 'col_sent') {
    	$post = get_post($post_ID);
    	$success_date = get_post_meta( $post->ID, 'wps_alert_sent_datetime', true );
    	if ($success_date):
    		echo $success_date;
    	else:
    		$failed_date = get_post_meta( $post->ID, 'wps_alert_failed_datetime', true );
    		if ($failed_date):
	    		echo '<div style="color: #f00">'.$failed_date.'</div>';
	    		echo get_post_meta( $post_ID, 'wps_alert_note', true );
	    	else:
	    		echo __('waiting...', WPS2_TEXT_DOMAIN);
	    	endif;
    	endif;
    }
    if ($column_name == 'col_recipient') {
		$user = get_user_by('login', get_post_meta( $post_ID, 'wps_alert_recipient', true ));
		if ($user):
			echo $user->user_login;
		else:
			echo '<div style="color: #f00">'.sprintf(__('User "%s" does not exist', WPS2_TEXT_DOMAIN), get_post_meta( $post_ID, 'wps_alert_recipient', true )).'</div>';
		endif;
    }
    if ($column_name == 'col_recipient_email') {
		$user = get_user_by('login', get_post_meta( $post_ID, 'wps_alert_recipient', true ));
		if ($user)
			echo $user->user_email;
    }

}


/* =========================== EXTRA ACTIONS =========================== */

add_filter( 'views_edit-wps_alerts', 'wps_alerts_clear_sent' );
function wps_alerts_clear_sent( $views )
{
	if ( current_user_can( 'manage_options' ) ):

		if (isset($_REQUEST['wps_action'])):

			$nonce = $_REQUEST['_wpnonce'];
			if ( wp_verify_nonce( $nonce, 'wps_alerts_clear' ) ) {

				global $wpdb;

				if ($_REQUEST['wps_action'] == 'wps_alerts_clear_sent'):

					$sql = "DELETE FROM ".$wpdb->prefix."posts WHERE post_type='wps_alerts' and post_status = 'publish'";
					$wpdb->query($sql);

					echo '<div class="updated"><p>';
					echo __('Sent alerts removed (refresh your page).', WPS2_TEXT_DOMAIN);
					echo '</p></div>';

				endif;

				if ($_REQUEST['wps_action'] == 'wps_alerts_clear_pending'):

					$sql = "DELETE FROM ".$wpdb->prefix."posts WHERE post_type='wps_alerts' and post_status = 'pending'";
					$wpdb->query($sql);

					echo '<div class="updated"><p>';
					echo __('Pending alerts removed (refresh your page).', WPS2_TEXT_DOMAIN);
					echo '</p></div>';

				endif;

			};

		endif;

		$nonce = wp_create_nonce( 'wps_alerts_clear' );
	    $views['wps-alerts-clear-sent'] = '<a onclick="return confirm(\''.__('Are you sure, this cannot be undone?', WPS2_TEXT_DOMAIN).'\')" id="wps_alerts_clear_sent" href="edit.php?post_type=wps_alerts&wps_action=wps_alerts_clear_sent&_wpnonce='.$nonce.'">'.__('Remove all sent alerts', WPS2_TEXT_DOMAIN).'</a>';
	    $views['wps-alerts-pending-sent'] = '<a onclick="return confirm(\''.__('Are you sure, this cannot be undone?', WPS2_TEXT_DOMAIN).'\')" id="wps_alerts_clear_sent" href="edit.php?post_type=wps_alerts&wps_action=wps_alerts_clear_pending&_wpnonce='.$nonce.'">'.__('Remove all pending alerts', WPS2_TEXT_DOMAIN).'</a>';
    	return $views;

    endif;
}




?>