<?php
// Quick Start
add_action('wps_admin_quick_start_hook', 'wps_admin_quick_start_profile');
function wps_admin_quick_start_profile() {

	echo '<div style="margin-right:10px; float:left">';
	echo '<form action="" method="POST">';
	echo '<input type="hidden" name="wpspro_quick_start" value="profile" />';
	echo '<input type="submit" class="button-secondary" value="'.__('Add Profile Pages', WPS2_TEXT_DOMAIN).'" />';
	echo '</form></div>';
}

add_action('wps_admin_quick_start_form_save_hook', 'wps_admin_quick_start_profile_save', 10, 1);
function wps_admin_quick_start_profile_save($the_post) {

	if (isset($the_post['wpspro_quick_start']) && $the_post['wpspro_quick_start'] == 'profile'):

		// Profile Page
		$post_content = '['.WPS_PREFIX.'-activity-page]';

		$post = array(
		  'post_content'   => $post_content,
		  'post_name'      => 'profile',
		  'post_title'     => __('Profile', WPS2_TEXT_DOMAIN),
		  'post_status'    => 'publish',
		  'post_type'      => 'page',
		  'ping_status'    => 'closed',
		  'comment_status' => 'closed',
		);  

		$new_id = wp_insert_post( $post );
		update_option('wpspro_profile_page', $new_id);

		// Edit Profile Page
		$post = array(
		  'post_content'   => '['.WPS_PREFIX.'-usermeta-change]',
		  'post_name'      => 'edit-profile',
		  'post_title'     => __('Edit Profile', WPS2_TEXT_DOMAIN),
		  'post_status'    => 'publish',
		  'post_type'      => 'page',
		  'ping_status'    => 'closed',
		  'comment_status' => 'closed',
		);  

		$new_edit_profile_id = wp_insert_post( $post );
		update_option('wpspro_edit_profile_page', $new_edit_profile_id);

		// Change Avatar Page
		$post = array(
		  'post_content'   => '['.WPS_PREFIX.'-avatar-change]',
		  'post_name'      => 'change-avatar',
		  'post_title'     => __('Change Avatar', WPS2_TEXT_DOMAIN),
		  'post_status'    => 'publish',
		  'post_type'      => 'page',
		  'ping_status'    => 'closed',
		  'comment_status' => 'closed',
		);  

		$new_change_avatar_id = wp_insert_post( $post );
		update_option('wpspro_change_avatar_page', $new_change_avatar_id);		

		// Friends Page
		$post = array(
		  'post_content'   => '['.WPS_PREFIX.'-friends-pending]['.WPS_PREFIX.'-friends count="100"]',
		  'post_name'      => 'friends',
		  'post_title'     => __('Friends', WPS2_TEXT_DOMAIN),
		  'post_status'    => 'publish',
		  'post_type'      => 'page',
		  'ping_status'    => 'closed',
		  'comment_status' => 'closed',
		);  

		$new_friends_id = wp_insert_post( $post );

		echo '<div class="wps_success">'.sprintf(__('Profile Page (%s) added. [<a href="%s">view</a>]', WPS2_TEXT_DOMAIN), get_permalink($new_id), get_permalink($new_id)).'<br />';
		echo sprintf(__('Edit Profile Page (%s) added. [<a href="%s">view</a>]', WPS2_TEXT_DOMAIN), get_permalink($new_edit_profile_id), get_permalink($new_edit_profile_id)).'<br />';
		echo sprintf(__('Change Avatar Page (%s) added. [<a href="%s">view</a>]', WPS2_TEXT_DOMAIN), get_permalink($new_change_avatar_id), get_permalink($new_change_avatar_id)).'<br />';
		echo sprintf(__('Friends Page (%s) added. [<a href="%s">view</a>]', WPS2_TEXT_DOMAIN), get_permalink($new_friends_id), get_permalink($new_friends_id)).'<br /><br />';

		echo sprintf(__('<strong>Do not add again, you will create multiple pages!</strong><br />You might want to add them to your <a href="%s">WordPress menu</a>.', WPS2_TEXT_DOMAIN), "nav-menus.php").'</div>';

	endif;

}

