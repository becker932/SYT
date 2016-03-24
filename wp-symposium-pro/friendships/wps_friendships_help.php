<?php

// Add to Getting Started information
add_action('wps_admin_getting_started_hook', 'wps_admin_getting_started_friendships');
function wps_admin_getting_started_friendships() {

  	echo '<div class="wps_admin_getting_started_menu_item" rel="wps_admin_getting_started_friendships">'.__('Friendships', WPS2_TEXT_DOMAIN).'</div>';

  	$display = isset($_POST['wps_expand']) && $_POST['wps_expand'] == 'wps_admin_getting_started_friendships' ? 'block' : 'none';
  	echo '<div class="wps_admin_getting_started_content" id="wps_admin_getting_started_friendships" style="display:'.$display.'">';

		?>
		<table class="form-table">
		<tr class="form-field">
			<td scope="row" valign="top">
				<label for="wps_friendships_all"><?php _e('Everybody friends', WPS2_TEXT_DOMAIN); ?></label>
			</td>
			<td>
				<input type="checkbox" style="width:10px" name="wps_friendships_all" <?php if (get_option('wps_friendships_all')) echo 'CHECKED'; ?> /> 
				<span class="description"><?php _e('Makes every user friends with everyone else, always. Good for private social networks.', WPS2_TEXT_DOMAIN); ?></span>
			</td>
		</tr> 
		</table>
        <?php
	echo '</div>';

}

add_action('wps_admin_setup_form_get_hook', 'wps_admin_friendships_save', 10, 2);
add_action('wps_admin_setup_form_save_hook', 'wps_admin_friendships_save', 10, 2);
function wps_admin_friendships_save($the_post) {

	if (isset($the_post['wps_friendships_all'])):
		update_option('wps_friendships_all', true);
	else:
		delete_option('wps_friendships_all');
	endif;


}

?>