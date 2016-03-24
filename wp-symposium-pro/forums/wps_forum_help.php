<?php
// Quick Start
add_action('wps_admin_quick_start_hook', 'wps_admin_quick_start_forum');
function wps_admin_quick_start_forum() {

	echo '<div style="margin-right:10px; float:left">';
	echo '<input type="submit" id="wps_admin_forum_add" class="button-secondary" value="'.__('Add Forum', WPS2_TEXT_DOMAIN).'" />';
	echo '</div>';

	echo '<div id="wps_admin_forum_add_details" style="clear:both;display:none">';
		echo '<form action="" method="POST">';
		echo '<input type="hidden" name="wpspro_quick_start" value="forum" />';
		echo '<br /><strong>'.__('Enter name of new forum', WPS2_TEXT_DOMAIN).'</strong><br />';
		echo '<input type="input" style="margin-top:4px;" id="wps_admin_forum_add_name" name="wps_admin_forum_add_name" /><br />';
		echo '<br /><strong>'.__('Enter description of new forum', WPS2_TEXT_DOMAIN).'</strong><br />';
		echo '<input type="input" style="margin-top:4px;width:300px;" id="wps_admin_forum_add_description" name="wps_admin_forum_add_description" /><br /><br />';
		echo '<input type="submit" id="wps_admin_forum_add_button" class="button-primary" value="'.__('Publish', WPS2_TEXT_DOMAIN).'" />';
		echo '</form>';
	echo '</div>';


}

add_action('wps_admin_quick_start_form_save_hook', 'wps_admin_quick_start_forum_save', 10, 1);
function wps_admin_quick_start_forum_save($the_post) {

	if (isset($the_post['wpspro_quick_start']) && $the_post['wpspro_quick_start'] == 'forum'):

		$name = $the_post['wps_admin_forum_add_name'];
		$description = $the_post['wps_admin_forum_add_description'];
		$slug = sanitize_title_with_dashes($name);

		$new_term = wp_insert_term(
		  $name, 
		  'wps_forum', 
		  array(
		    'description'=> $description,
		    'slug' => $slug,
		  )
		);	

		if (is_wp_error($new_term)):
			
			echo '<div class="wps_error">'.__('You have already added this Forum.', WPS2_TEXT_DOMAIN).'</div>';

		else:

			$post_content = '['.WPS_PREFIX.'-forum-page slug="'.$slug.'"]';

			// Activity Page
			$post = array(
			  'post_content'   => $post_content,
			  'post_name'      => $slug,
			  'post_title'     => $name,
			  'post_status'    => 'publish',
			  'post_type'      => 'page',
			  'ping_status'    => 'closed',
			  'comment_status' => 'closed',
			);  

			$new_id = wp_insert_post( $post );	

			wps_update_term_meta( $new_term['term_id'], 'wps_forum_public', true );
			wps_update_term_meta( $new_term['term_id'], 'wps_forum_cat_page', $new_id );
			wps_update_term_meta( $new_term['term_id'], 'wps_forum_order', 1 );

			echo '<div class="wps_success">';
				echo sprintf(__('Forum Page (%s) added. [<a href="%s">view</a>]', WPS2_TEXT_DOMAIN), get_permalink($new_id), get_permalink($new_id)).'<br /><br />';
				echo sprintf(__('If you are using the <a href="%s">Forum Roles Security</a> extension, choose who can see the forum via <a href="%s">WPS Pro->Forum Setup</a>.', WPS2_TEXT_DOMAIN), "http://www.wpsymposiumpro.com/plugin/forum-roles-security/", "edit-tags.php?taxonomy=wps_forum&post_type=wps_forum_post").'<br />';
				echo sprintf(__('You might want to add it to your <a href="%s">WordPress menu</a>.', WPS2_TEXT_DOMAIN), "nav-menus.php");
			echo '</div>';

		endif;

	endif;

}

