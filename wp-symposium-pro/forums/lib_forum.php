<?php
while(!is_file('wp-config.php')){
	if(is_dir('../')) chdir('../');
	else die('Could not find WordPress config file.');
}
include_once( 'wp-config.php' );

$action = isset($_POST['action']) ? $_POST['action'] : false;

if ($action) {

	global $current_user;
	get_currentuserinfo();

	if ( is_user_logged_in() ) {

		/* ADD POST */
		if ($action == 'wps_forum_post_add') {

			$the_post = $_POST;
			$status = $the_post['wps_forum_moderate'] == '1' ? 'pending' : 'publish';

			$post = array(
			  'post_title'     => $the_post['wps_forum_post_title'],
			  'post_content'   => $the_post['wps_forum_post_textarea'],
			  'post_status'    => $status,
			  'author'		   => $current_user->ID,
			  'post_type'      => 'wps_forum_post',
			  'post_author'    => $current_user->ID,
			  'ping_status'    => 'closed',
			  'comment_status' => 'open',
			);  
			$new_id = wp_insert_post( $post );

			wp_set_object_terms( $new_id, $the_post['wps_forum_slug'], 'wps_forum' );

			if ($new_id):

				// Any further actions?
				do_action( 'wps_forum_post_add_hook', $the_post, $_FILES, $new_id );

			endif;

			$new_post = get_post($new_id);
			echo $new_post->post_name;

		}

		/* ADD COMMENT (REPLY) */
		if ($action == 'wps_forum_comment_add') {

			$the_comment = $_POST;
			$status = $the_comment['wps_forum_moderate'] == '1' ? '0' : '1';

            if ($the_comment['wps_forum_comment']):
                $data = array(
                    'comment_post_ID' => $the_comment['post_id'],
                    'comment_content' => $the_comment['wps_forum_comment'],
                    'comment_type' => '',
                    'comment_parent' => 0,
                    'comment_author' => $current_user->user_login,
                    'comment_author_email' => $current_user->user_email,
                    'user_id' => $current_user->ID,
                    'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
                    'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
                    'comment_approved' => $status,
                );
                $new_id = wp_insert_comment($data);
            else:
                $new_id = false;
            endif;
            $return_html = 'reload';

            // Close Post?
            if (isset($_POST['wps_close_post']) && $_POST['wps_close_post'] == 'on'):

                $my_post = array(
                      'ID'           	=> $the_comment['post_id'],
                      'comment_status' 	=> 'closed',
                );
                wp_update_post( $my_post );

            endif;
            
            // Private Post?
            if (isset($_POST['wps_private_post']) && $_POST['wps_private_post'] == 'on')
                update_comment_meta($new_id, 'wps_private_post', true);

            // Reset read flags (apart from current user)
            $read = array();
            $read[] = $current_user->user_login;
            update_post_meta ( $the_comment['post_id'], 'wps_forum_read', $read);

            // Move forums?
            if (isset($the_comment['wps_post_forum_slug'])) :

                $current_post_terms = get_the_terms( $the_comment['post_id'], 'wps_forum' );
                $current_post_term = $current_post_terms[0];
                if ($current_post_term->slug != $the_comment['wps_post_forum_slug']):

                    $the_post = get_post($the_comment['post_id']);
                    if (is_multisite()) {

                        $blog_details = get_blog_details($blog->blog_id);
                        $url = $blog_details->path.$the_comment['wps_post_forum_slug'].'/'.$the_post->post_name;

                    } else {

                        if ( wps_using_permalinks() ):
                            $url = get_bloginfo('url').'/'.$the_comment['wps_post_forum_slug'].'/'.$the_post->post_name;
                        else:
                            // Get term, and then page for forum
                            $new_term = get_term_by('slug', $the_comment['wps_post_forum_slug'], 'wps_forum');
                            $forum_page_id = wps_get_term_meta($new_term->term_id, 'wps_forum_cat_page', true);
                            $url = get_bloginfo('url')."/?page_id=".$forum_page_id."&topic=".$the_post->post_name;
                        endif;

                    }

                    $return_html = $url;

                    // Save post forum (term)
                    wp_set_object_terms( $the_comment['post_id'], $the_comment['wps_post_forum_slug'], 'wps_forum' );

                endif;

            endif;

            // Any further actions?
            do_action( 'wps_forum_comment_add_hook', $the_comment, $_FILES, $the_comment['post_id'], $new_id );
            
            echo $return_html;            

		}
		
	}


}

?>
