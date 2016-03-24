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
// Register and load the widget
function syt_load_docnav_widget() {
	
	register_widget( 'syt_onlinedocs_widget' );
}
add_action( 'widgets_init', 'syt_load_docnav_widget' );
/*
//Add an exception to document restrictions for demo content - there is only one or two things available if you are a demo user
function checkForDemoPage($original_value, $vars = array())
{
	global $post;

	if($post)
	{
		if(empty($_SESSION['SYT_DEMO_USER']))return true;
		$demoPostIds = [2289,3160,3162,2331,2342,2365,2396,2838,3814,3854,3884,2852];

		if(in_array( $post->ID, $demoPostIds) || in_array(get_post_meta($post->ID, 'syt-custom-contained-by', true), $demoPostIds) )
		{
			if($_SESSION['SYT_DEMO_USER']) return true;
		}
		
	  	return false;	
	}
		
	return false;	
}

add_filter("ws_plugin__s2member_check_post_level_access_excluded", "checkForDemoPage");

//add an exception to document editing permission for demo content
function give_permissions_for_demo( $allcaps, $cap, $args ) {
	global $post;
	
	//
	if($post)
	{
		
		//if it is the HSW policy page or the users copy of the customisable part
		if(empty($_SESSION['SYT_DEMO_USER']) )
		{
			error_log("Check the session var:".$_SESSION['SYT_DEMO_USER']);
			$allcaps[$cap[0]] = true;
			return $allcaps;
		}
		if($post->ID == 2289 || $post->ID != 2779 && get_post_meta($post->ID, 'syt-custom-contained-by', true) == 2289)
		{
			if($post->ID != 2289 && $_SESSION['SYT_DEMO_USER'])error_log("checking the contained by id of the post ".$post->ID."  ".get_post_meta($post->ID, 'syt-custom-contained-by', true)." ".$_SESSION['SYT_DEMO_USER']);
			if($_SESSION['SYT_DEMO_USER']) $allcaps[$cap[0]] = true;
		}
	}

  	return $allcaps;
}

add_filter( 'user_has_cap', 'give_permissions_for_demo', 0, 3 );*/


?>