// Add to Getting Started information
add_action('wps_admin_getting_started_hook', 'wps_admin_getting_started_forum');
function wps_admin_getting_started_forum() {

  	echo '<div class="wps_admin_getting_started_menu_item" rel="wps_admin_getting_started_forum">'.__('Forum', WPS2_TEXT_DOMAIN).'</div>';

  	$display = isset($_POST['wps_expand']) && $_POST['wps_expand'] == 'wps_admin_getting_started_forum' ? 'block' : 'none';
  	echo '<div class="wps_admin_getting_started_content" id="wps_admin_getting_started_forum" style="display:'.$display.'">';

		?>
		<table class="form-table">
		<tr class="form-field">
			<td scope="row" valign="top">
				<label for="wps_forum_auto_close"><?php _e('Auto-close period', WPS2_TEXT_DOMAIN); ?></label>
			</td>
			<td>
				<input type="text" style="width:50px" name="wps_forum_auto_close" value="<?php echo get_option('wps_forum_auto_close'); ?>" /> 
				<span class="description"><?php _e('Number of days after no activity that a forum post will close automatically (blank for never).', WPS2_TEXT_DOMAIN); ?></span>
			</td>
		</tr> 
		</table>
		<?php

  		echo '<h2>'.__('Getting Started', WPS2_TEXT_DOMAIN).'</h2>';

  		echo '<p><em>'.__('Either click on "<a href="#">Add Forum</a>" at the top of this page, or...', WPS2_TEXT_DOMAIN).'</em></p>';

  		echo '<div style="border:1px dashed #333; background-color:#efefef; margin-bottom:10px; padding-left:15px">';

		  	echo '<ol>';
			echo '<li>'.sprintf(__('<a href="%s">Create a new WordPress page</a> for your forum (for example "General Forum").', WPS2_TEXT_DOMAIN), 'post-new.php?post_type=page').'<br />';
				echo '<strong>'.__('Your forum page must not have a parent page.', WPS2_TEXT_DOMAIN).'</strong>';
				echo '</li>';
			echo '<li>'.sprintf(__('Create a forum, with a slug that matches your new page slug, in <a href="%s">Forum Setup</a> (for example "general-forum").', WPS2_TEXT_DOMAIN), 'edit-tags.php?taxonomy=wps_forum&post_type=wps_forum_post').'</li>';
			echo '<li>'.__('Copy the following shortcodes to your new WordPress page to display your site forums', WPS2_TEXT_DOMAIN).'</li>';
		  	echo '</ol>';

			echo '<p>';
		  	echo '<strong>['.WPS_PREFIX.'-forum-post slug="general-forum"]</strong> <span class="description">'.__("Show the 'New Post' form for the 'general-forum' forum", WPS2_TEXT_DOMAIN).'</span><br />';
		  	echo '<strong>['.WPS_PREFIX.'-forum slug="general-forum"]</strong> <span class="description">'.__("Show the forum post or posts on the 'general-forum' forum", WPS2_TEXT_DOMAIN).'</span><br />';
		  	echo '<strong>['.WPS_PREFIX.'-forum-comment slug="general-forum"]</strong> <span class="description">'.__("Show the 'Add Comment' form for the 'general-forum' forum", WPS2_TEXT_DOMAIN).'</span><br />';
		  	echo '<strong>['.WPS_PREFIX.'-forum-backto slug="general-forum"]</strong> <span class="description">'.__("Show a link back to the 'general-forum' forum", WPS2_TEXT_DOMAIN).'</span><br />';
			echo '<span class="description"><a href="http://www.wpsymposiumpro.com/shortcodes" target="_blank">'.__('more examples...', WPS2_TEXT_DOMAIN).'</a></span>';
		  	echo '</p>';

			echo '<p>';
			echo __('The various shortcodes above will operate when appropriate.', WPS2_TEXT_DOMAIN);
			echo '</p><p>';
			echo sprintf(__('When you have several forums, <a href="%s">create a new WordPress page</a> to display an overview, and copy the following shortcode to your new WordPress page.', WPS2_TEXT_DOMAIN), 'post-new.php?post_type=page');
			echo '</p>';

			echo '<p>';
		  	echo '<strong>['.WPS_PREFIX.'-forums]</strong> <span class="description">'.__("Displays your site forums", WPS2_TEXT_DOMAIN).'</span><br />';  	
		  	echo '<span class="description"><a href="http://www.wpsymposiumpro.com/shortcodes" target="_blank">'.__('more examples...', WPS2_TEXT_DOMAIN).'</a></span>';
		  	echo '</p>';

			echo '<p>'.sprintf(__('Once you have created one or more forums, you may want to <strong>add the page or pages</strong> to your <a href="%s">site menu</a>.', WPS2_TEXT_DOMAIN), 'nav-menus.php').'</p>';

            echo '<p><strong>'.__('Forum Subscriptions', WPS2_TEXT_DOMAIN).'</strong></p>';
            echo '<p>'.__('If you have activated the Forum Subscriptions extension, you can add the following shortcodes:', WPS2_TEXT_DOMAIN).'</p>';

if (!is_admin()) add_shortcode(WPS_PREFIX.'-subscribe-forum', 'wps_subscribe_forum');
if (!is_admin()) add_shortcode(WPS_PREFIX.'-subscribe-post', 'wps_subscribe_post');
    
            echo '<p>';
		  	echo '<strong>['.WPS_PREFIX.'-subscribe-forum slug="general-forum"]</strong> <span class="description">'.__("Show a subscription link for the 'general-forum' forum (when not viewing a single post)", WPS2_TEXT_DOMAIN).'</span><br />';
		  	echo '<strong>['.WPS_PREFIX.'-subscribe-post]</strong> <span class="description">'.__("Show a subscription link for current single post", WPS2_TEXT_DOMAIN).'</span><br />';
		  	echo '</p>';
    
        echo '</div>';

	echo '</div>';

}

add_action('wps_admin_setup_form_get_hook', 'wps_admin_forum_save', 10, 2);
add_action('wps_admin_setup_form_save_hook', 'wps_admin_forum_save', 10, 2);
function wps_admin_forum_save($the_post) {

	if (isset($the_post['wps_forum_auto_close']) && $the_post['wps_forum_auto_close'] != ''):
		update_option('wps_forum_auto_close', $the_post['wps_forum_auto_close']);
	else:
		delete_option('wps_forum_auto_close');
	endif;


}

?>