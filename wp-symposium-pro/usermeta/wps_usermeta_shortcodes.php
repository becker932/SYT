<?php

																	/* **** */
																	/* INIT */
																	/* **** */

function wps_usermeta_init() {
    
	// JS and CSS
	wp_enqueue_script('wps-usermeta-js', plugins_url('wps_usermeta.js', __FILE__), array('jquery'));	
	wp_enqueue_style('wps-usermeta-css', plugins_url('wps_usermeta.css', __FILE__), 'css');
	wp_localize_script('wps-usermeta-js', 'wps_usermeta', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ));    	
	// Anything else?
	do_action('wps_usermeta_init_hook');

}


																	/* ********** */
																	/* SHORTCODES */
																	/* ********** */

function wps_usermeta_button($atts) {

	// Init
	add_action('wp_footer', 'wps_usermeta_init');

	global $current_user;
	$html = '';

	// Shortcode parameters
	extract( shortcode_atts( array(
		'user_id' => false,
		'url' => '',
		'value' => __('Go', WPS2_TEXT_DOMAIN),
		'class' => '',
		'after' => '',
		'before' => '',
	), $atts, 'wps_usermeta_button' ) );

	if (!$user_id) $user_id = wps_get_user_id();

	if (!$url):

		$html .= '<div class="wps_error">'.__('Please set URL option in the shortcode.', WPS2_TEXT_DOMAIN).'</div>';

	else:

		$html .= '<form action="" method="POST">';
		$url .= wps_query_mark($url).'user_id='.$user_id;
		$html .= '<input class="wps_user_button" rel="'.$url.'" type="submit" class="wps_submit '.$class.'" value="'.$value.'" />';
		$html .= '</form>';

	endif;

	if ($html) $html = htmlspecialchars_decode($before).$html.htmlspecialchars_decode($after);

	return $html;	
}

function wps_usermeta($atts) {

	// Init
	add_action('wp_footer', 'wps_usermeta_init');

	global $current_user;
	$html = '';

	// Shortcode parameters
	extract( shortcode_atts( array(
		'user_id' => false,
		'meta' => 'wpspro_home',
		'label' => '',
		'size' => '250,250',
		'map_style' => 'dynamic',
		'zoom' => 5,
		'after' => '',
		'before' => '',
	), $atts, 'wps_usermeta' ) );
	$size = explode(',', $size);

	if (!$user_id) $user_id = wps_get_user_id();

	$friends = wps_are_friends($current_user->ID, $user_id);
	// By default same user, and friends of user, can see profile
	$user_can_see_profile = ($current_user->ID == $user_id || $friends['status'] == 'publish') ? true : false;
	$user_can_see_profile = apply_filters( 'wps_check_profile_security_filter', $user_can_see_profile, $user_id, $current_user->ID );

	if ($user_can_see_profile):

		$user = get_user_by('id', $user_id);
		if ($user):
			if ($meta != 'wpspro_map'):
				if ($value = get_user_meta( $user_id, $meta, true )):
					if ($label) $html .= '<span class="wps_usermeta_label">'.$label.'</span> ';
					$html .= $value;
				else:
					if ($value = get_user_meta( $user_id, 'wps_'.$meta, true )):
						// Filter for value
						$value = apply_filters( 'wps_usermeta_value_filter', $value, $atts, $user_id );
						$html .= $value;
					endif;
				endif;
			else:
				$city = get_user_meta( $user_id, 'wpspro_home', true );
				$country = get_user_meta( $user_id, 'wpspro_country', true );
				if ($city && $country):
					if ($map_style == "static"):
						$html .= '<a target="_blank" href="http://maps.google.co.uk/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q='.$city.',+'.$country.'&amp;ie=UTF8&amp;hq=&amp;hnear='.$city.',+'.$country.'&amp;output=embed&amp;z=5" alt="Click on map to enlarge" title="Click on map to enlarge">';
						$html .= '<img src="http://maps.google.com/maps/api/staticmap?center='.$city.',.+'.$country.'&size='.$size[0].'x'.$size[1].'&zoom='.$zoom.'&maptype=roadmap&markers=color:blue|label:&nbsp;|'.$city.',+'.$country.'&sensor=false" />';
						$html .= "</a>";
					else:
						$html .= "<iframe width='".$size[0]."' height='".$size[1]."' frameborder='0' scrolling='no' marginheight='0' marginwidth='0' src='https://maps.google.co.uk/maps?q=".$city.",+".$country."&amp;z=".$zoom."&amp;output=embed&amp;iwloc=near'></iframe>";
					endif;

				endif;

			endif;
		endif;

	endif;

	if ($html) $html = htmlspecialchars_decode($before).$html.htmlspecialchars_decode($after);

	return $html;

}

