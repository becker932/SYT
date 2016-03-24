<?php

// Add menu item(s) to WPS Pro Editor toolbar button
add_filter('wps_admin_shortcodes', 'wps_admin_shortcodes_add_usermeta', 10, 1);
function wps_admin_shortcodes_add_usermeta($items) {
	
	$item = array();
	$item['div'] = 'wps_admin_shortcodes_add_usermeta_dialog'; // DIV to show in dialog
	$item['label'] = __('User / Avatar', WPS2_TEXT_DOMAIN); // Shows on the menu
	$items['wps_usermeta'] = $item; // Unique ID

	return $items;

}

// Add dialog to show when menu item(s) are clicked on
add_action('wps_admin_shortcodes_dialog', 'wps_admin_shortcodes_add_usermeta_dialog');
function wps_admin_shortcodes_add_usermeta_dialog() {
	echo '<div id="wps_admin_shortcodes_add_usermeta_dialog" class="wps_admin_shortcodes_add_dialog" style="display:none;">';

		// List of shortcodes
    	echo '<p><select id="wps_admin_shortcodes_select_usermeta">';
    		echo '<option value="display_name">'.__('Show user display name', WPS2_TEXT_DOMAIN).'</option>';    		
    		echo '<option value="avatar">'.__('Show user avatar', WPS2_TEXT_DOMAIN).'</option>';    		
    		echo '<option value="usermeta">'.__('Show user information (meta)', WPS2_TEXT_DOMAIN).'</option>';    		
    		echo '<option value="usermeta_change">'.__('User edit profile', WPS2_TEXT_DOMAIN).'</option>';    		
    	echo '</select></p>';

    	// [wps-display-name]
    	echo '<div id="wps_admin_shortcodes_usermeta_display_name" class="wps_admin_shortcodes_dialog_div">';

    		echo '<p>'.__('Act as a hyperlink?', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<select id="wps_display_name_link">';
    			echo '<option value="1">'.__('Yes', WPS2_TEXT_DOMAIN).'</option>';
    			echo '<option value="0">'.__('No', WPS2_TEXT_DOMAIN).'</option>';
    		echo '</select></p>';

    		echo '<p>'.__('HTML before shortcode', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_display_name_before" style="width:100%" /></p>';

    		echo '<p>'.__('HTML after shortcode', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_display_name_after" style="width:100%" /></p>';

   			echo '<p><input id="insert_wps_display_name" type="button" class="button-primary" value="'.__('Insert shortcode', WPS2_TEXT_DOMAIN).'"></p>';

   		echo '</div>';

    	// [wps-avatar]
    	echo '<div id="wps_admin_shortcodes_usermeta_avatar" class="wps_admin_shortcodes_dialog_div" style="display:none">';

    		echo '<p>'.__('Size of avatar in pixels', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_avatar_size" /></p>';

    		echo '<p>'.__('Show link to change avatar?', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<select id="wps_avatar_change_link">';
    			echo '<option value="1">'.__('Yes', WPS2_TEXT_DOMAIN).'</option>';
    			echo '<option value="0">'.__('No', WPS2_TEXT_DOMAIN).'</option>';
    		echo '</select></p>';

    		echo '<p>'.__('HTML before shortcode', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_avatar_before" style="width:100%" /></p>';

    		echo '<p>'.__('HTML after shortcode', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_avatar_after" style="width:100%" /></p>';

   			echo '<p><input id="insert_wps_avatar" type="button" class="button-primary" value="'.__('Insert shortcode', WPS2_TEXT_DOMAIN).'"></p>';

   		echo '</div>';   

    	// [wps-usermeta]
    	echo '<div id="wps_admin_shortcodes_usermeta_usermeta" class="wps_admin_shortcodes_dialog_div" style="display:none">';

    		echo '<p>'.__('User meta field', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<select id="wps_usermeta_meta">';
    			echo '<option value="wpspro_home">'.__('Town/City', WPS2_TEXT_DOMAIN).'</option>';
    			echo '<option value="wpspro_country">'.__('Country', WPS2_TEXT_DOMAIN).'</option>';
    			echo '<option value="wpspro_map">'.__('Map', WPS2_TEXT_DOMAIN).'</option>';
    		echo '</select></p>';

    		echo '<p>'.__('Label to display', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_usermeta_label" /></p>';

    		echo '<p>'.__('Size of Google map in pixels (eg: 250,250)', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_usermeta_size" /></p>';

    		echo '<p>'.__('Zoom level of Google map', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_usermeta_zoom" /></p>';

    		echo '<p>'.__('HTML before shortcode', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_usermeta_before" style="width:100%" /></p>';

    		echo '<p>'.__('HTML after shortcode', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_usermeta_after" style="width:100%" /></p>';

   			echo '<p><input id="insert_wps_usermeta" type="button" class="button-primary" value="'.__('Insert shortcode', WPS2_TEXT_DOMAIN).'"></p>';

   		echo '</div>';   

    	// [wps-usermeta-change]
    	echo '<div id="wps_admin_shortcodes_usermeta_usermeta_change" class="wps_admin_shortcodes_dialog_div" style="display:none">';

    		echo '<p>'.__('Label for button', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_usermeta_change_label" /></p>';

    		echo '<p>'.__('Label for display name', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_usermeta_change_displayname" /></p>';

    		echo '<p>'.__('Label for email address', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_usermeta_change_email" /></p>';

    		echo '<p>'.__('Label for town/city', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_usermeta_change_town" /></p>';

    		echo '<p>'.__('Label for country', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_usermeta_change_country" /></p>';

    		echo '<p>'.__('Label for password', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_usermeta_change_password" /></p>';

    		echo '<p>'.__('Label for re-typing password', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_usermeta_change_password2" /></p>';

    		echo '<p>'.__('Label for logging in after changing password', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_usermeta_change_password_msg" /></p>';

    		echo '<p>'.__('CSS class for labels', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_usermeta_change_meta_class" /></p>';

    		echo '<p>'.__('CSS class for button', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_usermeta_change_class" /></p>';

    		echo '<p>'.__('HTML before shortcode', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_usermeta_change_before" style="width:100%" /></p>';

    		echo '<p>'.__('HTML after shortcode', WPS2_TEXT_DOMAIN).'<br />';
    		echo '<input type="text" id="wps_usermeta_change_after" style="width:100%" /></p>';

   			echo '<p><input id="insert_wps_usermeta_change" type="button" class="button-primary" value="'.__('Insert shortcode', WPS2_TEXT_DOMAIN).'"></p>';

   		echo '</div>';  


	echo '</div>';
}

// Javascript that re-acts to button(s) on dialog(s)
add_action( 'admin_head', 'wps_admin_shortcodes_add_usermeta_js' );
function wps_admin_shortcodes_add_usermeta_js() {
	$js = '

	jQuery(document).ready(function() {

		jQuery("#wps_admin_shortcodes_select_usermeta").change(function() {
			jQuery(".wps_admin_shortcodes_dialog_div").hide();
			jQuery("#wps_admin_shortcodes_usermeta_"+jQuery(this).val()).show();
		});

		// Display name
		jQuery("#insert_wps_display_name").click(function (event) {

			var link = jQuery("#wps_display_name_link").val();
			var before = jQuery("#wps_display_name_before").val().replace(/\"/g, "\'");
			var after = jQuery("#wps_display_name_after").val().replace(/\"/g, "\'");

			var code = "[wps-display-name";

			code += " link=\"1\"";
			if (before != "") code += " before=\""+before+"\"";
			if (after != "") code += " after=\""+after+"\"";

			code += "]";

			code = jQuery("<div/>").text(code).html();

			tinyMCE.activeEditor.insertContent(code);
			tb_remove();
		});

		// Avatar
		jQuery("#insert_wps_avatar").click(function (event) {

			var size = jQuery("#wps_avatar_size").val().replace(/px/g, "");
			var change_link = jQuery("#wps_avatar_change_link").val();
			var before = jQuery("#wps_avatar_before").val().replace(/\"/g, "\'");
			var after = jQuery("#wps_avatar_after").val().replace(/\"/g, "\'");

			var code = "[wps-avatar";

			if (size != "") code += " size=\""+size+"\"";
			code += " change_link=\""+change_link+"\"";
			if (before != "") code += " before=\""+before+"\"";
			if (after != "") code += " after=\""+after+"\"";

			code += "]";

			code = jQuery("<div/>").text(code).html();

			tinyMCE.activeEditor.insertContent(code);
			tb_remove();
		});

		// User meta
		jQuery("#insert_wps_usermeta").click(function (event) {

			var meta = jQuery("#wps_usermeta_meta").val();
			var label = jQuery("#wps_usermeta_label").val();
			var size = jQuery("#wps_usermeta_size").val().replace(/px/g, "");
			var zoom = jQuery("#wps_usermeta_zoom").val();
			var before = jQuery("#wps_usermeta_before").val().replace(/\"/g, "\'");
			var after = jQuery("#wps_usermeta_after").val().replace(/\"/g, "\'");

			var code = "[wps-usermeta";

			code += " meta=\""+meta+"\"";
			code += " label=\""+label+"\"";
			if (size != "") code += " size=\""+size+"\"";
			if (zoom != "") code += " zoom=\""+zoom+"\"";
			if (before != "") code += " before=\""+before+"\"";
			if (after != "") code += " after=\""+after+"\"";

			code += "]";

			code = jQuery("<div/>").text(code).html();

			tinyMCE.activeEditor.insertContent(code);
			tb_remove();
		});

		// User meta (edit profile)
		jQuery("#insert_wps_usermeta_change").click(function (event) {

			var label = jQuery("#wps_usermeta_change_label").val();
			var displayname = jQuery("#wps_usermeta_change_displayname").val();
			var email = jQuery("#wps_usermeta_change_email").val();
			var town = jQuery("#wps_usermeta_change_town").val();
			var country = jQuery("#wps_usermeta_change_country").val();
			var password = jQuery("#wps_usermeta_change_password").val();
			var password2 = jQuery("#wps_usermeta_change_password2").val();
			var password_msg = jQuery("#wps_usermeta_change_password_msg").val();
			var meta_class = jQuery("#wps_usermeta_change_meta_class").val();
			var x_class = jQuery("#wps_usermeta_change_class").val();
			var before = jQuery("#wps_usermeta_change_before").val().replace(/\"/g, "\'");
			var after = jQuery("#wps_usermeta_change_after").val().replace(/\"/g, "\'");

			var code = "[wps-usermeta-change";

			if (label != "") code += " label=\""+label+"\"";
			if (displayname != "") code += " displayname=\""+displayname+"\"";
			if (email != "") code += " email=\""+email+"\"";
			if (town != "") code += " town=\""+town+"\"";
			if (country != "") code += " country=\""+country+"\"";
			if (password != "") code += " password=\""+password+"\"";
			if (password2 != "") code += " password2=\""+password2+"\"";
			if (password_msg != "") code += " password_msg=\""+password_msg+"\"";
			if (meta_class != "") code += " meta_class=\""+meta_class+"\"";
			if (x_class != "") code += " x_class=\""+x_class+"\"";

			if (before != "") code += " before=\""+before+"\"";
			if (after != "") code += " after=\""+after+"\"";

			code += "]";

			code = jQuery("<div/>").text(code).html();

			tinyMCE.activeEditor.insertContent(code);
			tb_remove();
		});

	});';

	echo '<script type="text/javascript">' . $js . '</script>';
}


?>