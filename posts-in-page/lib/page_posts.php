<?php
/**
 *	Page Posts Class, main workhorse for the ic_add_posts shortcode.
 */


function remove_tinymce_buttons( $buttons ){

    $remove = array( 'fullscreen', 'wp_more', 'link','unlink' );

    return array_diff( $buttons, $remove );
}

add_filter( 'mce_buttons','remove_tinymce_buttons' );

function remove_three_tinymce2_buttons( $buttons ){
    //Remove last 3 buttons in the second row
    //Undo/Redo/Keyboard Shortcuts
    $remove = array( 'removeformat', 'wp_help' );

    return array_diff( $buttons, $remove );
}

add_filter( 'mce_buttons_2','remove_three_tinymce2_buttons' );



if ( !function_exists( 'add_action' ) )
	wp_die( 'You are trying to access this file in a manner not allowed.', 'Direct Access Forbidden', array( 'response' => '403' ) );

add_action( 'wp_enqueue_scripts', 'add_syt_docedit_script' );

/* insert editor ajax svascript*/
function add_syt_docedit_script() {

    wp_register_script( 'ajax-js',WP_PLUGIN_URL.'/syt-onlinedocs-plugin/ajax.js', array( 'jquery' ), '', true );
    wp_localize_script( 'ajax-js', 'ajax_params', array( 'ajax_url' => admin_url( 'admin-ajax.php' ),'ajaxnonce' => wp_create_nonce( 'syt-ajaxnonce' )) );
    wp_enqueue_script( 'ajax-js' );

}


class ICPagePosts {

	protected $args = array(
		'post_type'   => 'post',
		'post_status' => 'publish',
		'orderby'     => 'date',
		'order'       => 'DESC',
		'paginate'    => false,
		'template'    => false
	); // set defaults for wp_parse_args

	public function __construct( $atts ) {
		self::set_args( $atts );
	}



