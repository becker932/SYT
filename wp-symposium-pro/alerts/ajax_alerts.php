<?php
// AJAX functions for crowds
add_action( 'wp_ajax_wps_alerts_activity_redirect', 'wps_alerts_activity_redirect' ); 
add_action( 'wp_ajax_wps_alerts_make_all_read', 'wps_alerts_make_all_read' ); 
add_action( 'wp_ajax_wps_alerts_list_item_delete', 'wps_alerts_list_item_delete' ); 
add_action( 'wp_ajax_wps_alerts_delete_all', 'wps_alerts_delete_all' ); 

/* DELETE ALL ALERTS */
function wps_alerts_delete_all() {

	global $current_user;
	$args = array(
		'posts_per_page'   => -1,
		'orderby'          => 'post_date',
		'order'            => 'DESC',
		'post_type'        => 'wps_alerts',
		'post_status'      => array('publish', 'pending'),
		'meta_query' => array(
        	array(
        		'key' => 'wps_alert_recipient',
        		'value' => $current_user->user_login,
        		'compare' => '=='
        	)
        )
	);
	$alerts = get_posts($args);
	if ($alerts):
		foreach ($alerts as $alert):
			wp_delete_post($alert->ID, true);
		endforeach;
	endif;

	exit();
}

/* DELETE ALERT */
function wps_alerts_list_item_delete() {

    wp_delete_post($_POST['alert_id'], true);
	exit();

}

/* MARK ALERT AS READ */
function wps_alerts_activity_redirect() {

	update_post_meta( $_POST['alert_id'], 'wps_alert_read', true );
	echo $_POST['url'];
	exit();

}

/* MARK ALL ALERTS AS READ */
function wps_alerts_make_all_read() {

	global $current_user;
	$args = array(
		'posts_per_page'   => -1,
		'orderby'          => 'post_date',
		'order'            => 'DESC',
		'post_type'        => 'wps_alerts',
		'post_status'      => array('publish', 'pending'),
		'meta_query' => array(
        	array(
        		'key' => 'wps_alert_recipient',
        		'value' => $current_user->user_login,
        		'compare' => '=='
        	)
        )
	);
	$alerts = get_posts($args);
	if ($alerts):
		foreach ($alerts as $alert):
			update_post_meta( $alert->ID, 'wps_alert_read', true );
		endforeach;
	endif;

	exit();

}


?>
