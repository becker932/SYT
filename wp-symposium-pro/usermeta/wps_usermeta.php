<?php 

add_action('show_user_profile', 'wps_usermeta_form');
add_action('edit_user_profile', 'wps_usermeta_form');

add_action( 'personal_options_update', 'wps_usermeta_form_save' );
add_action( 'edit_user_profile_update', 'wps_usermeta_form_save' );

add_action( 'wp_ajax_wps_deactivate_account', 'wps_deactivate_account' ); 

add_filter('authenticate', 'wps_check_login', 30, 3);

/* CHECK IF CLOSED ON WP LOGIN */
function wps_check_login($user, $username, $password) {
    
    $return = $user;
    if ($username && wps_is_account_closed($user->ID)) $return = new WP_Error('wps_login_fail', __('This account is closed.', WPS2_TEXT_DOMAIN));
        
    return $return;
    
}

/* DE-ACTIVATE (CLOSE) ACCOUNT */
function wps_deactivate_account($id=false) {

    global $wpdb;
    $user_id = $id ? $id : $_POST['user_id'];
    
    if ($user_id):
    
        // get the user
        $get_the_user = get_user_by('id', $user_id);
        // remove user email
        $update_user = wp_update_user( array(
            'ID'            => $user_id,
            'user_pass'     => wp_generate_password( 12, false ),
            'user_email'    => $get_the_user->user_login.'@example.com',
            'display_name'  => $get_the_user->user_login,
			'nickname'      => $get_the_user->user_login,
			'first_name'    => '',
			'last_name'     => ''
        ));
        // remove avatar
        user_avatar_delete_files($get_the_user->ID);
        // remove WPS meta
        $sql = "DELETE FROM ".$wpdb->base_prefix."usermeta WHERE user_id = %d and meta_key like 'wps_%%'";
        $wpdb->query($wpdb->prepare($sql, $get_the_user->ID));
        $sql = "DELETE FROM ".$wpdb->base_prefix."usermeta WHERE user_id = %d and meta_key like 'wpspro_%%'";
        $wpdb->query($wpdb->prepare($sql, $get_the_user->ID));
        // set as closed
        global $current_user;
        $info = array (
            'date' => current_time('mysql', 1),
            'user_id' => $get_the_user->ID,
            'user_login' => $get_the_user->user_login,
            'client_ip' => $_SERVER['REMOTE_ADDR']
        );
        update_user_meta($get_the_user->ID, 'wps_account_closed', $info);
        // logout (if being closed by the user)
        if (!$id):
            wp_logout();
        else:
            wp_redirect(admin_url('user-edit.php?user_id='.$get_the_user->ID));    
        endif;
    
    endif;

	exit;
}

function wps_usermeta_form($user)
{

	global $current_user;
	
	// Check if it is current user or super admin role
	if( $user->ID == $current_user->ID || current_user_can('edit_user', $current_user->ID) || is_super_admin($current_user->ID) )
	{
		?>

		<h3><?php _e('WP Symposium Pro', WPS2_TEXT_DOMAIN); ?></h3>

		<table class="form-table">

            <?php if (current_user_can('manage_options')): ?>

                <?php if (!wps_is_account_closed($user->ID)): ?>

                    <tr>
                        <th><label for="wps_close_account"><?php _e('Close account', WPS2_TEXT_DOMAIN); ?></label></th>
                        <td>
                            <input type="checkbox" name="wps_close_account" id="wps_close_account" />
                            <span class="description"><?php _e('All personal information will be deleted, this cannot be undone.', WPS2_TEXT_DOMAIN); ?></span>
                        </td>
                    </tr>

                <?php else: ?>

                    <tr>
                        <th><label for="wps_reopen_account"><?php _e('Re-open account', WPS2_TEXT_DOMAIN); ?></label></th>
                        <td>
                            <input type="checkbox" name="wps_reopen_account" id="wps_reopen_account" />
                            <span class="description"><?php _e('You can optionally set the password, email address, etc, above before saving.', WPS2_TEXT_DOMAIN); ?></span>
                        </td>
                    </tr>

                <?php endif; ?>

            <?php endif; ?>

			<tr>
				<th><label for="wpspro_home"><?php _e('Town/City', WPS2_TEXT_DOMAIN); ?></label></th>
				<td>
					<input type="text" name="wpspro_home" id="wpspro_home" value="<?php echo esc_attr( get_the_author_meta( 'wpspro_home', $user->ID ) ); ?>" class="regular-text" /><br />
					<span class="description"><?php _e('Please enter your town or city.', WPS2_TEXT_DOMAIN); ?></span>
				</td>
			</tr>

			<tr>
				<th><label for="wpspro_country"><?php _e('Country', WPS2_TEXT_DOMAIN); ?></label></th>
				<td>
					<input type="text" name="wpspro_country" id="wpspro_country" value="<?php echo esc_attr( get_the_author_meta( 'wpspro_country', $user->ID ) ); ?>" class="regular-text" /><br />
					<span class="description"><?php _e('Please enter your country.', WPS2_TEXT_DOMAIN); ?></span>
				</td>
			</tr>

		</table>

		<?php

	}
	
} 

function wps_usermeta_form_save( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	update_user_meta($user_id, 'wpspro_home', $_POST['wpspro_home']);
	update_user_meta($user_id, 'wpspro_country', $_POST['wpspro_country']);

	if ($_POST['wpspro_home'] && $_POST['wpspro_country']):

		// Change spaces to %20 for Google maps API and geo-code
		$city = str_replace(' ','%20',$_POST['wpspro_home']);
		$country = str_replace(' ','%20',$_POST['wpspro_country']);
		$fgc = 'http://maps.googleapis.com/maps/api/geocode/json?address='.$city.'+'.$country.'&sensor=false';

		if ($json = @file_get_contents($fgc) ):
			$json_output = json_decode($json, true);
			$lat = $json_output['results'][0]['geometry']['location']['lat'];
			$lng = $json_output['results'][0]['geometry']['location']['lng'];

			update_user_meta($user_id, 'wpspro_lat', $lat);
			update_user_meta($user_id, 'wpspro_long', $lng);
		endif;

	endif;

    // This must be last
    if (current_user_can('manage_options')):
        if (isset($_POST['wps_close_account'])) wps_deactivate_account($user_id);
        if (isset($_POST['wps_reopen_account'])) delete_user_meta($user_id, 'wps_account_closed');
    endif;
        
}


?>