	/**
	 *	Output's the posts
	 * If it is a customisable post and the user has no personal version of it an new copy of is created and assigned to them
	 * (with the original "parent" source post defined in a custom field... syt-custom-contained-by)
	 *
	 *	@return string output of template file
	 */
	public function output_posts() {

		global $SYT_custom_query;
		global $SYT_static_query;
		global $SYT_original_custom_id;
		global $post;

		//if this is not a request for a revision then set the static id for passing to the  revision sidebar
		if(isset($_GET["revSID"]) !== true)
		{
			//echo 'set static id to n '.$post->ID;
			$SYT_static_query = $post->ID;
		}

		if ( !$this->args )
			return '';
		$multiFormSeperator = 0;
		$SYT_original_custom_id = null;

		$user_ID = get_current_user_id();
		$src_custom_fields = get_user_option('s2member_custom_fields', $user_ID);
		$hasParent = $src_custom_fields['syt_linked_admin_id'];
        //error_log(print_r($src_custom_fields));
		if(empty($hasParent))
		{
			$myId = $user_ID;
			//error_log("reckons it doesn't hasParent".$hasParent.">>".($user_ID));

		}
		else
		{
			//error_log("reckons it hasParent".$hasParent);
			$myId = $hasParent;
		}
       // echo "-b4 1-";
		$page_posts = apply_filters( 'posts_in_page_results', new WP_Query( $this->args ) ); // New WP_Query object
		//error_log("should have restricted query to user ID".$myId.">>>".$hasParent.">>>".the_author_meta( 'ID' ));
		$output = '';
		if ( $page_posts->have_posts() ):
			while ( $page_posts->have_posts() ):
				//error_log("check author on post".$myId." vs ".$page_posts->the_author_meta( 'ID'));
				$page_posts->the_post();
				$overrideCustom = false;
				//echo 'test echos statement';
				if ( in_category('customisable', $page_posts->the_post.get_the_ID()) )
                {

					//set original id for the 0 revision in sidebar
					$SYT_original_custom_id = $page_posts->the_post.get_the_ID();
					if(isset($_GET["revDID"]) === true)
					{

						$oldPost= get_post((int)get_query_var( 'revDID' ));
						if($oldPost){

						    $output = $oldPost->post_content;
						    break;
							//echo do_shortcode('[themify_box style="orange rounded shadow"]<span style="font-family: Arial, serif;"><b>Note: You are viewing revision '.get_query_var( 'revDID' ).' - which is an old version of this document.</b></span>[/themify_box]');
						}
						else
						{
							echo do_shortcode('[themify_box style="red rounded shadow"]<span style="font-family: Arial, serif;"><b>Error: Invalid revision requested. Please contact SYT support.</b></span>[/themify_box]');
						}
						// do a query for the requested revision, if it exists then produce warning message and replace the content
					}
					else
					{
						//echo 'is customisable  '.($page_posts->the_post.get_the_ID());
						$my_query = new WP_Query();
						$my_query->query(array( 'author' => $myId, 'meta_key' => 'syt-original-doc', 'meta_value' => $page_posts->the_post.get_the_ID()));
                        $style = "style='background-color:peachpuff; padding: 10px 10px 10px 10px ; visibility:hidden'";
                        $areaColour = "peachpuff";
						if ( $my_query->have_posts() ){
                            //error_log('returned a version of the doc with the right value in the custom field'.count($my_query));
						     //it exists
							// Set our current quey to use the author's version
							if(get_post_meta( $my_query->posts[0]->ID, 'syt-draft-mode', true)=='true')
							{
								if(!empty($_SESSION['SYT_READ_ONLY_USER']) && $_SESSION['SYT_READ_ONLY_USER'])
								{
									//show last approved version
									//error_log("THINKS ITS A RO USER - TRYING TO GET A VALID REVISION NOT A DRAFT ".$my_query->posts[0]->ID);
									$args = array(
										'orderby' => 'date',
										'order'   => 'DESC',
									);
									$validRevisions = wp_get_post_revisions($my_query->posts[0]->ID,$args);
									$baseDate = get_userdata(get_current_user_id( ))->user_registered;

									foreach ($validRevisions as $rev) {
										if(wp_is_post_autosave($rev))continue;
										if (get_metadata('post', $rev->ID, 'syt-draft-mode', true) == "true") continue;
										if($rev->post_date > $baseDate)
										{
											$baseDate = $rev->post_date;
											$validRev = $rev;
										}
									}
									//error_log("ID OF REVISION ".$validRev->ID);
									$content = $output = $validRev->post_content;
									$my_query = new WP_Query( 'p='.$validRev->ID );
									$SYT_custom_query = $validRev->ID;
									$overrideCustom = true;
								}
								else
								{
                                    $SYT_custom_query = $my_query->posts[0]->ID;
									$draftHtml = '<b>This policy is in draft mode, it will not be live until it has been approved.</b>';
									echo "<script type='text/javascript'>document.getElementById('draftBanner').innerHTML =\"". $draftHtml."\"</script>";
                                    $areaColour="peachpuff";

								}
							}
							else
							{
                                $SYT_custom_query = $my_query->posts[0]->ID;
								//error_log("WE HAVE A VALID POST NOT IN DRAFT MODE".$my_query->posts[0]->ID);
							}

							$page_posts = $my_query;
                            $style =  "style='background-color:{$areaColour}; padding: 10px 10px 10px 10px ; height:480px; display: none ;visibility: hidden;'";
                            $nonEditorStyle ="style='background-color:{$areaColour}; display: block'";
                            $content = apply_filters('the_content', ($my_query->posts[0]->post_content));
                            $content = str_replace(']]>', ']]&gt;', $content);
                            $editor_id = 'editpost';
                            ob_start();
                            $settings = array('textarea_name'=>"content_ajax", 'quicktags' => false,'media_buttons' => false,'editor_height' => 400);
                            wp_editor( $my_query->posts[0]->post_content, $editor_id, $settings);
                            $html = ob_get_contents();
                            ob_end_clean();
                            $content = "<div id='my-nested-post' class='my-nested-post' {$nonEditorStyle}>{$content}</div>";
                            $content .="<form action=\"\" {$style} method=\"post\" id=\"syt_docedit_form\"><input type=\"hidden\" id =\"postID\" name=\"postID\" value=\"{$my_query->posts[0]->ID}\"><input type=\"hidden\" name=\"action\" value=\"sytdocedit\">{$html}</form>";


                            $output .= $content;


                            break;

						}
						else
						{
							if(!empty($_SESSION['SYT_READ_ONLY_USER']) && $_SESSION['SYT_READ_ONLY_USER'] == true)
							{
								$content = $output ="This document has not yet been approved for staff use. Please contact your administrator for more information on this topic.";
								break;
							}
							else
							{
                                //error_log("Thinks you are not a read only user and that this is an unapproved document - create a new copy");
                                $draftHtml = '<b>This policy is in draft mode, it will not be live until it has been approved.</b>';
                                echo "<script type='text/javascript'>document.getElementById('draftBanner').innerHTML =\"". $draftHtml."\"</script>";
								//echo $page_posts->the_post().get_the_content())
								$my_post = array(
								     'post_title' => 'USER '.get_current_user_id().' copy of '.$page_posts->the_post.get_the_ID().'('.$page_posts->the_post.get_the_title().')',
								     'post_content' => ($page_posts->the_post.get_the_content()),
								     'post_category' => array(53,39),
								     'post_status' => 'private',
								     'post_type' => 'post',
								  );
								//echo 'setup new post';
								$post_id = wp_insert_post($my_post);
								$SYT_custom_query = $post_id;
								//echo 'entered new post.'.$post_id;
								$originalContainingPost = get_post_meta( $page_posts->the_post.get_the_ID(), 'syt-custom-contained-by', true);
								add_post_meta($post_id, 'syt-original-doc', $page_posts->the_post.get_the_ID(), true);
								add_post_meta($post_id, 'syt-custom-contained-by', $originalContainingPost, true);
								add_post_meta($post_id, 'syt-draft-mode', 'true', true);
								$custom_fields = get_post_custom($page_posts->the_post.get_the_ID());
                                $customCount ="";
                                if(!empty($custom_fields['syt_sig'])) {
                                    $my_custom_field = $custom_fields['syt_sig'];
                                    if ($my_custom_field) {
                                        foreach ($my_custom_field as $key => $value) {
                                            add_post_meta($post_id, 'syt_sig', $value);
                                        }
                                    }
                                }
                                //return filtered content (include formatting)
                                $content = apply_filters('the_content', ($page_posts->the_post . get_the_content()));
                                $content = str_replace(']]>', ']]&gt;', $content);
                                $editor_id = 'editpost';
                                ob_start();
                                $settings = array('textarea_name'=>"content_ajax", 'quicktags' => false,'media_buttons' => false,'editor_height' => 400 );
                                wp_editor( $content, $editor_id, $settings);
                                $html = ob_get_contents();
                                ob_end_clean();
                                $style =  "style='background-color:{$areaColour}; padding: 10px 10px 10px 10px ; height:480px; display: none ;visibility: hidden;'";
                                $nonEditorStyle ="style='background-color:{$areaColour}; display: block'";
                                $content = "<div id='my-nested-post' class='my-nested-post' {$nonEditorStyle}>{$content}</div>";
                                $content .= "<form action=\"\" {$style} method=\"post\" id=\"syt_docedit_form\"><input type=\"hidden\" id =\"postID\" name=\"postID\" value=\"{$post_id}\"><input type=\"hidden\" name=\"action\" value=\"sytdocedit\">{$html}</form>";
                                $output .= $content;
								$output .= $customCount;

								//we break here but we could concatanate and continue the while loop (but we only ever use one post in page special case)
								//create new post using this template content.. set its SYT id and customisable fields
							}
						}
					}

				}

			$output .= self::add_template_part( $page_posts );
			if(!$overrideCustom)$SYT_custom_query = get_the_ID();
			endwhile;
			$output .= ( $this->args['paginate'] ) ? '<div class="pip-nav">' . apply_filters( 'posts_in_page_paginate', $this->paginate_links( $page_posts ) ) . '</div>' : '';
		endif;

		wp_reset_postdata();

		remove_filter( 'excerpt_more', array( &$this, 'custom_excerpt_more' ) );

        echo "

<script>
    jQuery(function ($) {
        // Was needed a timeout since RTE is not initialized when this code run.
        setTimeout(function () {
            for (var i = 0; i < tinymce.editors.length; i++) {
                tinymce.editors[i].onChange.add(function (ed, e) {
                    // Update HTML view textarea (that is the one used to send the data to server).
                    contentEdited = true;
                });
            }
        }, 1000);
    });

contentEdited = false;
window.onload = function() {
    var barePost = document.getElementById('my-nested-post');
    var edPost = document.getElementById('syt_docedit_form');
    barePost.style.cursor = 'pointer';
    barePost.onclick = function() {
        barePost.style.visibility = 'hidden' ;
        barePost.style.display = 'none' ;
        edPost.style.display = 'block' ;
        edPost.style.visibility = 'visible' ;
    }
}
window.onbeforeunload = function (e) {
            if(contentEdited){
            var message = 'You have unsaved edits on this page, are you sure you want to leave?',
              e = e || window.event;
              // For IE and Firefox
              if (e) {
                  e.returnValue = message;
              }

              // For Safari
              return message;
              }
            };</script>";

		if(get_post_meta( $SYT_custom_query, 'syt-draft-mode', true) == "true")
		{
			$output = str_replace( "class='my-editable-post'", "class='my-editable-post' style='background-color:peachpuff; padding: 10px 10px 10px 10px'", $output );
		}
		$custom_fields = get_post_custom($SYT_custom_query);
		// If this post has signatures defined (a list of custom fields of the cprrect names) then handle those
		if( count(get_post_meta( $SYT_custom_query, 'syt_sig')) >0)
		{
			////error_log("IT RECKONS THERE ARE SIGS ON ".$SYT_custom_query."  ".count(get_post_meta( $SYT_custom_query, 'syt_sig')));
			if(!empty($custom_fields['syt_sig']))
            {
                $my_custom_field = $custom_fields['syt_sig'];
			    $customCount ="<div id='syt_all_sigs' name='sigs'>";
		    	$customCount .= "<script type='text/javascript'>function delsig(formID){document.getElementById('input_'+formID+'_1').value = 'DELETING';document.getElementById('input_'+formID+'_2').value = 'Please wait...';document.getElementById('gform_'+formID).submit();}</script>";
                foreach ($my_custom_field as $key => $value) {
                    $a = explode('|', $value);
                    $mybreak = false;
                    if (get_post_meta($SYT_custom_query, 'syt-draft-mode', true) == "true") {
                        // in draft mode with sigs - reset all sigs
                        //error_log("IN DRAFT MODE WITH ALL SIGS - RESET ALL SIGS");
                        add_post_meta($SYT_custom_query, 'syt_sig_archive', $value);
                        update_post_meta($SYT_custom_query, 'syt_sig', urldecode($a[0]) . '|' . urldecode($a[1]) . "||" . (8 + $multiFormSeperator), $value);
                        $multiFormSeperator++;
                    } else {

                        if (!empty($_SESSION['SYT_READ_ONLY_USER']) && $_SESSION['SYT_READ_ONLY_USER']) {
                            //error_log("SESSION VARIABLE OF READ ONLY USER STILL SET");
                            $customCount .= "<br><table width ='100%'>
						<tr>
						<th colspan='3' >Approved By</th>
						</tr>
						<tr>
						<th width='40%'>Name</th>
						<th width='40%'>Role</th>
						<th width='20%'>Approved On</th>
						</tr>
						<tr>
						<td>" . $a[0] . "</td>
						<td>" . $a[1] . "</td>
						<td>" . $a[2] . "</td>
						</tr>
						</table>";
                        } else {
                            //error_log("SET UP SIGS FORM");
                            //add forms
                            $customCount .= "<br><hr>";
                            $customCount .= do_shortcode('[gravityform id="' . (8 + $multiFormSeperator) . '" ajax="true" field_values="multiformid=' . (8 + $multiFormSeperator) . '&syt_sig_post_id=' . $SYT_custom_query . '&syt_sig_original_value=' . urlencode($value) . '&syt_sig_name=' . urlencode($a[0]) . '&syt_sig_role=' . urlencode($a[1]) . '&syt_sig_date=' . urlencode($a[2]) . '&is_approved=' . (($a[2] == "") ? 0 : 1) . '"]');
                            $multiFormSeperator++;
                            if ($multiFormSeperator == 13) $multiFormSeperator = 15;
                            //error_log("after sig set up");
                        }


                    }

                }
                //error_log ("page_posts multiFormSeperator".$multiFormSeperator);
                if(!empty($_SESSION['SYT_READ_ONLY_USER']) && $_SESSION['SYT_READ_ONLY_USER'])
                {
                    if(get_post_meta( $SYT_custom_query, 'syt-draft-mode', true) != "true")
                    {
                        if($multiFormSeperator==12)
                        {
                            $customCount .=  'Maximum signatories reached. For further assistance email <a href="mailto:support@safetyyoutrust.com">support@safetyyoutrust.com</a>';
                        }
                        else
                        {
                            //add the "new signatory" button
                            $customCount .= do_shortcode('[gravityform id="22" title="false" field_values="syt_sig_post_id='.$SYT_custom_query.'&syt_sig_current_index='.(8+$multiFormSeperator).'"]');
                        }
                    }
                }
                $customCount .= "</div>";
                $output .= $customCount;
            }

		}

		return $output;
	}

