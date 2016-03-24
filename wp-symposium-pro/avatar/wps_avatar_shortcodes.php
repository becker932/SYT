<?php

																	/* **** */
																	/* INIT */
																	/* **** */

function wps_avatar_init() {
	wp_enqueue_script("thickbox");
	wp_enqueue_style("thickbox");
	wp_enqueue_script('wps-avatar-js', plugins_url('wps_avatar.js', __FILE__), array('jquery'));	
	wp_enqueue_style('user-avatar', plugins_url('user-avatar.css', __FILE__), 'css');
	wp_enqueue_style('imgareaselect');
	wp_enqueue_script('imgareaselect');
	// Anything else?
	do_action('wps_avatar_init_hook');
}

																	/* ********** */
																	/* SHORTCODES */
																	/* ********** */

function wps_avatar($atts) {

	// Init
	add_action('wp_footer', 'wps_avatar_init');

	global $current_user;
	$html = '';

	// Shortcode parameters
	extract( shortcode_atts( array(
		'user_id' => false,
		'size' => 256,
		'change_link' => false,
        'profile_link' => false, // only if avatar is NOT current user
		'after' => '',
		'before' => '',
	), $atts, 'wps_avatar' ) );

    if (!$user_id) $user_id = wps_get_user_id();

	$friends = wps_are_friends($current_user->ID, $user_id);
	// By default same user, and friends of user, can see profile
	$user_can_see_profile = ($current_user->ID == $user_id || $friends['status'] == 'publish') ? true : false;
	$user_can_see_profile = apply_filters( 'wps_check_profile_security_filter', $user_can_see_profile, $user_id, $current_user->ID );
	
	if ($user_can_see_profile):

		if ($user_id != $current_user->ID) {
            if ($profile_link)
                $html .= '<a href="'.get_page_link(get_option('wpspro_profile_page')).'?user_id='.$user_id.'">';
			$html .= user_avatar_get_avatar( $user_id, $size );
            if ($profile_link)
                $html .= '</a>';
		} else {
			$profile = get_user_by('id', $user_id);
			global $current_user;
			
			$html .= '<div class="wps_avatar">';
			$html .= user_avatar_get_avatar( $user_id, $size );
			if ($change_link) $html .= '<a id="user-avatar-link" style="text-decoration: none;opacity:0.7;background-color: #000; color:#fff !important; padding: 3px 8px 3px 8px; position:absolute; bottom:18px; left: 10px;" href="'.get_page_link(get_option('wpspro_change_avatar_page')).'?user_id='.$user_id.'&action=change_avatar" title="'.__('Upload and Crop an Image to be Displayed', WPS2_TEXT_DOMAIN).'" >'.__('Change Picture', WPS2_TEXT_DOMAIN).'</a>';
			$html .= '</div>';
		}

	endif;

	if ($html) $html = htmlspecialchars_decode($before).$html.htmlspecialchars_decode($after);
	return $html;

}


function wps_avatar_change_link($atts) {

	// Init
	add_action('wp_footer', 'wps_avatar_init');

	global $current_user;
	$html = '';

	if (is_user_logged_in()) {

		// Shortcode parameters
		extract( shortcode_atts( array(
			'text' => __('Change Picture', WPS2_TEXT_DOMAIN),
			'after' => '',
			'before' => '',
		), $atts, 'wps_avatar_change' ) );

		$user_id = wps_get_user_id();

		if ($current_user->ID == $user_id)
			$html .= '<a href="'.get_page_link(get_option('wpspro_change_avatar_page')).'?user_id='.$user_id.'">'.$text.'</a>';

	}

	if ($html) $html = htmlspecialchars_decode($before).$html.htmlspecialchars_decode($after);
	return $html;

}