function wps_usermeta_change($atts) {

	// Init
	add_action('wp_footer', 'wps_usermeta_init');

	global $current_user;
	$html = '';

	if (is_user_logged_in()) {

		// Shortcode parameters
		extract( shortcode_atts( array(
			'meta_class' => 'wps_usermeta_change_label',
			'user_id' => 0,
			'class' => '',
			'label' => __('Update', WPS2_TEXT_DOMAIN),
			'town' => __('Town/City', WPS2_TEXT_DOMAIN),
			'country' => __('Country', WPS2_TEXT_DOMAIN),
			'displayname' => __('Display Name', WPS2_TEXT_DOMAIN),
			'password' => __('Change your password', WPS2_TEXT_DOMAIN),
			'password2' => __('Re-type your password', WPS2_TEXT_DOMAIN),
			'password_msg' => __('Password changed, please log in again.', WPS2_TEXT_DOMAIN),
			'email' => __('Email address', WPS2_TEXT_DOMAIN),
			'after' => '',
			'before' => '',

		), $atts, 'wps_usermeta' ) );
		
		if (!$user_id)
			$user_id = wps_get_user_id();

		$user_can_see_profile = ($current_user->ID == $user_id || current_user_can('manage_options')) ? true : false;

		if ($user_can_see_profile):

			// Update if POSTing
			if (isset($_POST['wps_usermeta_change_update'])):

				if ($display_name = $_POST['wpspro_display_name'])
					wp_update_user( array ( 'ID' => $user_id, 'display_name' => $display_name ) ) ;

				if ($user_email = $_POST['wpspro_email'])
					wp_update_user( array ( 'ID' => $user_id, 'user_email' => $user_email ) ) ;

				update_user_meta( $user_id, 'wpspro_home', $_POST['wpsro_home']);
				update_user_meta( $user_id, 'wpspro_country', $_POST['wpspro_country']);

				if (isset($_POST['wpspro_password']) && $_POST['wpspro_password'] != ''):
					$pw = $_POST['wpspro_password'];
					wp_set_password($pw, $user_id);
					$html .= '<div class="wps_success password_msg">'.$password_msg.'</div>';
				endif;
        
				do_action( 'wps_usermeta_change_hook', $user_id, $atts, $_POST, $_FILES );

			endif;

			// Show form
			$form_html = '';
			if (!isset($_POST['wpspro_password']) || $_POST['wpspro_password'] == ''):

				$form_html .= '<form enctype="multipart/form-data" id="wps_usermeta_change" action="#" method="POST">';

					$form_html .= '<input type="hidden" name="wps_usermeta_change_update" value="yes" />';

					$the_user = get_user_by('id', $user_id);

					$value = isset($_POST['wpspro_display_name']) ? stripslashes($_POST['wpspro_display_name']) : $the_user->display_name;
						$form_html .= '<div class="wps_usermeta_change_item">';
						$form_html .= '<div class="'.$meta_class.'">'.$displayname.'</div>';
						$form_html .= '<input type="text" id="wpspro_display_name" name="wpspro_display_name" value="'.$value.'" />';
						$form_html .= '</div>';

					$value = isset($_POST['wpspro_email']) ? $_POST['wpspro_email'] : $the_user->user_email;
						$form_html .= '<div class="wps_usermeta_change_item">';
						$form_html .= '<div class="'.$meta_class.'">'.$email.'</div>';
						$form_html .= '<input type="text" id="wpspro_email" name="wpspro_email" style="width:250px" value="'.$value.'" />';
						$form_html .= '</div>';

					$value = get_user_meta( $user_id, 'wpspro_home', true );
						$form_html .= '<div id="wpspro_home" class="wps_usermeta_change_item">';
						$form_html .= '<div class="'.$meta_class.'">'.$town.'</div>';
						$form_html .= '<input type="text" id="wpspro_home" name="wpsro_home" value="'.$value.'" />';
						$form_html .= '</div>';

					$value = get_user_meta( $user_id, 'wpspro_country', true );
						$form_html .= '<div id="wpspro_country" class="wps_usermeta_change_item">';
						$form_html .= '<div class="'.$meta_class.'">'.$country.'</div>';
						$form_html .= '<input type="text" id="wpspro_country" name="wpspro_country" value="'.$value.'" />';
						$form_html .= '</div>';

					// Password change
						$form_html .= '<div class="wps_usermeta_change_item">';
						$form_html .= '<div class="'.$meta_class.'">'.$password.'</div>';
						$form_html .= '<input type="password" name="wpspro_password" id="wpspro_password" />';
						$form_html .= '<div class="'.$meta_class.'">'.$password2.'</div>';
						$form_html .= '<input type="password" name="wpspro_password2" id="wpspro_password2" />';
						$form_html .= '</div>';

					// Anything else?
					$form_html = apply_filters( 'wps_usermeta_change_filter', $form_html, $atts, $user_id );

					$form_html .= '<input type="submit" id="wps_usermeta_change_submit" class="wps_submit '.$class.'" value="'.$label.'" />';

				$form_html .= '</form>';

			endif;

			$html .= $form_html;

		endif;

		if ($html) $html = htmlspecialchars_decode($before).$html.htmlspecialchars_decode($after);

	}

	return $html;

}