	protected function paginate_links( $posts ){
		global $wp_query;
		$page_url = home_url( '/' . $wp_query->post->post_name . '/' );
		$page = isset( $_GET['page'] ) ? $_GET['page'] : 1;
		$total_pages = $posts->max_num_pages;
		$per_page = $posts->query_vars['posts_per_page'];
		$curr_page = ( isset( $posts->query_vars['paged'] ) && $posts->query_vars['paged'] > 0	) ? $posts->query_vars['paged'] : 1;
		$prev = ( $curr_page && $curr_page > 1 ) ? '<li><a href="'.$page_url.'?page='. ( $curr_page-1 ).'">Previous</a></li>' : '';
		$next = ( $curr_page && $curr_page < $total_pages ) ? '<li><a href="'.$page_url.'?page='. ( $curr_page+1 ).'">Next</a></li>' : '';
		return '<ul>' . $prev . $next . '</ul>';
	}

	/**
	 *	Build additional Arguments for the WP_Query object
	 *
	 *	@param array $atts Attritubes for building the $args array.
	 */
	protected function set_args( $atts ) {
		global $wp_query;
		$this->args['posts_per_page'] = get_option( 'posts_per_page' );
		// parse the arguments using the defaults
		$this->args = wp_parse_args( $atts, $this->args );

        if(!empty($_SESSION['SYT_READ_ONLY_USER']) && $_SESSION['SYT_READ_ONLY_USER'] )
		{
			//if we areread only then add thr original author to the query
			//$user_custom_fields = get_user_option('s2member_custom_fields',$new_user_id);
            $user_custom_fields = get_user_option('s2member_custom_fields');
			//error_log("trying to modify page in posts query for readonly user ".get_current_user_id()."    >".$user_custom_fields['syt_linked_admin_id']);
			$this->args['author'] = $user_custom_fields['syt_linked_admin_id'];
		}

		// multiple post types are indicated, pass as an array
		if( preg_match( '`,`', $this->args['post_type'] ) ){
			$post_types = explode( ',', $this->args['post_type'] );
			$this->args['post_type'] = $post_types;
		}

		// Show specific posts by ID
		if ( isset( $atts['ids'] ) ) {
			$post_ids = explode( ',', $atts['ids'] );
			$this->args['post__in'] = $post_ids;
			$this->args['posts_per_page'] = count( $post_ids );
		}

		// Use a specified template
		if ( isset( $atts['template'] ) )
			$this->args['template'] = $atts['template'];

		// get posts in a certain category by name (slug)
		if ( isset( $atts['category'] ) ) {
			$this->args['category_name'] = $atts['category'];
		} elseif (	isset( $atts['cats'] ) ) { // get posts in a certain category by id
			$this->args['cat'] =  $atts['cats'];
		}

		// Do a tex query, tax and term a required.
		if( isset( $atts['tax'] ) ) {
			if( isset( $atts['term'] ) ){
				$terms = explode( ',', $atts['term'] );
				$this->args['tax_query'] = array(
					array( 'taxonomy' => $atts['tax'], 'field' => 'slug', 'terms' => ( count( $terms ) > 1 ) ? $terms : $atts['term'] )
				);
			}
		}

		// get posts with a certain tag
		if ( isset( $atts['tag'] ) ) {
			$this->args['tag'] = $atts['tag'];
		}

		// exclude posts with certain category by name (slug)
		if ( isset( $atts['exclude_category'] ) ) {
			$category = $atts['exclude_category'];
			if( preg_match( '`,`', $category ) ) { // multiple
				$category = explode( ',', $category );

				foreach( $category AS $cat ) {
					$term = get_category_by_slug( $cat );
					$exclude[] = '-' . $term->term_id;
				}
				$category = implode( ',', $exclude );

			} else { // single
				$term = get_category_by_slug( $category );
				$category = '-' . $term->term_id;
			}

			if( !is_null( $this->args['cat'] ) ) { // merge lists
				$this->args['cat'] .= ',' . $category;
			}
			$this->args['cat'] = $category;
			// unset our unneeded variables
			unset( $category, $term, $exclude );
		}

		// show number of posts (default is 10, showposts or posts_per_page are both valid, only one is needed)
		if ( isset( $atts['showposts'] ) )
			$this->args[ 'posts_per_page' ] = $atts['showposts'];

		// handle pagination (for code, template pagination is in the template)
		if ( isset( $wp_query->query_vars['page'] ) &&	$wp_query->query_vars['page'] > 1 ) {
			$this->args['paged'] = $wp_query->query_vars['page'];
		}

		if ( ! isset( $this->args['ignore_sticky_posts'] ) ) {
			$this->args['post__not_in'] = get_option( 'sticky_posts' );
		}

		if ( ! isset( $this->args['ignore_sticky_posts'] ) ) {
			$this->args['post__not_in'] = get_option( 'sticky_posts' );
		}

		if ( isset( $this->args['more_tag'] ) ) {
			add_filter( 'excerpt_more', array( &$this, 'custom_excerpt_more' ), 1 );
		}

		$this->args = apply_filters( 'posts_in_page_args', $this->args );

	}

