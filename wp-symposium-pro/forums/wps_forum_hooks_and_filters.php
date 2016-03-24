<?php

// Add automatic subscriptions for any forums
add_action('user_register','wps_forum_auto_subscribe');
function wps_forum_auto_subscribe($user_id){

    if (function_exists('wps_forum_subs_extension_insert_rewrite_rules')):
    
        $terms = get_terms( "wps_forum", array(
            'hide_empty'    => false, 
            'fields'        => 'all', 
            'hierarchical'  => false, 
        ) );

        if ($terms):

            foreach ($terms as $term):

                if ( wps_get_term_meta($term->term_id, 'wps_forum_auto', true) ):

                    $user = get_user_by('id', $user_id);
                    $post = array(
                        'post_title'		=> $user->user_login,
                        'post_status'   	=> 'publish',
                        'post_type'     	=> 'wps_forum_subs',
                        'post_author'   	=> $user->ID,
                        'ping_status'   	=> 'closed',
                        'comment_status'	=> 'closed',
                    );  
                    $new_sub_id = wp_insert_post( $post );
                    update_post_meta( $new_sub_id, 'wps_forum_id', $term->term_id );

                endif;

            endforeach;

        endif;
    
    endif;
    
}

?>