// Settings
add_action('wps_admin_getting_started_hook', 'wps_admin_getting_started_profile');
function wps_admin_getting_started_profile() {

	// Show menu item	
  	echo '<div class="wps_admin_getting_started_menu_item" id="wps_admin_getting_started_menu_item_default" rel="wps_admin_getting_started_profile">'.__('Profile Page', WPS2_TEXT_DOMAIN).'</div>';

  	// Show setup/help content
  	$display = isset($_POST['wps_expand']) && $_POST['wps_expand'] == 'wps_admin_getting_started_profile' ? 'block' : 'none';
  	echo '<div class="wps_admin_getting_started_content" id="wps_admin_getting_started_profile" style="display:'.$display.'">';
	?>

		<table class="form-table">
			<tr valign="top"> 
			<td scope="row"><label for="profile_page"><?php echo __('Profile Page', WPS2_TEXT_DOMAIN); ?></label></td>
			<td>
				<p style="margin-bottom:5px"><strong><?php echo __('Your profile page must not have a parent page.', WPS2_TEXT_DOMAIN); ?></strong></p>
				<select name="profile_page">
				 <?php 
				  $profile_page = get_option('wpspro_profile_page');
				  if (!$profile_page) echo '<option value="0">'.__('Select page...', WPS2_TEXT_DOMAIN).'</option>';
				  if ($profile_page) echo '<option value="0">'.__('Reset...', WPS2_TEXT_DOMAIN).'</option>';						
				  $pages = get_pages(); 
				  foreach ( $pages as $page ) {
				  	$option = '<option value="' . $page->ID . '"';
				  		if ($page->ID == $profile_page) $option .= ' SELECTED';
				  		$option .= '>';
					$option .= $page->post_title;
					$option .= '</option>';
					echo $option;
				  }
				 ?>						
				</select>
				<span class="description"><?php echo __('WordPress page that profile links go to.', WPS2_TEXT_DOMAIN); ?>
				<?php if ($profile_page) {
					echo ' [<a href="post.php?post='.$profile_page.'&action=edit">'.__('edit', WPS2_TEXT_DOMAIN).'</a>';
					echo '|<a href="'.get_permalink($profile_page).'">'.__('view', WPS2_TEXT_DOMAIN).'</a>]';
				}
				?>
				</span></td> 
			</tr> 

			<tr valign="top"> 
			<td scope="row"><label for="edit_profile_page"><?php echo __('Edit Profile Page', WPS2_TEXT_DOMAIN); ?></label></td>
			<td>
				<select name="edit_profile_page">
				 <?php 
				  $profile_page = get_option('wpspro_edit_profile_page');
				  if (!$profile_page) echo '<option value="0">'.__('Select page...', WPS2_TEXT_DOMAIN).'</option>';
				  if ($profile_page) echo '<option value="0">'.__('Reset...', WPS2_TEXT_DOMAIN).'</option>';						
				  $pages = get_pages(); 
				  foreach ( $pages as $page ) {
				  	$option = '<option value="' . $page->ID . '"';
				  		if ($page->ID == $profile_page) $option .= ' SELECTED';
				  		$option .= '>';
					$option .= $page->post_title;
					$option .= '</option>';
					echo $option;
				  }
				 ?>						
				</select>
				<span class="description"><?php echo __('WordPress page that allows user to edit their profile.', WPS2_TEXT_DOMAIN); ?>
				<?php if ($profile_page) {
					echo ' [<a href="post.php?post='.$profile_page.'&action=edit">'.__('edit', WPS2_TEXT_DOMAIN).'</a>';
					echo '|<a href="'.get_permalink($profile_page).'">'.__('view', WPS2_TEXT_DOMAIN).'</a>]';
				 } ?>
				</span></td> 
			</tr> 

			<tr valign="top"> 
			<td scope="row"><label for="change_avatar_page"><?php echo __('Change Avatar Page', WPS2_TEXT_DOMAIN); ?></label></td>
			<td>
				<select name="change_avatar_page">
				 <?php 
				  $profile_page = get_option('wpspro_change_avatar_page');
				  if (!$profile_page) echo '<option value="0">'.__('Select page...', WPS2_TEXT_DOMAIN).'</option>';
				  if ($profile_page) echo '<option value="0">'.__('Reset...', WPS2_TEXT_DOMAIN).'</option>';						
				  $pages = get_pages(); 
				  foreach ( $pages as $page ) {
				  	$option = '<option value="' . $page->ID . '"';
				  		if ($page->ID == $profile_page) $option .= ' SELECTED';
				  		$option .= '>';
					$option .= $page->post_title;
					$option .= '</option>';
					echo $option;
				  }
				 ?>						
				</select>
				<span class="description"><?php echo __('WordPress page that allows user to change their avatar.', WPS2_TEXT_DOMAIN); ?>
				<?php if ($profile_page) {
					echo ' [<a href="post.php?post='.$profile_page.'&action=edit">'.__('edit', WPS2_TEXT_DOMAIN).'</a>';
					echo '|<a href="'.get_permalink($profile_page).'">'.__('view', WPS2_TEXT_DOMAIN).'</a>]';
				} ?>
				</span></td> 
			</tr> 

			<tr valign="top"> 
			<td scope="row"><label for="profile_permalinks"><?php echo __('Profile Parameter', WPS2_TEXT_DOMAIN); ?></label></td>
			<td>
				<input name="wpspro_profile_permalinks" id="wpspro_profile_permalinks" type="checkbox" <?php if ( get_option('wpspro_profile_permalinks') ) echo 'CHECKED'; ?> style="width:10px" />
   				<span class="description"><?php _e('Do not use usernames for profile page links', WPS2_TEXT_DOMAIN); ?></span>
			</tr> 

		</table>

		<?php

  		echo '<h2>'.__('Getting Started', WPS2_TEXT_DOMAIN).'</h2>';

  		echo '<p><em>'.__('Either click on "<a href="#">Add Profile Pages</a>" at the top of this page, or...', WPS2_TEXT_DOMAIN).'</em></p>';

  		echo '<div style="border:1px dashed #333; background-color:#efefef; margin-bottom:10px; padding-left:15px">';

		  	echo '<h3>'.__('Profile Page', WPS2_TEXT_DOMAIN).'</h3>';

		  	if (!$profile_page = get_option('wpspro_profile_page')):
			  	echo '<p>'.sprintf(__('<a href="%s">Create a WordPress page</a>, then select it above, and save. When you have done that, some example shortcodes will be shown here that you can copy into that page.', WPS2_TEXT_DOMAIN), 'post-new.php?post_type=page').'</p>';
		  	else:
		  		echo '<p>'.__('Copy the following shortcode', WPS2_TEXT_DOMAIN).', <a href="post.php?post='.$profile_page.'&action=edit">'.__('edit your "Profile" page', WPS2_TEXT_DOMAIN).'</a> '.__('and paste the shortcodes to get started.', WPS2_TEXT_DOMAIN).'</p>';
		  		echo '<p>';
			  	echo '<strong>['.WPS_PREFIX.'-activity-page]</strong> <span class="description">'.__("Creates a profile page with key elements", WPS2_TEXT_DOMAIN).'</span><br />';
			  	echo '<span class="description"><a href="http://www.wpsymposiumpro.com/shortcodes" target="_blank">'.__('more examples...', WPS2_TEXT_DOMAIN).'</a></span>';
			  	echo '</p>';
		  	endif;

		  	echo '<h3>'.__('Edit Profile Page', WPS2_TEXT_DOMAIN).'</h3>';

		  	if (!$profile_page = get_option('wpspro_edit_profile_page')):
			  	echo '<p>'.sprintf(__('<a href="%s">Create a WordPress page</a>, then select it above, and save. When you have done that, some example shortcodes will be shown here that you can copy into that page.', WPS2_TEXT_DOMAIN), 'post-new.php?post_type=page').'</p>';
		  	else:
		  		echo '<p>'.__('Copy the following shortcodes', WPS2_TEXT_DOMAIN).', <a href="post.php?post='.$profile_page.'&action=edit">'.__('edit your "Edit Profile" page', WPS2_TEXT_DOMAIN).'</a> '.__('and paste the shortcodes to get started.', WPS2_TEXT_DOMAIN).'</p>';
		  		echo '<p>';
			  	echo '<strong>['.WPS_PREFIX.'-usermeta-change]</strong> <span class="description">'.__("Let's the user change their profile details", WPS2_TEXT_DOMAIN).'</span><br />';
			  	echo '<span class="description"><a href="http://www.wpsymposiumpro.com/shortcodes" target="_blank">'.__('more examples...', WPS2_TEXT_DOMAIN).'</a></span>';
			  	echo '</p>';
		  	endif;

		  	echo '<h3>'.__('Change Avatar Page', WPS2_TEXT_DOMAIN).'</h3>';

		  	if (!$profile_page = get_option('wpspro_change_avatar_page')):
			  	echo '<p>'.sprintf(__('<a href="%s">Create a WordPress page</a>, then select it above, and save. When you have done that, some example shortcodes will be shown here that you can copy into that page.', WPS2_TEXT_DOMAIN), 'post-new.php?post_type=page').'</p>';
		  	else:
		  		echo '<p>'.__('Copy the following shortcodes', WPS2_TEXT_DOMAIN).', <a href="post.php?post='.$profile_page.'&action=edit">'.__('edit your "Change Avatar" page', WPS2_TEXT_DOMAIN).'</a> '.__('and paste the shortcodes to get started.', WPS2_TEXT_DOMAIN).'</p>';
		  		echo '<p>';
			  	echo '<strong>['.WPS_PREFIX.'-avatar-change]</strong> <span class="description">'.__("Let's the user upload and crop their avatar", WPS2_TEXT_DOMAIN).'</span><br />';
			  	echo '<span class="description"><a href="http://www.wpsymposiumpro.com/shortcodes" target="_blank">'.__('more examples...', WPS2_TEXT_DOMAIN).'</a></span>';
			  	echo '</p>';
		  	endif;

		  	echo '<h3>'.__('Adding the Pages to your Site', WPS2_TEXT_DOMAIN).'</h3>';
		  	echo '<p>'.sprintf(__('Once you have created your pages, you may want to add them to your <a href="%s">site menu</a>.', WPS2_TEXT_DOMAIN), 'nav-menus.php').'</p>';

		echo '</div>';

	echo '</div>';

}

add_action( 'wps_admin_setup_form_save_hook', 'wps_profile_admin_options_save', 10, 1 );
function wps_profile_admin_options_save ($the_post) {

	if (isset($the_post['profile_page']) && $the_post['profile_page'] > 0):
		update_option('wpspro_profile_page', $the_post['profile_page']);
	else:
		delete_option('wpspro_profile_page');
	endif;

	if (isset($the_post['change_avatar_page']) && $the_post['change_avatar_page'] > 0):
		update_option('wpspro_change_avatar_page', $the_post['change_avatar_page']);
	else:
		delete_option('wpspro_change_avatar_page');
	endif;		

	if (isset($the_post['edit_profile_page']) && $the_post['edit_profile_page'] > 0):
		update_option('wpspro_edit_profile_page', $the_post['edit_profile_page']);
	else:
		delete_option('wpspro_edit_profile_page');
	endif;		

	if (isset($the_post['wpspro_profile_permalinks'])):
		update_option('wpspro_profile_permalinks', true);
	else:
		delete_option('wpspro_profile_permalinks');
	endif;


}

?>