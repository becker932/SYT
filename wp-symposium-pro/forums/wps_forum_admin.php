<?php

function wpspro_forum_setup() {

  	echo '<div class="wrap">';
        	
	  	echo '<div id="icon-themes" class="icon32"><br /></div>';

	  	echo '<h2>'.__('All Forums', WPS2_TEXT_DOMAIN).'</h2>';

	  	if (isset($_POST['wps_forum_id'])):
			$range = array_keys($_POST['wps_forum_id']);
			foreach ($range as $key):
				$wps_forum_id = $_POST['wps_forum_id'][$key];
				$wps_forum_order = $_POST['wps_forum_order'][$key];
				$wps_forum_public = $_POST['wps_forum_public'][$key];
				$wps_forum_closed = $_POST['wps_forum_closed'][$key];
				$wps_forum_author = $_POST['wps_forum_author'][$key];
                $wps_forum_auto = $_POST['wps_forum_auto'][$key];

				if ($wps_forum_order):
					wps_update_term_meta( $wps_forum_id, 'wps_forum_order', $wps_forum_order );
				else:
					wps_update_term_meta( $wps_forum_id, 'wps_forum_order', 0 );
				endif;

				if ($wps_forum_public):
					wps_update_term_meta( $wps_forum_id, 'wps_forum_public', true );
				else:
					wps_update_term_meta( $wps_forum_id, 'wps_forum_public', false );
				endif;

				if ($wps_forum_closed):
					wps_update_term_meta( $wps_forum_id, 'wps_forum_closed', $_POST['wps_forum_closed'] );
				else:
					wps_update_term_meta( $wps_forum_id, 'wps_forum_closed', 0 );
				endif;

				if ($wps_forum_author):
					wps_update_term_meta( $wps_forum_id, 'wps_forum_author', $wps_forum_author );
				else:
					wps_update_term_meta( $wps_forum_id, 'wps_forum_author', 0 );
				endif;

				if ($wps_forum_auto):
					wps_update_term_meta( $wps_forum_id, 'wps_forum_auto', $wps_forum_auto );
				else:
					wps_update_term_meta( $wps_forum_id, 'wps_forum_auto', 0 );
				endif;

				do_action( 'wpspro_forum_setup_save', $wps_forum_id, $_POST );

			endforeach;

		endif;

		$terms = get_terms( "wps_forum", array(
		    'hide_empty'    => false, 
		    'fields'        => 'all', 
		    'hierarchical'  => false, 
		    'order'			=> 'ASC',
		    'orderby'		=> 'name'
		) );

		if ($terms):

			$sort = array();
			foreach ($terms as $k=>$v):
		    	$sort['term_id'][$k] = $v->term_id;				
		    	$sort['name'][$k] = $v->name;				
		    	$sort['order'][$k] = wps_get_term_meta($v->term_id, 'wps_forum_order', true);				
			endforeach;
			array_multisort($sort['order'], SORT_ASC, $sort['name'], SORT_ASC, $terms);

			echo '<form action="" method="POST">';

				echo '<br /><table class="widefat">';
    
                echo '<tr>';
                    echo '<td>'.__('Forum', WPS2_TEXT_DOMAIN).'</td>';
                    echo '<td style="text-align:center">'.__('Order', WPS2_TEXT_DOMAIN).'</td>';
                    echo '<td style="text-align:center">'.__('Privacy', WPS2_TEXT_DOMAIN).'</td>';
                    echo '<td style="text-align:center">'.__('Status', WPS2_TEXT_DOMAIN).'</td>';
                    echo '<td style="text-align:center">'.__('Visibility', WPS2_TEXT_DOMAIN).'</td>';
                    if (function_exists('wps_forum_subs_extension_insert_rewrite_rules')) echo '<td style="text-align:center">'.__('Autosubscribe', WPS2_TEXT_DOMAIN).'</td>';
                echo '<tr>';

				foreach ($terms as $term):

					echo '<tr>';

						$page_id = wps_get_term_meta($term->term_id, 'wps_forum_cat_page', true);
						$url = $page_id ? get_permalink($page_id) : false;

						echo '<td style="border-top:1px solid #cfcfcf;width:40%;">';
						echo '<strong><a style="text-decoration:none" href="edit-tags.php?action=edit&taxonomy=wps_forum&tag_ID='.$term->term_id.'&post_type=wps_forum_post">'.$term->name.'</a></strong><br />'.$term->slug.'<br />';
						echo '<a style="text-decoration:none" href="edit-tags.php?action=edit&taxonomy=wps_forum&tag_ID='.$term->term_id.'&post_type=wps_forum_post">'.__('Edit', WPS2_TEXT_DOMAIN).'</a> | ';
						if ($page_id) {
							echo '<a style="text-decoration:none" href="post.php?post='.$page_id.'&action=edit">'.__('Page', WPS2_TEXT_DOMAIN).'</a> | ';
							if ($url) echo '<a style="text-decoration:none" href="'.$url.'">'.__('View', WPS2_TEXT_DOMAIN).'</a>';
						} else {
							echo '<a href="edit-tags.php?action=edit&taxonomy=wps_forum&tag_ID='.$term->term_id.'&post_type=wps_forum_post">'.__('select WordPress page...', WPS2_TEXT_DOMAIN).'</a>';
						}
						echo '</td>';

						echo '<input name="wps_forum_id[]" type="hidden" value="'.$term->term_id.'" />'; 

						echo '<td style="text-align:center;border-top:1px solid #cfcfcf;width:15%;">';
						echo '<input name="wps_forum_order[]" id="wps_forum_order" type="text" value="';
						echo wps_get_term_meta($term->term_id, 'wps_forum_order', true);
						echo '" style="width:50px" />';
						echo '</td>';

						echo '<td style="text-align:center;border-top:1px solid #cfcfcf;width:15%;">';
						echo '<select name="wps_forum_public[]">';
						if ( !wps_get_term_meta($term->term_id, 'wps_forum_public', true) ):
							echo '<option value="0" SELECTED>'.__('Private', WPS2_TEXT_DOMAIN).'</option>';
							echo '<option value="1">'.__('Public', WPS2_TEXT_DOMAIN).'</option>';
						else:
							echo '<option value="0">'.__('Private', WPS2_TEXT_DOMAIN).'</option>';
							echo '<option value="1" SELECTED>'.__('Public', WPS2_TEXT_DOMAIN).'</option>';
						endif;
						echo '</select>';
						echo '</td>';

						echo '<td style="text-align:center;border-top:1px solid #cfcfcf;width:15%;">';
						echo '<select name="wps_forum_closed[]">';
						if ( !wps_get_term_meta($term->term_id, 'wps_forum_closed', true) ):
							echo '<option value="0" SELECTED>'.__('Open', WPS2_TEXT_DOMAIN).'</option>';
							echo '<option value="1">'.__('Closed', WPS2_TEXT_DOMAIN).'</option>';
						else:
							echo '<option value="0">'.__('Open', WPS2_TEXT_DOMAIN).'</option>';
							echo '<option value="1" SELECTED>'.__('Closed', WPS2_TEXT_DOMAIN).'</option>';
						endif;
						echo '</select>';
						echo '</td>';

						echo '<td style="text-align:center;border-top:1px solid #cfcfcf;width:15%;">';
						echo '<select name="wps_forum_author[]">';
						if ( !wps_get_term_meta($term->term_id, 'wps_forum_author', true) ):
							echo '<option value="0" SELECTED>'.__('All', WPS2_TEXT_DOMAIN).'</option>';
							echo '<option value="1">'.__('Just own', WPS2_TEXT_DOMAIN).'</option>';
						else:
							echo '<option value="0">'.__('All', WPS2_TEXT_DOMAIN).'</option>';
							echo '<option value="1" SELECTED>'.__('Just own', WPS2_TEXT_DOMAIN).'</option>';
						endif;
						echo '</select>';
						echo '</td>';

                        if (function_exists('wps_forum_subs_extension_insert_rewrite_rules')):
                            echo '<td style="text-align:center;border-top:1px solid #cfcfcf;width:15%;">';
                            echo '<select name="wps_forum_auto[]">';
                            if ( !wps_get_term_meta($term->term_id, 'wps_forum_auto', true) ):
                                echo '<option value="0" SELECTED>'.__('No', WPS2_TEXT_DOMAIN).'</option>';
                                echo '<option value="1">'.__('Yes', WPS2_TEXT_DOMAIN).'</option>';
                            else:
                                echo '<option value="0">'.__('No', WPS2_TEXT_DOMAIN).'</option>';
                                echo '<option value="1" SELECTED>'.__('Yes', WPS2_TEXT_DOMAIN).'</option>';
                            endif;
                            echo '</select>';
                            echo '</td>';
                        endif;
    
					echo '</tr>';

					// Any more? (eg. forum security)
					do_action( 'wpspro_forum_setup_after', $term );

				endforeach;

				echo '</table>';

			echo '<br /><input type="submit" class="button-primary" value="'.__('Update', WPS2_TEXT_DOMAIN).'" />';
			echo '</form>';

		else:

			echo '<a href="admin.php?page=wps_pro_setup">'.__('Add a forum via Setup', WPS2_TEXT_DOMAIN).'</a>';

		endif;

		
	echo '</div>';	  	

}



?>