	/**
	 *	Tests if a theme has a theme template file that exists
	 *
	 *	@return true if template exists, false otherwise.
	 */
	protected function has_theme_template() {
		$template_file = ( $this->args['template'] )
			? get_stylesheet_directory()  . '/' . $this->args['template'] // use specified template file
			: get_stylesheet_directory() . '/posts_loop_template.php'; // use default template file

		return ( file_exists( $template_file ) ) ? $template_file : false;
	}

	/**
	 *	Retrieves the post loop template and returns the output
	 *
	 *	@return string results of the output
	 */
	protected function add_template_part( $ic_posts, $singles=false ) {
		if ( $singles ) {
			setup_postdata( $ic_posts );
		} else {
			$ic_posts->the_post();
		}
		$output = '';
		ob_start();
		$output .= apply_filters( 'posts_in_page_pre_loop', '' );
		require ( $file_path = self::has_theme_template() )
			? $file_path // use template file in theme
			: POSTSPAGE_DIR . '/posts_loop_template.php'; // use default plugin template file
		$output .= ob_get_contents();
		$output .= apply_filters( 'posts_in_page_post_loop', '' );
		return ob_get_clean();
	}

	public function custom_excerpt_more( $more ) {
		$more_tag = $this->args['more_tag'];
		return ' <a class="read-more" href="'. get_permalink( get_the_ID() ) . '">' . $more_tag . '</a>';
	}


}