function wps_avatar_change($atts) {

	// Init
	add_action('wp_footer', 'wps_avatar_init');

	global $current_user;
	$html = '';

	if (is_user_logged_in()):

		// Shortcode parameters
		extract( shortcode_atts( array(
			'label' => 'Upload',
            'crop' => true,
			'choose' => __('Click here to choose an image...', WPS2_TEXT_DOMAIN),
			'try_again_msg' => __('Try again...', WPS2_TEXT_DOMAIN),
			'file_types_msg' => __("Please upload an image file (.jpeg, .gif, .png).", WPS2_TEXT_DOMAIN),
			'not_permitted' => __('You are not allowed to change this avatar.', WPS2_TEXT_DOMAIN)
		), $atts, 'wps_avatar' ) );

		if (isset($_POST['user_id'])):
			$user_id = $_POST['user_id'];
		else:
			$user_id = wps_get_user_id();
		endif;

		if ($current_user->ID == $user_id || current_user_can('manage_options') || is_super_admin($current_user->ID) ):

			include_once ABSPATH . 'wp-admin/includes/media.php';
			include_once ABSPATH . 'wp-admin/includes/file.php';
			include_once ABSPATH . 'wp-admin/includes/image.php';

			if (!isset($_POST['wps_avatar_change_step'])):

				$html .= '<form enctype="multipart/form-data" id="avatarUploadForm" method="POST" action="#" >';
					$html .= '<input type="hidden" name="wps_avatar_change_step" value="2" />';
					$html .= '<input type="hidden" name="user_id" value="'.$user_id.'" />';
					$html .= '<input title="'.$choose.'" type="file" id="avatar_file_upload" name="uploadedfile" style="display:none" /><br /><br />';
					wp_nonce_field('user-avatar');
					$html .= '<input type="submit" class="wps_submit" value="'.$label.'" />';
				$html .= '</form>';

			elseif ($_POST['wps_avatar_change_step'] == '2' && $crop):

				if (!(($_FILES["uploadedfile"]["type"] == "image/gif") || ($_FILES["uploadedfile"]["type"] == "image/jpeg") || ($_FILES["uploadedfile"]["type"] == "image/png") || ($_FILES["uploadedfile"]["type"] == "image/pjpeg") || ($_FILES["uploadedfile"]["type"] == "image/x-png"))):
					
					$html .= "<div class='wps_error'>".$file_types_msg."</div>";
					$html .= "<p><a href=''>".$try_again_msg.'</a></p>';

				else:

					$overrides = array('test_form' => false);
					$file = wp_handle_upload($_FILES['uploadedfile'], $overrides);

					if ( isset($file['error']) ){
						die( $file['error'] );
					}
					
					$url = $file['url'];
					$type = $file['type'];
					$file = $file['file'];
					$filename = basename($file);
					
					set_transient( 'avatar_file_'.$user_id, $file, 60 * 60 * 5 );
					// Construct the object array
					$object = array(
					'post_title' => $filename,
					'post_content' => $url,
					'post_mime_type' => $type,
					'guid' => $url);

					// Save the data
					list($width, $height, $type, $attr) = getimagesize( $file );
					
					if ( $width > 420 ) {
						$oitar = $width / 420;
						$image = wp_crop_image($file, 0, 0, $width, $height, 420, $height / $oitar, false, str_replace(basename($file), 'midsize-'.basename($file), $file));

						$url = str_replace(basename($url), basename($image), $url);
						$width = $width / $oitar;
						$height = $height / $oitar;
					} else {
						$oitar = 1;
					}
					
					$html .= '<form id="iframe-crop-form" method="POST" action="#">';
					$html .= '<input type="hidden" name="wps_avatar_change_step" value="3" />';
					$html .= '<input type="hidden" name="user_id" value="'.$user_id.'" />';					

					$html .= '<div style="margin-bottom:20px">';
					$html .= '<img src="'.$url.'" id="wps_upload" width="'.esc_attr($width).'" height="'.esc_attr($height).'" />';
					$html .= '</div>';
					
					$html .= '<div id="wps_preview" style="float: left; width: 150px; height: 150px; overflow: hidden;">';
					$html .= '<img src="'.esc_url_raw($url).'" width="'.esc_attr($width).'" height="'.esc_attr($height).'" style="max-width:none" />';
					$html .= '</div>';
					
					$html .= '<input type="hidden" name="x1" id="x1" value="0" />';
					$html .= '<input type="hidden" name="y1" id="y1" value="0" />';
					$html .= '<input type="hidden" name="x2" id="x2" />';
					$html .= '<input type="hidden" name="y2" id="y2" />';
					$html .= '<input type="hidden" name="width" id="width" value="'.esc_attr($width).'" />';
					$html .= '<input type="hidden" name="height" id="height" value="'.esc_attr($height).'" />';
					$html .= '<input type="hidden" id="init_width" value="'.esc_attr($width).'" />';
					$html .= '<input type="hidden" id="init_height" value="'.esc_attr($height).'" />';
					
					$html .= '<input type="hidden" name="oitar" id="oitar" value="'.esc_attr($oitar).'" />';
					wp_nonce_field('user-avatar');
					$html .= '<input type="submit" class="wps_submit" style="margin-left:20px;" id="user-avatar-crop-button" value="'.__('Crop', WPS2_TEXT_DOMAIN).'" />';
					$html .= '</form>';

				endif;

			else:

                if (isset($_POST['oitar'])):
    
                    // Doing crop
    
                    if ( $_POST['oitar'] > 1 ):
                        $_POST['x1'] = $_POST['x1'] * $_POST['oitar'];
                        $_POST['y1'] = $_POST['y1'] * $_POST['oitar'];
                        $_POST['width'] = $_POST['width'] * $_POST['oitar'];
                        $_POST['height'] = $_POST['height'] * $_POST['oitar'];
                    endif;

                    $original_file = get_transient( 'avatar_file_'.$user_id );
                    delete_transient('avatar_file_'.$user_id );

                    if( !file_exists($original_file) ):

                        $html .= "<div class='error'><p>". __('Sorry, no file available', WPS2_TEXT_DOMAIN)."</p></div>";

                    else:

                        // Create avatar folder if not already existing
                        $continue = true;
                        if( !file_exists(WP_CONTENT_DIR."/wps-pro-content/members/".$user_id."/avatar/") ):
    
                            if (!mkdir(WP_CONTENT_DIR."/wps-pro-content/members/".$user_id."/avatar/", 0777 ,true)):
                                $error = error_get_last();
                                $html .= $error['message'].'<br />';
                                $html .= WP_CONTENT_DIR."/wps-pro-content/members/".$user_id."/avatar/<br>";
                                $continue = false;
                            else:
                                $path = WP_CONTENT_DIR."/wps-pro-content/members/".$user_id."/avatar/";
                                $cropped_full = $path.time()."-wpsfull.jpg";
                                $cropped_thumb = $path.time()."-wpsthumb.jpg";
                            endif;
                        else:
                            $cropped_full = user_avatar_core_avatar_upload_path($user_id).time()."-wpsfull.jpg";
                            $cropped_thumb = user_avatar_core_avatar_upload_path($user_id).time()."-wpsthumb.jpg";
                        endif;

                        if ($continue):

                            // delete the previous files
                            user_avatar_delete_files($user_id);
                            @mkdir(WP_CONTENT_DIR."/wps-pro-content/members/".$user_id."/avatar/", 0777 ,true);

                            if (!class_exists('SimpleImage')) require_once('SimpleImage.php');

                            // update the files 
                            $img = $original_file;
	                            $image = new SimpleImage();
	                            $image->load($img);
	                            $image->cut($_POST['x1'], $_POST['y1'], $_POST['width'], $_POST['height']);
	                            $image->save($cropped_full);
    
                            $img = $original_file;
	                            $image = new SimpleImage();
	                            $image->load($img);
	                            $image->cut($_POST['x1'], $_POST['y1'], $_POST['width'], $_POST['height']);
	                            $image->save($cropped_thumb);
    
                            if ( is_wp_error( $cropped_full ) ):
                                $html .= __( 'Image could not be processed. Please try again.', WPS2_TEXT_DOMAIN);	
                                var_dump($cropped_full);	
                            else:
                                /* Remove the original */
                                @unlink( $original_file );
                                $html .= '<script>window.location.replace("'.get_page_link(get_option('wpspro_profile_page')).'?user_id='.$user_id.'");</script>';
                            endif;

                        endif;

                    endif;
    
                else:
    
                    // Skip crop
    
					$overrides = array('test_form' => false);
					$file = wp_handle_upload($_FILES['uploadedfile'], $overrides);

					if ( isset($file['error']) ){
						die( $file['error'] );
					}

					$url = $file['url'];
					$type = $file['type'];
					$original_file = $file['file'];
					$filename = basename($original_file);

                    if( !file_exists($original_file) ):

                        $html .= "<div class='error'><p>". __('Sorry, no file available', WPS2_TEXT_DOMAIN)."</p></div>";

                    else:

                        // Create avatar folder if not already existing
                        $continue = true;
                        if( !file_exists(WP_CONTENT_DIR."/wps-pro-content/members/".$user_id."/avatar/") ):
                            if (!mkdir(WP_CONTENT_DIR."/wps-pro-content/members/".$user_id."/avatar/", 0777 ,true)):
                                $error = error_get_last();
                                $html .= $error['message'].'<br />';
                                $html .= WP_CONTENT_DIR."/wps-pro-content/members/".$user_id."/avatar/<br>";
                                $continue = false;
                            else:
                                $path = WP_CONTENT_DIR."/wps-pro-content/members/".$user_id."/avatar/";
                                $cropped_full = $path.time()."-wpsfull.jpg";
                                $cropped_thumb = $path.time()."-wpsthumb.jpg";
                            endif;
                        else:
                            $cropped_full = user_avatar_core_avatar_upload_path($user_id).time()."-wpsfull.jpg";
                            $cropped_thumb = user_avatar_core_avatar_upload_path($user_id).time()."-wpsthumb.jpg";
                        endif;

                        if ($continue):

                            // delete the previous files
                            user_avatar_delete_files($user_id);

                            // update the files 
                            list($width, $height, $type, $attr) = getimagesize( $original_file );    
                            $cropped_full = wp_crop_image( $original_file, 0, 0, $width, $height, 300, 300, false, $cropped_full );
                            $cropped_thumb = wp_crop_image( $original_file, 0, 0, $width, $height, 300, 300, false, $cropped_thumb );

                            if ( is_wp_error( $cropped_full ) ):
                                $html .= __( 'Image could not be processed. Please try again.', WPS2_TEXT_DOMAIN);	
                                var_dump($cropped_full);	
                            else:
                                /* Remove the original */
                                @unlink( $original_file );
                                $html .= '<script>window.location.replace("'.get_page_link(get_option('wpspro_profile_page')).'?user_id='.$user_id.'");</script>';
                            endif;

                        endif;

                    endif;
    
                endif;


			endif;

		else:

			$html .= $not_permitted;

		endif;

	endif;
	
	return $html;
}



add_shortcode(WPS_PREFIX.'-avatar', 'wps_avatar');
add_shortcode(WPS_PREFIX.'-avatar-change-link', 'wps_avatar_change_link');
add_shortcode(WPS_PREFIX.'-avatar-change', 'wps_avatar_change');
?>