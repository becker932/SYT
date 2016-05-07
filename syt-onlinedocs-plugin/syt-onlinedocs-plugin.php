<?php
/*
Plugin Name: SYT Online Doc Sidebar Widget
Description: Display section links and docoument navigation in sidebar of online docs
*/


// Creating the widget 
class syt_onlinedocs_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
		// Base ID of your widget
		'syt_onlinedocs_widget', 

		// Widget name will appear in UI
		__('SYT Online Doc Sidebar Widget', 'syt_onlinedocs_widget_domain'), 

		// Widget description
		array( 'description' => __( 'Display document nav in sidebar for repo', 'syt_onlinedocs_widget_domain' ), ) 
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
		echo $args['before_title'] . $title . $args['after_title'];

		// This is where we run the code
		echo '<h4><a href="\?page_id=2811">Back to Dashboard</a></h4><hr>';
		$quickLinks = '<h4>Quick links</h4><select onChange="window.location.href=this.value"><br />'.do_shortcode('[syt_show_dropdown_options]').'</select><br><hr><br>';
		echo $quickLinks;

		echo $args['after_widget'];
	}
			
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
			}
			else {
			$title = __( 'New title', 'syt_onlinedocs_widget_domain' );
		}
		// Widget admin form
		?>
			<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
		<?php 
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
}

function custom_excerpt_length( $length ) {
    return 20;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

// Changing excerpt more
function new_excerpt_more($more) {
    global $post;
    return '… <a href="'. get_permalink($post->ID) . '">' . 'Read More &raquo;' . '</a>';
}
add_filter('excerpt_more', 'new_excerpt_more');

// Register and load the widget
function syt_load_docnav_widget() {
	
	register_widget( 'syt_onlinedocs_widget' );
}
add_action( 'widgets_init', 'syt_load_docnav_widget' );

function sytdocedit_process() {
    // do whatever you need in order to process the form.

     $nonce = $_POST['nonce'];
error_log($nonce."   nonce");
 // check to see if the submitted nonce matches with the
 // generated nonce we created earlier
 if ( ! wp_verify_nonce( $nonce, 'syt-ajaxnonce' ) )
     die ( 'Busted!');

    if(!empty($_SESSION['SYT_DEMO_USER']) && $_SESSION['SYT_DEMO_USER'])
    {
        die('Edits will NOT be saved in Demo version.');
    }
    /*  if (!$this->verifyNonce()) {
          header('HTTP/1.0 403 Unauthorized', true, 403);
          die('Access denied');
      }*/
error_log("post content on ajax save".$_POST['content']);
    $postID = $_POST['postID'];
    $post = array();
    $updatedPosts = 0;

    if(current_user_can('edit_post', $postID)){
        $post['ID'] = $postID;
        $post['post_content'] =$_POST['content'];
        if (wp_update_post($post) !== 0) {
            $updatedPosts++;
        }

    }

    if ($updatedPosts == 1) {
        die("{$updatedPosts} post saved successfully {$postID} <");
    } else {
        die("{$updatedPosts} posts saved successfully{$postID} <");
    }
}

function sytdocedit_demo_process() {
    // do whatever you need in order to process the form for demo users
    echo "did some demo stuff";
    die();
}

add_action("wp_ajax_sytdocedit", "sytdocedit_process");

//use this version for if you want the callback to work for users who are not logged in
add_action("wp_ajax_nopriv_sytdocedit", "sytdocedit_demo_process");


function verifyNonce() {
    if (!isset($_POST['nonce'])) {
        return false;
    }
    return wp_verify_nonce($_POST['nonce'], self::SAVE_POSTS_NONCE);
}
function add_highlight_button() {
    if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
        return;
    if ( get_user_option('rich_editing') == 'true') {
        add_filter('mce_external_plugins', 'add_tcustom_tinymce_plugin');
        add_filter('mce_buttons', 'register_tcustom_button');
    }
}

add_action('init', 'add_highlight_button');
function register_tcustom_button( $buttons ) {
    array_push( $buttons, "|", "syt_tc_button" );
    return $buttons;
}
function add_tcustom_tinymce_plugin( $plugin_array ) {

    $plugin_array['syt_tc_button'] = plugins_url( 'sytsavebutton.js', __FILE__ );
    return $plugin_array;
}
?>