function wps_usermeta_change_link($atts) {

	// Init
	add_action('wp_footer', 'wps_usermeta_init');

	global $current_user;
	$html = '';

	if (is_user_logged_in()) {

		// Shortcode parameters
		extract( shortcode_atts( array(
			'text' => __('Edit Profile', WPS2_TEXT_DOMAIN),
			'user_id' => 0,
			'after' => '',
			'before' => '',
		), $atts, 'wps_usermeta_change_link' ) );

		if (!$user_id)
			$user_id = wps_get_user_id();

		if ($current_user->ID == $user_id || current_user_can('manage_options')):
			$url = get_page_link(get_option('wpspro_edit_profile_page'));
			$html .= '<a href="'.$url.wps_query_mark($url).'user_id='.$user_id.'">'.$text.'</a>';
		endif;

		if ($html) $html = htmlspecialchars_decode($before).$html.htmlspecialchars_decode($after);

	}

	return $html;

}

function wps_close_account($atts) {

	// Init
	add_action('wp_footer', 'wps_usermeta_init');

	global $current_user;
	$html = '';

	if (is_user_logged_in()) {
        
		// Shortcode parameters
		extract( shortcode_atts( array(
			'class' => '',
			'label' => __('Close account', WPS2_TEXT_DOMAIN),
			'are_you_sure_text' => __('Are you sure? You cannot re-open a closed account.', WPS2_TEXT_DOMAIN),
			'logout_text' => __('Your account has been closed.', WPS2_TEXT_DOMAIN),
            'url' => '/', // set URL to go to after de-activation, probably a logout page, or '' for current page
			'after' => '',
			'before' => '',

		), $atts, 'wps_usermeta' ) );
		
        $user_id = wps_get_user_id();
        if ($user_id == $current_user->ID || current_user_can('manage_options')):

            $html .= '<input type="button" data-sure="'.$are_you_sure_text.'" data-url="'.$url.'" data-logout="'.$logout_text.'" id="wps_close_account" data-user="'.$user_id.'" value="'.$label.'" />';

            if ($html) $html = htmlspecialchars_decode($before).$html.htmlspecialchars_decode($after);

        endif;
        
            
    }

    return $html;
}

add_shortcode(WPS_PREFIX.'-usermeta', 'wps_usermeta');
add_shortcode(WPS_PREFIX.'-usermeta-change', 'wps_usermeta_change');
add_shortcode(WPS_PREFIX.'-usermeta-change-link', 'wps_usermeta_change_link');
add_shortcode(WPS_PREFIX.'-usermeta-button', 'wps_usermeta_button');
add_shortcode(WPS_PREFIX.'-close-account', 'wps_close_account');

?>
