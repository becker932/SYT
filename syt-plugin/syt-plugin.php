<?php
/*
Plugin Name: SYT Revision widget
Description: Display revision history in custom sidebar for SYT customisable posts
*/
/* Start Adding Functions Below this Line */


// Creating the widget 
class syt_revision_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
		// Base ID of your widget
		'syt_revision_widget', 

		// Widget name will appear in UI
		__('SYT Revision Widget', 'syt_revision_widget_domain'), 

		// Widget description
		array( 'description' => __( 'Display revision history in sidebar for customisable SYT files', 'syt_revision_widget_domain' ), ) 
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		global $SYT_custom_query;
		global $SYT_static_query ;
		global $SYT_custom_revision;
		global $SYT_static_revision;
		global $SYT_original_custom_id;

		$SYT_custom_revision = $SYT_custom_query;
		$SYT_static_revision = $SYT_static_query;
		$title = apply_filters( 'widget_title', $instance['title'] );
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];

		echo '<h4><a href="\?page_id=2811">Back to Dashboard</a></h4><hr>';
		$quickLinks = '<h4>Quick links</h4>Control Manual Pages:<br><select onChange="window.location.href=this.value">'.do_shortcode('[syt_show_dropdown_options]').'</select><br><hr><a href="?p=2838" >Issue A Document</a><br><hr><br>';

		
		echo $quickLinks;
		if ( ! empty( $title ) )
		echo $args['before_title'] . $title . $args['after_title'];

		// This is where you run the code and display the output
        if(!empty($_SESSION['SYT_DEMO_USER']) && $_SESSION['SYT_DEMO_USER'])
		{
			error_log("IN DEMO MODE IN VERSIONS BOX");
			echo do_shortcode('[themify_box style="orange rounded shadow"]<span style="font-family: Arial, serif;"><b>You are currently in draft mode.</b> In this mode you can edit your documents (by clicking on the coloured area) but they are not live until they are approved. This allows staff to see the current policy rather than one that is under revision. Approving changes and saving edits (which would turn the edit box green and track your versions) is disabled in your Demo membership.</span>[/themify_box]');
		} else 
		{
            error_log("checking if use can edit ".$SYT_custom_revision."   =    ".current_user_can('edit_post', $SYT_custom_revision));
			if(!empty($_SESSION['SYT_READ_ONLY_USER']) && $_SESSION['SYT_READ_ONLY_USER'])
			{
				error_log("READ ONLY USER CANNOT EDIT THIS POST SO NO BUTTON ADDED ".$SYT_custom_revision);
			}else if (current_user_can('edit_post', $SYT_custom_revision) ) 
			{
                error_log("Add a save Draft button ".$SYT_custom_revision);
				//add a svae draft mode button
				$visDB = (get_post_meta( $SYT_custom_revision, 'syt-draft-mode', true) == "false" || isset($_GET["revNo"]) === true)?'none':'block';
				echo "<div id='draft_mode_box' style='display: ".$visDB.";'>";
				echo do_shortcode('[themify_box style="orange rounded shadow"]<span style="font-family: Arial, serif;"><b>You are currently in draft mode.</b> This is not the live version seen by your users. To update the live document click "Approve Changes".</span>[gravityforms id=1 field_values="post_id='. $SYT_custom_revision.'"][/themify_box]');
				echo "</div>";
			}
			else
			{
				error_log("USER CANNOT EDIT THIS POST SO NO BUTTON ADDED ".$SYT_custom_revision."   ".current_user_can('edit_post', $SYT_custom_revision));
			}

			$array1 = wp_get_post_revisions($SYT_custom_query);
			$userFirstRevision = end($array1);
			reset($array1);
			$array2 = wp_get_post_revisions($SYT_static_query);
			$arr =  array_merge($array1, $array2);
			usort($arr, "syt_archive_sort");
			$archive = array();
			// if !autossave and occured after first user interaction then display in archive

			foreach ($arr as $rev) { 
				if($rev->post_date < (get_userdata(get_current_user_id( ))->user_registered)) break;
				if(wp_is_post_autosave($rev))continue;
				if (get_metadata('post', $rev->ID, 'syt-draft-mode', true) == "true") continue;
				if($rev->post_parent == $SYT_custom_query)
				{
					//don't show draft mode custom posts
					$SYT_custom_revision = $rev->ID;
				}
				else if ($rev->post_parent == $SYT_static_query)
				{
					$SYT_static_revision = $rev->ID;
				}
				//echo (($rev->post_parent == $SYT_static_query )?'STATIC':'USER-DEF').': '.$rev->ID.': '.$rev->post_name.' : '.$rev->post_date.' : '.$SYT_static_revision.' : '.$SYT_custom_revision.'<br/>';
				array_push($archive, array('<a href="?p='.$SYT_static_query.'&revSID='.$SYT_static_revision.'&revDID='.$SYT_custom_revision.'&revPcID='.$SYT_custom_query.'&revNo=',mysql2date('l, F jS, Y', $rev->post_date).'</a><br/>'));
			}
			
			$rev_id  = count($archive);

			if($rev_id == 0)
			{
				$output = '<div id="revision_list"><b>There have been no revisions made to this document</b></div>';
				if(get_post_meta( $SYT_custom_query, 'syt-draft-mode', true) == 'true')
				{
					$output .= '<h4 id="revision_list"><b>In Draft Mode.</b></h4>';
				}
			}
			else
			{
				
				if(isset($_GET["revNo"]) === true)
				{

						$output = '<h4><b><div id="revision_list" style="color:orange"> Viewing revision: '.get_query_var( 'revNo' ).'</div></b></h4>';
						if(get_post_meta( $SYT_custom_query, 'syt-draft-mode', true) == 'false')$output .='<a href="?p='.$SYT_static_query.'">'.$rev_id.': Current live version</a><br/>';
				}
				else
				{
					if(get_post_meta( $SYT_custom_query, 'syt-draft-mode', true) == 'true')
					{
						$output = '<h4 id="revision_list"><b>In Draft Mode.</b></h4>';
					}
					else
					{
						$output = '<h4 id="revision_list" style="color:orange">'.$rev_id.': This is the live version</h4>';
					}
				}
				// remove the top result (it is the current revision)
				// and add the unmolested version of the files as the 0 revision
				$tempRevCount = $rev_id;
				foreach ($archive as $str) { 
					if($tempRevCount != $rev_id)
					{
						if($tempRevCount-1 == $rev_id && get_post_meta( $SYT_custom_query, 'syt-draft-mode', true) == 'true')
						{
							//top of version list in draft mode is the current live version
							if(isset($_GET["revNo"]) === true && current_user_can('edit_post',$SYT_custom_query) ) $output .= '<a href="?p='.$SYT_static_query.'">'.($rev_id +1).': Current draft version.</a><br/>';
							if(get_query_var( 'revNo' ) == $rev_id ) $output .= '<div style="color:orange">'.$rev_id.": Current live version</div>";
							else $output .= $str[0].$rev_id.'&liveDraft=1">'.$rev_id.": Current live version</a><br/>";
						}
						else
						{
							if(get_query_var( 'revNo' ) == $rev_id ) $output .= '<div style="color:orange">'.$rev_id.": ".$str[1]."</div>";
							else $output .= $str[0].$rev_id.'">'.$rev_id.": ".$str[1];
						}
					}
					$rev_id --;
					if($rev_id == 0)
					{
						if(isset($_GET["revNo"]) === true && $_GET["revNo"] == $rev_id )
						{
							$output .= '<div style="color:orange">0: '.mysql2date('l, F jS, Y', (get_userdata(get_current_user_id( ))->user_registered))."</div>";
						}
						else
						{
							$output .= '<a href="?p='.$SYT_static_query.'&revSID='.$SYT_static_query.'&revDID='.$SYT_original_custom_id.'&revPcID='.$SYT_custom_query.'&revNo=0">0: '.mysql2date('l, F jS, Y', (get_userdata(get_current_user_id( ))->user_registered)).'</a><br/>';
						}
					}
				}
				
			}
			echo __( $output, 'syt_revision_widget_domain' );
		}
		
		
		echo $args['after_widget'];

	}
			
	// Widget Backend 
	public function form( $instance ) { 
		if ( isset( $instance[ 'title' ] ) ) {
		$title = $instance[ 'title' ];
		}
		else {
		$title = __( 'New title', 'syt_revision_widget_domain' );
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
		error_log("STRIP TAGS IN WIDGET UPDATE");
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
} // Class wpb_widget ends here

// Register and load the widget
function syt_load_widget() {
	
	register_widget( 'syt_revision_widget' );
}
add_action( 'widgets_init', 'syt_load_widget' );

/* used when sorting the posts for the revision widget by date*/
function syt_archive_sort($a, $b)
{
	if($a->post_date == $b->post_date) return 0;
	return ($a->post_date > $b->post_date) ? -1 : 1;

}

/* add valid query vars that are used in all operations */
function add_query_vars_filter( $vars ){
  $vars[] = "revSID";
  $vars[] =  "revDID";
  $vars[] =  "revNo";
  $vars[] =  "revPcID";
  $vars[] =  "liveDraft";
  $vars[] = "syt-ra-postid";
  $vars[] = "syt-ra-hazard";
  $vars[] = "syt-ra-risk";
  $vars[] = "syt-ra-atRisk";
  $vars[] = "syt-ra-currentControls";
  $vars[] = "syt-ra-standard"; 
  $vars[] = "syt-ra-recommendations";
  $vars[] = "syt-ra-standardMet";
  $vars[] = "syt-ra-actionSwitch";
  $vars[] = "syt-ra-actioned";
  $vars[] = "syt-ra-actionedby";
  $vars[] = "syt-dra-title";
  $vars[] = "syt-dra-department";
  $vars[] = "syt-dra-assessor";
  $vars[] = "syt-dra-tasks-being-assessed";
  $vars[] = "syt-dra-date";

  return $vars;
}
add_filter( 'query_vars', 'add_query_vars_filter' );

add_action('after_setup_theme', 'remove_admin_bar');

// no admin bar for anyone - except site admin
function remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin()) {
	  show_admin_bar(false);
	}
}

//////////////////////////////
// form processing  for draft mode off
add_filter("gform_disable_post_creation_1", "disable_post_creation", 10, 3); 
add_action('gform_after_submission_1', 'draft_mode_off', 10, 2);  
add_filter("gform_confirmation_1", "draft_mode_off_confirmation", 10, 4); 
add_filter("gform_disable_notification_1", "disable_notification", 10, 4);  

function disable_post_creation($is_disabled, $form, $entry){ 
 return true;  
}  

function disable_notification($is_disabled, $notification, $form, $entry){  
return true;  
}  

function draft_mode_off_confirmation($confirmation, $form, $lead, $ajax){ 
$confirmation = array("redirect" =>$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ); 
} 

function draft_mode_off($entry, $form) { 

	if (current_user_can('edit_post',$entry["1"]) && get_post_meta( $entry["1"], 'syt-draft-mode', true) == "true") 
	{
		update_post_meta( $entry["1"], 'syt-draft-mode', "false" );
	}
}

/////////////////////////////
//////////////////////////////
// form processing  for signature approval

add_filter("gform_disable_post_creation_7", "syt_sig_disable_post_creation", 10, 3); 
add_action('gform_after_submission_7', 'syt_sig_authorise_func', 10, 2);  
add_filter("gform_confirmation_7", "syt_sig_confirmation_func", 10, 4); 
add_filter("gform_disable_notification_7", "syt_sig_disable_notification", 10, 4);
add_filter("gform_submit_button_7", "syt_form_submit_button", 10, 2);  

add_filter("gform_disable_post_creation_8", "syt_sig_disable_post_creation", 10, 3); 
add_action('gform_after_submission_8', 'syt_sig_authorise_func', 10, 2);  
add_filter("gform_confirmation_8", "syt_sig_confirmation_func", 10, 4); 
add_filter("gform_disable_notification_8", "syt_sig_disable_notification", 10, 4);
add_filter("gform_submit_button_8", "syt_form_submit_button", 10, 2);  

add_filter("gform_disable_post_creation_9", "syt_sig_disable_post_creation", 10, 3); 
add_action('gform_after_submission_9', 'syt_sig_authorise_func', 10, 2);  
add_filter("gform_confirmation_9", "syt_sig_confirmation_func", 10, 4); 
add_filter("gform_disable_notification_9", "syt_sig_disable_notification", 10, 4);
add_filter("gform_submit_button_9", "syt_form_submit_button", 10, 2);  

add_filter("gform_disable_post_creation_10", "syt_sig_disable_post_creation", 10, 3); 
add_action('gform_after_submission_10', 'syt_sig_authorise_func', 10, 2);  
add_filter("gform_confirmation_10", "syt_sig_confirmation_func", 10, 4); 
add_filter("gform_disable_notification_10", "syt_sig_disable_notification", 10, 4);
add_filter("gform_submit_button_10", "syt_form_submit_button", 10, 2);  

add_filter("gform_disable_post_creation_11", "syt_sig_disable_post_creation", 10, 3); 
add_action('gform_after_submission_11', 'syt_sig_authorise_func', 10, 2);  
add_filter("gform_confirmation_11", "syt_sig_confirmation_func", 10, 4); 
add_filter("gform_disable_notification_11", "syt_sig_disable_notification", 10, 4);
add_filter("gform_submit_button_11", "syt_form_submit_button", 10, 2);  

add_filter("gform_disable_post_creation_12", "syt_sig_disable_post_creation", 10, 3); 
add_action('gform_after_submission_12', 'syt_sig_authorise_func', 10, 2);  
add_filter("gform_confirmation_12", "syt_sig_confirmation_func", 10, 4); 
add_filter("gform_disable_notification_12", "syt_sig_disable_notification", 10, 4);
add_filter("gform_submit_button_12", "syt_form_submit_button", 10, 2);  

add_filter("gform_disable_post_creation_13", "syt_sig_disable_post_creation", 10, 3); 
add_action('gform_after_submission_13', 'syt_sig_authorise_func', 10, 2);  
add_filter("gform_confirmation_13", "syt_sig_confirmation_func", 10, 4); 
add_filter("gform_disable_notification_13", "syt_sig_disable_notification", 10, 4);
add_filter("gform_submit_button_13", "syt_form_submit_button", 10, 2);  

add_filter("gform_disable_post_creation_14", "syt_sig_disable_post_creation", 10, 3); 
add_action('gform_after_submission_14', 'syt_sig_authorise_func', 10, 2);  
add_filter("gform_confirmation_14", "syt_sig_confirmation_func", 10, 4); 
add_filter("gform_disable_notification_14", "syt_sig_disable_notification", 10, 4);
add_filter("gform_submit_button_14", "syt_form_submit_button", 10, 2);  

add_filter("gform_disable_post_creation_15", "syt_sig_disable_post_creation", 10, 3); 
add_action('gform_after_submission_15', 'syt_sig_authorise_func', 10, 2);  
add_filter("gform_confirmation_15", "syt_sig_confirmation_func", 10, 4); 
add_filter("gform_disable_notification_15", "syt_sig_disable_notification", 10, 4);
add_filter("gform_submit_button_15", "syt_form_submit_button", 10, 2);  

add_filter("gform_disable_post_creation_16", "syt_sig_disable_post_creation", 10, 3); 
add_action('gform_after_submission_16', 'syt_sig_authorise_func', 10, 2);  
add_filter("gform_confirmation_16", "syt_sig_confirmation_func", 10, 4); 
add_filter("gform_disable_notification_16", "syt_sig_disable_notification", 10, 4);
add_filter("gform_submit_button_16", "syt_form_submit_button", 10, 2);  

add_filter("gform_disable_post_creation_17", "syt_sig_disable_post_creation", 10, 3); 
add_action('gform_after_submission_17', 'syt_sig_authorise_func', 10, 2);  
add_filter("gform_confirmation_17", "syt_sig_confirmation_func", 10, 4); 
add_filter("gform_disable_notification_17", "syt_sig_disable_notification", 10, 4);
add_filter("gform_submit_button_17", "syt_form_submit_button", 10, 2);  

add_filter("gform_disable_post_creation_18", "syt_sig_disable_post_creation", 10, 3); 
add_action('gform_after_submission_18', 'syt_sig_authorise_func', 10, 2);  
add_filter("gform_confirmation_18", "syt_sig_confirmation_func", 10, 4); 
add_filter("gform_disable_notification_18", "syt_sig_disable_notification", 10, 4);
add_filter("gform_submit_button_18", "syt_form_submit_button", 10, 2);  

add_filter("gform_disable_post_creation_19", "syt_sig_disable_post_creation", 10, 3); 
add_action('gform_after_submission_19', 'syt_sig_authorise_func', 10, 2);  
add_filter("gform_confirmation_19", "syt_sig_confirmation_func", 10, 4); 
add_filter("gform_disable_notification_19", "syt_sig_disable_notification", 10, 4);
add_filter("gform_submit_button_19", "syt_form_submit_button", 10, 2);  

add_filter("gform_disable_post_creation_20", "syt_sig_disable_post_creation", 10, 3); 
add_action('gform_after_submission_20', 'syt_sig_authorise_func', 10, 2);  
add_filter("gform_confirmation_20", "syt_sig_confirmation_func", 10, 4); 
add_filter("gform_disable_notification_20", "syt_sig_disable_notification", 10, 4);
add_filter("gform_submit_button_20", "syt_form_submit_button", 10, 2);  

add_filter("gform_disable_post_creation_21", "syt_sig_disable_post_creation", 10, 3); 
add_action('gform_after_submission_21', 'syt_sig_authorise_func', 10, 2);  
add_filter("gform_confirmation_21", "syt_sig_confirmation_func", 10, 4); 
add_filter("gform_disable_notification_21", "syt_sig_disable_notification", 10, 4);
add_filter("gform_submit_button_21", "syt_form_submit_button", 10, 2);  

/*
When amending or editing a signature field at the bottom of a control manual page
We also use this for deleting. When a delte button is pressed all it really does is fill the form in with DELETING and submit, we then 
intercept that here and trigger the removal of the signature.
*/
function syt_sig_authorise_func($entry, $form) { 
	

	if((int)$form['id'] > 6 && (int)$form['id'] < 22)
	{
$notRO = (empty($_SESSION['SYT_READ_ONLY_USER'])  || !empty($_SESSION['SYT_READ_ONLY_USER']) && $_SESSION['SYT_READ_ONLY_USER']==false)?true:false;
		if (current_user_can('edit_post',$entry["4"]) && $notRO )
		{
			//error_log ("syt_sig_authorise_func >".$entry["4"].urldecode($entry["1"]));
			if(urldecode($entry["1"]) == "DELETING")
			{
				
				// loop through all, change if less than our current val - urldecode($entry["6"]
				$custom_fields = get_post_custom($entry["4"]);
				$my_custom_field = $custom_fields['syt_sig'];			
				foreach ( $my_custom_field as $key => $value ) 
				{
					$a = explode( '|', $value );
					error_log ("syt_sig_authorise_func DELETING: value in field ".(int)$a[3]." vs form".(int)urldecode($entry["6"]));
					if((int)$a[3] == (int)urldecode($entry["6"]))
					{
						//delete this value key
						error_log ("syt_sig_authorise_func DELETING ".urldecode($entry["6"]));
						delete_post_meta($entry["4"], 'syt_sig', $value); 
					}
					else if((int)$a[3] > (int)urldecode($entry["6"]))
					{
						// update to be id -1
						$newVal = urldecode($entry["1"])."|".urldecode($entry["2"])."|".urldecode($entry["3"])."|".((int)urldecode($entry["6"])-1);
						error_log ("syt_sig_authorise_func update post meta".$entry["4"]." ".$newVal." ".urldecode($entry["5"]));
						update_post_meta( $entry["4"], 'syt_sig', $newVal, urldecode($entry["5"]));
					}
				}
			}else
			{
				$newVal = urldecode($entry["1"])."|".urldecode($entry["2"])."|".urldecode($entry["3"])."|".urldecode($entry["6"]);
				error_log ("syt_sig_authorise_func update post meta".$entry["4"]." ".$newVal." ".urldecode($entry["5"]));
				update_post_meta( $entry["4"], 'syt_sig', $newVal, urldecode($entry["5"]));
			}
		}
	}
}

// if we are dealing with a signature form (which we can tell from the form id) then we do disable dbase entry, otehrwise we do not
function syt_sig_disable_post_creation($is_disabled, $form, $entry){ 
 	if((int)$form['id'] > 6 && (int)$form['id'] < 22) return true;  
}  

// if we are dealing with a signature form (which we can tell from the form id) then we do disbale a notification, otehrwise we do not
function syt_sig_disable_notification($is_disabled, $notification, $form, $entry){  
	if((int)$form['id'] > 6 && (int)$form['id'] < 22) return true;  
}  

// when we have completed our signature update we force a refresh on the page to ensure the user sees the result
function syt_sig_confirmation_func($confirmation, $form, $lead, $ajax)
{ 
	//error_log ("syt_sig_confirmation_func ".(int)$form['id']);
	echo '<script type="text/javascript">window.parent.location = document.referrer;</script>';
}

// add the confirm or delete buttons to the bottom of the signature forms
function syt_form_submit_button($button, $form){  
	global $SYT_custom_query;
	//error_log ("create form submit button, try to find date value of field:");

	if((int)$form['id'] > 8 && (int)$form['id'] < 22)
	{
		 return "<div style='float:right;align:center;text-align:center;'><a href='#sigs' onclick='delsig(\"".$form["id"]."\")'><img width ='20' height='20' src='http://www.safetyyoutrust.com/wp-content/uploads/2014/10/Delete.png'><br/>Delete this signatory</a></div><button class='button' style='float:left;' id='gform_submit_button_{$form["id"]}'>Confirm and Approve</button>";  
	}
	else if((int)$form['id'] == 8 )
	{
		 return "<div style='float:right;align:center;text-align:center;'></div><button class='button' style='float:left;' id='gform_submit_button_{$form["id"]}'>Confirm and Approve</button>";  
	
	}
   
}  


/////////////////////////////
//////////////////////////////
// form processing for create new signatory

add_filter("gform_disable_notification_22", "disable_notification", 10, 4); 
add_filter("gform_disable_post_creation_22", "add_sig_post_creation", 10, 3); 
add_filter("gform_confirmation_22", "add_sig_confirmation", 10, 4); 
add_filter("gform_submit_button_22", "add_sig_submit_button", 10, 2);  

function add_sig_post_creation($is_disabled, $form, $entry){ 
	error_log ("add_sig_confirmation ".$entry["1"]."  ".$entry["3"]);
	add_post_meta($entry["1"], 'syt_sig', '|||'.$entry["3"]);
 	return true;  
}  

	
function add_sig_confirmation($confirmation, $form, $lead, $ajax){ 
	echo '<script type="text/javascript">window.parent.location = document.referrer;</script>';
	//$confirmation = array("redirect" =>$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ); 
} 

function add_sig_submit_button($button, $form){  
	return "<hr><div style='float:right;align:center;text-align:center;'><input type='image' src='http://www.safetyyoutrust.com/wp-content/uploads/2014/10/Add.png' id='gform_submit_button_22' class='gform_button gform_image_button' alt='Submit' tabindex='1' onclick='if(window[\"gf_submitting_22\"]){return false;}  window[\"gf_submitting_22\"]=true; '/><br>Add new signatory</div>";
}  

/////////////////////////////

//////////////////////////////
// form processing for create new risk assessment 
add_filter("gform_disable_notification_2", "disable_notification", 10, 4);  
add_filter("gform_confirmation_2", "dra_confirmation", 10, 4); 
function dra_confirmation($confirmation, $form, $lead, $ajax){ 

	$post_name = get_permalink($lead["post_id"]);
	$confirmation= '<script type="text/javascript">window.parent.location = "'.$post_name.'";</script>';
	error_log($confirmation);
	echo $confirmation;
} 

/////////////////////////////

//////////////////////////////
// form processing for create new risk assessment task
add_filter("gform_confirmation_3", "dra_task_confirmation", 10, 4); 
add_filter("gform_disable_notification_3", "disable_notification", 10, 4);  

function dra_task_confirmation($confirmation, $form, $lead, $ajax){ 

	echo '<script type="text/javascript">window.parent.location = document.referrer;</script>';
    
} 
/////////////////////////////

//////////////////////////////
// form processing for editing existing risk assessment task
add_filter("gform_confirmation_4", "dra_task_edit_confirmation", 10, 4); 
add_filter("gform_disable_notification_4", "disable_notification", 10, 4);  
add_filter("gform_disable_post_creation_4", "dra_task_edit_disable_post_creation", 10, 3); 
//hijack the post creation and manually update all the custom fields that hold these values
function dra_task_edit_disable_post_creation($is_disabled, $form, $entry){ 
	update_post_meta( $entry[14], 'syt-ra-hazard',$entry[2] );
    update_post_meta( $entry[14], 'syt-ra-risk',$entry[3] );
    update_post_meta( $entry[14], 'syt-ra-atRisk',$entry[4] );
    update_post_meta( $entry[14], 'syt-ra-currentControls',$entry[7] );
    update_post_meta( $entry[14], 'syt-ra-standard',$entry[5] );
    update_post_meta( $entry[14], 'syt-ra-recommendations',$entry[8] );
    update_post_meta( $entry[14], 'syt-ra-standardMet',$entry[6] );
    update_post_meta( $entry[14], 'syt-ra-actionSwitch',$entry[13] );
    update_post_meta( $entry[14], 'syt-ra-actioned',$entry[9] );
    update_post_meta( $entry[14], 'syt-ra-actionedby',$entry[15] );
    return true;
}

//reload the top level page when we have edited a task
function dra_task_edit_confirmation($confirmation, $form, $lead, $ajax){ 
	echo '<script type="text/javascript">window.parent.parent.location.reload();</script>';
} 
/////////////////////////////

// form processing for editing existing risk assessment task
add_filter("gform_confirmation_5", "dra_task_confirmation", 10, 4); 
add_filter("gform_disable_notification_5", "disable_notification", 10, 4);  
add_filter("gform_disable_post_creation_5", "dra_edit_disable_post_creation", 10, 3); 
//
function dra_edit_disable_post_creation($is_disabled, $form, $entry){ 
	update_post_meta( $entry[11], 'syt-dra-title',$entry[10] );
	update_post_meta( $entry[11], 'syt-dra-department',$entry[6] );
    update_post_meta( $entry[11], 'syt-dra-assessor',$entry[7] );
    update_post_meta( $entry[11], 'syt-dra-tasks-being-assessed',$entry[8] );
    update_post_meta( $entry[11], 'syt-dra-date',$entry[9] );
    return true;
}

/////////////////////////////
// when a post is updated we check if it a control manual page being confirmed
// if it is we can turn off the draft mode custom field flag for that post
function syt_save_post_meta( $post_id, $post = null) {

	if ( $post_id ==  null) {
		error_log ("Attempting to save post in syt_save_post_meta with null post_id with id ".$post_id);
        return;
    }

	$parent_id = wp_is_post_revision( $post_id );

	if ( in_category('user_defined',  $parent_id ) ){
		if ( $parent_id ) {

			$parent  = get_post( $parent_id );
			$my_meta = get_post_meta( $parent->ID, 'syt-draft-mode', true );
			if ( false !== $my_meta )
				add_metadata( 'post', $post_id, 'syt-draft-mode', $my_meta );



		}
		update_post_meta( $parent_id, 'syt-draft-mode','true' );

		//if there are any signatories, invalidate them as policy has been changed
		if(count(get_post_meta( $parent_id, 'syt_sig')) >0)
		{
			$custom_fields = get_post_custom($parent_id);
			$my_custom_field = $custom_fields['syt_sig'];			
			foreach ( $my_custom_field as $key => $value ) 
			{
				$a = explode( '|', $value );
				$newVal = $a[0]."|".$a[1]."||".$a[3];
				update_post_meta($parent_id, 'syt_sig', $newVal, $value);

			}
		}

	}
}

add_action( 'save_post', 'syt_save_post_meta' );


//make sure we include gravity form css on pages with forms that will be fired in modals
function syt_custom_gf_scripts()
{
	//echo "in category".in_category('dynamic_risk_assessments',get_the_ID()); 
  //if ( in_category('dynamic_risk_assessments',   get_the_ID() ) )
  //{
     gravity_form_enqueue_scripts(3, true);
     gravity_form_enqueue_scripts(4, true);
     gravity_form_enqueue_scripts(8, true);
  //}
}



/////////////////////////////////////////////
//
//                 S2MEMBER HACKS
//
/////////////////////////////////////////////

//fix for S2 member login redirect

add_filter('ws_plugin__s2member_login_redirect', 's2hack_redirect_all_but_administrators', 10, 2);

function s2hack_redirect_all_but_administrators($bool, $vars) {
	extract($vars);
	if(is_super_admin ($user_id)) return $bool;
	else return true;
}

// add extra subscriber user when a user registers
add_action( 'set_user_role', 'syt_add_read_only_user', 10, 2);
function syt_add_read_only_user($user_id) {
	if( user_can($user_id, 'access_s2member_level4')) 
	{
		//Paying member - create a read only account as well
		$user = get_user_by("id", $user_id);
		$src_custom_fields = get_user_option('s2member_custom_fields', $user_id);
		$hasParent = $src_custom_fields['syt_linked_admin_id'];
		//set author role
		error_log("CREATE USER - TEST FOR EXISTING PARENT ID ".empty($hasParent)."-----".($src_custom_fields['syt_linked_admin_id'] != "" ).($src_custom_fields['syt_linked_admin_id'] != null )." <>  ".($src_custom_fields['syt_linked_admin_id'] != undefined ));
		if(!empty($hasParent) || substr($user->user_login, -5) == "staff")
		{
			return;
		}
		$adminPassword = wp_generate_password(8); 
		$updateData = array(
			'ID' => $user_id,
		    'role'  =>  "author",
		);
		$user->add_cap("edit_posts");
		$user->add_cap("edit_published_posts");
		wp_update_user($user_id,$updateData );
		wp_set_password( $adminPassword, $user_id );

		error_log ("Attempting to add read only user");

		//
		$userPassword = wp_generate_password(8); 
		$userdata = array(
		    'user_login'  =>  $user->user_login."_staff",
		    'user_url'    =>  $user->$website,
		    'role'  =>  "subscriber",
		    'user_pass'   =>  $userPassword

		);

		$new_user_id = wp_insert_user( $userdata ) ;
		error_log ("    read only user id ".$new_user_id );
		$dest_custom_fields = get_user_option('s2member_custom_fields',$new_user_id);
		$dest_custom_fields['syt_organisation'] = $src_custom_fields['syt_organisation'];
		$dest_custom_fields['syt_linked_admin_id'] = $user_id;
		update_user_option($new_user_id, 's2member_custom_fields', $dest_custom_fields);

		$newuser = new WP_User($new_user_id);
		$newuser->add_cap("access_s2member_level2");
		$newuser->add_cap("read_private_posts");

		$site_url = get_bloginfo('wpurl');
		$to = $user->user_email;
		$subject = "[Safety You Trust] Usernames and Passwords";
		$message = "Thanks for joining SafetyYouTrust.com \r\n Your login details for your admin account (which allows you to edit policies) are;\r\n username: ".$user->user_login." \r\npassword: ".$adminPassword."\r\n \r\nLogin details for your read-only account (to be used by staff members who are not authorised to edit policies) are;\r\n username: ".$newuser->user_login." \r\npassword: ".$userPassword."\r\n\r\n http://www.safetyyoutrust.com\r\n\r\nPlease keep these details secure.";
		wp_mail($to, $subject, $message);

	}
	else
	{
		$user = get_user_by("id", $user_id);
		$user ->add_cap("access_s2member_level2");
		//demo user - send email notification
		$adminPassword = wp_generate_password(8); 
		wp_set_password( $adminPassword, $user_id );
		$site_url = get_bloginfo('wpurl');
		$to = $user->user_email;

		$subject = "[Safety You Trust] Username and Passwords";
		$message = "Thanks for joining SafetyYouTrust.com \r\n Your login details for your free account (which allows you to view our demo and post on the forums) are;\r\n username: ".$user->user_login." \r\npassword: ".$adminPassword."\r\n \r\n http://www.safetyyoutrust.com\r\n\r\nPlease keep these details secure.";
		wp_mail($to, $subject, $message);

	}
}

/// Hijack login process to change to parent account if a read only user
// we do this so that the user sees everything the admin user sees, but has their abilities curtailed by the $SYT_READ_ONLY_USER session var
add_action ('wp_authenticate' , 'check_custom_authentication',5,1);

function check_custom_authentication ($username) {
	global $wpdb;

    if (!username_exists($username)) {
		return;
	}
    $userinfo = get_user_by('login', $username);
    error_log("LOGIN: CHECK FOR READ ONLY ".(!is_super_admin($userinfo->ID))."   ".$username."   ".(user_can($userinfo->ID, "subscriber" )));
    error_log(print_r($userinfo , true));
    if(!is_super_admin ($userinfo->ID) && user_can($userinfo->ID, "subscriber" ) )
	{
		//user is a subscriber - olog them in as their parent account and set the session var to read only
		error_log("SET USER TO READ ONLY");
	    $syt_linked_admin_id = get_user_field('syt_linked_admin_id', $userinfo->ID);
	    error_log("USER ".$username."  ".empty($syt_linked_admin_id));
	    if(empty($syt_linked_admin_id))
	    {
	    	$_SESSION['SYT_DEMO_USER'] = true;
	    	return;
	    }

	    $_SESSION['SYT_READ_ONLY_USER'] = true;
	    $_SESSION['SYT_PARENT'] = $syt_linked_admin_id ;
	}
	error_log("LOGIN: NO CHOKE".empty($_SESSION['SYT_READ_ONLY_USER'])."  ".empty($_SESSION['SYT_READ_ONLY_USER']) );
}

// When retrieving posts we check if we are the read only user, if so we modify the query to use the admin user for that accoutn
// the read only user thus sees everything the admin user sees, but has their abilities curtailed by the $SYT_READ_ONLY_USER session var
function syt_pre_get_posts( $query ) {

	if(!empty($_SESSION['SYT_READ_ONLY_USER']) && $_SESSION['SYT_READ_ONLY_USER'] )
	{
		//error_log("ATTEMPTING TO ADD NEW AUTHOR ID TO QUERY", $query->author);
		$query->set('author__in', array($query->author,$_SESSION['SYT_PARENT'],1));
	}
}

add_action( 'pre_get_posts', 'syt_pre_get_posts' );
add_action('init', 'myStartSession', 1);
add_action('wp_logout', 'myEndSession');
add_action('wp_login', 'myEndSession');

//housekeeping for php session variables
function myStartSession() {
    if(!session_id()) {
        session_start();
    }
}

//housekeeping for php session variables
function myEndSession() {
    session_destroy ();
}

///remove edit links from all posts
add_filter( 'edit_post_link', '__return_null' );

//Do not allow anyone other than site administrator to access the wordpress dashboard
function block_dashboard() {
    $file = basename($_SERVER['PHP_SELF']);
    if (is_user_logged_in() && is_admin() && !current_user_can('edit_posts') && $file != 'admin-ajax.php'){
        if(!empty($_SESSION['SYT_READ_ONLY_USER']) && $_SESSION['SYT_READ_ONLY_USER'])
    	{
    		wp_set_current_user($_SESSION['SYT_RO']);
    	}
        wp_redirect( home_url() );
        exit();
    }
}
add_action('init', 'block_dashboard');

///////////////////
//SHORTCODES
///////////////////


// custom shortcode for displaying info based on read only or demo mode
//[syt_read_only]
function syt_read_only_func( $atts, $content = null ) {

	$a = shortcode_atts( array(
		'ifuseris' => 'readOnly',
	), $atts );

	if($a['ifuseris'] == 'demo' && $_SESSION['SYT_DEMO_USER']  || $a['ifuseris'] == 'readOnly' && $_SESSION['SYT_READ_ONLY_USER'] || $a['ifuseris'] == 'readWrite' && empty($_SESSION['SYT_READ_ONLY_USER']) )
	{
		return $content;
	}
	else
	{
		return "";
	}
}

add_shortcode( 'syt_read_only', 'syt_read_only_func' );


//toggle content for users that aren't logged in
function visitor_check_shortcode( $atts, $content = null ) {
	 if ( ( !is_user_logged_in() && !is_null( $content ) ) || is_feed() )
		return $content;
	return '';
}
add_shortcode( 'visitor', 'visitor_check_shortcode' );

//custom shortcode for the number of docs left to sign
function syt_docs_to_sign_func( $atts, $content = null ) {
	//$args = array( 'meta_key' => 'syt_sig', 'category_name' => 'customised');
	//$sig_query = new WP_Query( $args );
	$args = array( 'meta_key' => 'syt_sig','category_name' => 'customised');
	$users_docs_query = new WP_Query( $args );
	$toReview = array();
	while ( $users_docs_query->have_posts() ){
		$requiresSigning = false;
		$users_docs_query->the_post();
		if(get_post_meta( get_the_ID(), 'syt-draft-mode', true) != 'true')
		{
			$result =  get_post_meta(get_the_ID(),'syt_sig');
			//check multiple sigs
			foreach ($result as $sigStr)
			{
				if(substr($sigStr,0,1)=="|")
				{
					$requiresSigning = true;
					break;
				}
			}
		}
		if($requiresSigning)
		{
			array_push($toReview, get_the_ID());
		}
	}
	wp_reset_query();
	if(count($toReview)==0)
	{
        if(!empty($_SESSION['SYT_DEMO_USER']) && $_SESSION['SYT_DEMO_USER'])
		{
			return '5 Policies require signatures (Disabled in Demo Mode)';
		}
		else
		{
			return '0 Policies require signatures';
		}
	}
	else
	{
        if(!empty($_SESSION['SYT_DEMO_USER']) && $_SESSION['SYT_DEMO_USER'])
		{
			return count($toReview).' Policies require signatures (Disabled in Demo Mode)';
		}
		else
		{
			return '<a href="?page_id=3487">'.count($toReview).' Policies require signatures</a>';
		}
	}
}

add_shortcode( 'syt_docs_to_sign', 'syt_docs_to_sign_func' );

//custom shortcode for displaying links to all required signature docs
function syt_display_all_sigs_func( $atts, $content = null ) {
	$args = array( 'meta_key' => 'syt_sig','category_name' => 'customised');
	$users_docs_query = new WP_Query( $args );
	$toReview = array();
	while ( $users_docs_query->have_posts() ){
		$requiresSigning = false;
		$users_docs_query->the_post();
		if(get_post_meta( get_the_ID(), 'syt-draft-mode', true) != 'true')
		{
			$result =  get_post_meta(get_the_ID(),'syt_sig');
			//check multiple sigs
			foreach ($result as $sigStr)
			{
				if(substr($sigStr,0,1)=="|")
				{
					$requiresSigning = true;
					break;
				}
			}
		}
		if($requiresSigning)
		{
			array_push($toReview, get_the_ID());
		}
	}#sigs#sigs

	if(count($toReview)==0)
	{
		return '0 Policies require signatures';
	}
	else
	{
		$str="";
		foreach ($toReview as $thisId)
		{
			$str .= "<a href='?page_id=".get_post_meta($thisId, 'syt-custom-contained-by', true)."#sigs'>".get_the_title(get_post_meta($thisId, 'syt-custom-contained-by', true))."</a><hr/>";
		}
		return $str;
	}
}
add_shortcode( 'syt_display_all_sigs', 'syt_display_all_sigs_func' );

//custom shortcode for the number of docs to review
function syt_docs_to_review_func( $atts, $content = null ) {
	//any docs which do not have a custom page or where the article 
	//is in draft

    if(!empty($_SESSION['SYT_READ_ONLY_USER']) && $_SESSION['SYT_READ_ONLY_USER'])
	{

		$str = " </br>";
	}
	else
	{
		//get posts that are in customisable and user defined
		$args = array( 'category_name' => 'customised');
		$users_docs_query = new WP_Query( $args );
		//get posts from control manual pages that are customisable
		$args = array( 'post_type' => 'controlmanualpage','category_name'  => 'customisable' );
		$control_query = new WP_Query( $args );
		//get posts in draft mode
		$args = array( 'meta_key' => 'syt-draft-mode', 'meta_value' => 'true' );
		$draft_query = new WP_Query( $args );
		$num = ((int)$control_query->found_posts - (int)$users_docs_query->found_posts) + (int)$draft_query->found_posts;
		//subtract one number from the other
        $str="";
        if(!empty($_SESSION['SYT_DEMO_USER']) && $_SESSION['SYT_DEMO_USER'])
		{
			$str .= $num.' Policies to review (Disabled in Demo Mode)';
		}
		else
		{
			$str .= '<a href="?page_id=3451">'.$num.' Policies to review</a>';
		}
	}
	return $str;
}
add_shortcode( 'syt_docs_to_review', 'syt_docs_to_review_func' );

//custom shortcode to list links to all pages needning review
function syt_list_all_to_review_func( $atts, $content = null ) {
	//get posts from control manual pages that are customisable
	$args = array('meta_type' => 'DECIMAL', 'order' => 'DESC' ,'orderby' => 'meta_value','meta_key' => 'CM_section','posts_per_page'=> -1,'post_type' => 'controlmanualpage', 'category_name'  => 'customisable' );
	$control_query = new WP_Query( $args );
	$str ="";
	$toReview = array();
	while ( $control_query->have_posts() ){
		$control_query->the_post();
		array_push($toReview, get_the_ID());
	}
	foreach ($toReview as $thisId)
	{
		$args = array( 'meta_key' => 'syt-original-doc', 'meta_value' => $thisId );
		$draft_query = new WP_Query( $args );
		if($draft_query->found_posts)
		{
			$draft_query->the_post();
			if(get_post_meta( get_the_ID(), 'syt-draft-mode', true)=='true')
			{
				$str .= "<div style='color:orange'><b>In draft mode:</b></div><a href='?page_id=".get_post_meta( get_the_ID(), 'syt-custom-contained-by', true)."'>".get_the_title(get_post_meta( get_the_ID(), 'syt-custom-contained-by', true))."</a><hr/>";
			} 
		}
		else
		{
			$str .= "<a href='?page_id=".get_post_meta($thisId, 'syt-custom-contained-by', true)."'>".get_the_title(get_post_meta($thisId, 'syt-custom-contained-by', true))."</a><hr/>";
		}
		//wp_reset_query();
	}

	return $str;
}
add_shortcode( 'syt_list_all_to_review', 'syt_list_all_to_review_func' );

//custom shortcode for the number of updates since last login
function syt_updates_alert_func( $atts, $content = null ) {
	return "0 Updates and Newsletters since your last login<br/>&nbsp;";
}
add_shortcode( 'syt_updates_alert', 'syt_updates_alert_func' );

//custom shortcode to disply popup for inline profile edit
function syt_edit_profile_link_func( $atts, $content = null ) {
    if(!empty($_SESSION['SYT_READ_ONLY_USER']) && $_SESSION['SYT_READ_ONLY_USER'])
	{
		return '';
	}
	else
	{
		return "<a href=\"#\" onclick=\"window.open('http://www.safetyyoutrust.com/?s2member_profile=1', '_popup', 'width=600,height=400,left=100,screenX=100,top=100,screenY=100,location=0,menubar=0,toolbar=0,status=0,scrollbars=1,resizable=1'); return false;\">Edit My Details</a> | ";
	}
}
add_shortcode( 'syt_edit_profile_link', 'syt_edit_profile_link_func' );

//custom shortcode displaying the options part of a dropdown containg the quicklinks
function syt_show_dropdown_options_func( $atts, $content = null ) {

	if(!empty($_SESSION['SYT_DEMO_USER']) && $_SESSION['SYT_DEMO_USER'])
	{
		
		$str = 	'<option value="#">Select a section...</option>
		<option  disabled="disabled">Amendment Register</option>
		<option  disabled="disabled">General Requirements</option>
	<option value="?p=2289">DEMO: HS&amp;W Policy Statement</option>
	<optgroup label = "Planning">
		<option disabled="disabled">Contents</option>
		<option disabled="disabled">Hazard id, R.A.s + controls</option>
		<option disabled="disabled">Legal + Other requirements</option>
		<option disabled="disabled">Objectives + Programmes</option>
	</optgroup>
	<optgroup label = "Implementation + Operations">
		<option disabled="disabled">Contents</option>
		<option disabled="disabled">Resources + responsibilities</option>
		<option value="?p=3160">DEMO: Management \ Board</option>
		<option value="?p=3162">DEMO: Chief Exec.</option>
		<option disabled="disabled">-- Senior HSW Manager</option>
		<option disabled="disabled">-- Heads of Depts.</option>
		<option disabled="disabled">-- Employees \ Comitteee</option>
		<option disabled="disabled">-- HS Administrator</option>
		<option disabled="disabled">-- HS+W Champions</option>
		<option disabled="disabled">Training + Awareness</option>
		<option disabled="disabled">Communication + participation</option>
		<option disabled="disabled">Documentation</option>
		<option disabled="disabled">Control of Docs.</option>
		<option disabled="disabled">Operational control</option>
		<option disabled="disabled">Emergency preparedness</option>
	</optgroup>
	<optgroup label = "Checking">
		<option disabled="disabled">Contents</option>
		<option disabled="disabled">Performance measurement</option>
		<option disabled="disabled">Evaluation of compliance</option>
		<option value="?p=2331">DEMO: Incident Investigation</option>
		<option disabled="disabled">Control of Records</option>
		<option disabled="disabled">Internal Audit</option>
	</optgroup>
	<option disabled="disabled">Management Review</option>
	<optgroup label = "Topic Specific Policies ">
		<option disabled="disabled">Contents</option>
		<option value="?p=2342">DEMO: Accidents + First-aid</option>
		<option disabled="disabled">Alcohol, Drugs + Smoking</option>
		<option disabled="disabled">Asbestos</option>
		<option disabled="disabled">Blood, Body Fluids + Sharps</option>
		<option disabled="disabled">Confined Spaces</option>
		<option disabled="disabled">Contractor Management</option>
		<option disabled="disabled">Display Screen Equipment</option>
		<option disabled="disabled">Electricity</option>
		<option disabled="disabled">EM + NI Radiation</option>
		<option value="?p=2365">DEMO: Fire Safety</option>
		<option disabled="disabled">Gas Safety</option>
		<option disabled="disabled">Hazardous Substances</option>
		<option disabled="disabled">Homeworking</option>
		<option disabled="disabled">Lifts + Escalators</option>
		<option disabled="disabled">Lone Working</option>
		<option disabled="disabled">Manual Handling</option>
		<option disabled="disabled">New + Expectant Mothers</option>
		<option disabled="disabled">Noise</option>
		<option disabled="disabled">Personal Protective Equipment</option>
		<option disabled="disabled">Stress Management</option>
		<option disabled="disabled">Terrorism Threat</option>
		<option disabled="disabled">Vehicles + Occup. Driving</option>
		<option disabled="disabled">Violence + Aggression</option>
		<option value="?p=2396">DEMO: Water Systems – Legionella</option>
		<option disabled="disabled">Wellbeing + O.H.</option>
		<option disabled="disabled">Work at Height</option>
		<option disabled="disabled">Workplace + Env. Conditions</option>
		<option disabled="disabled">Young Persons</option>
	</optgroup>';
	}
	else
	{
		$str = '<option value="#">Select a section...</option>
	<option value="?p=2270">Full Contents</option>
	<option value="?p=2279">Amendment Register</option>
	<option value="?p=2284">General Requirements</option>
	<option value="?p=2289">HS&amp;W Policy Statement</option>
	<optgroup label = "Planning">
		<option value="?p=2291">Contents</option>
		<option value="?p=2293">Hazard id, R.A.s + controls</option>
		<option value="?p=2295">Legal + Other requirements</option>
		<option value="?p=2298">Objectives + Programmes</option>
	</optgroup>
	<optgroup label = "Implementation + Operations">
		<option value="?p=2301">Contents</option>
		<option value="?p=2303">Resources + responsibilities</option>
		<option value="?p=3160">-- Management \ Board</option>
		<option value="?p=3162">-- Chief Exec.</option>
		<option value="?p=3164">-- Senior HSW Manager</option>
		<option value="?p=3166">-- Heads of Depts.</option>
		<option value="?p=3168">-- Employees \ Comitteee</option>
		<option value="?p=3182">-- HS Administrator</option>
		<option value="?p=3184">-- HS+W Champions</option>
		<option value="?p=2306">Training + Awareness</option>
		<option value="?p=2310">Communication + participation</option>
		<option value="?p=2312">Documentation</option>
		<option value="?p=2315">Control of Docs.</option>
		<option value="?p=2317">Operational control</option>
		<option value="?p=2320">Emergency preparedness</option>
	</optgroup>
	<optgroup label = "Checking">
		<option value="?p=2322">Contents</option>
		<option value="?p=2325">Performance measurement</option>
		<option value="?p=2328">Evaluation of compliance</option>
		<option value="?p=2331">Incident Investigation</option>
		<option value="?p=2333">Control of Records</option>
		<option value="?p=2335">Internal Audit</option>
	</optgroup>
	<option value="?p=2337">Management Review</option>
	<optgroup label = "Topic Specific Policies ">
		<option value="?p=2339">Contents</option>
		<option value="?p=2342">Accidents + First-aid</option>
		<option value="?p=2344">Alcohol, Drugs + Smoking</option>
		<option value="?p=2346">Asbestos</option>
		<option value="?p=2348">Blood, Body Fluids + Sharps</option>
		<option value="?p=2350">Confined Spaces</option>
		<option value="?p=2353">Contractor Management</option>
		<option value="?p=2355">Display Screen Equipment</option>
		<option value="?p=2358">Electricity</option>
		<option value="?p=2362">EM + NI Radiation</option>
		<option value="?p=2365">Fire Safety</option>
		<option value="?p=2367">Gas Safety</option>
		<option value="?p=2369">Hazardous Substances</option>
		<option value="?p=2371">Homeworking</option>
		<option value="?p=2373">Lifts + Escalators</option>
		<option value="?p=2375">Lone Working</option>
		<option value="?p=2378">Manual Handling</option>
		<option value="?p=2380">New + Expectant Mothers</option>
		<option value="?p=2383">Noise</option>
		<option value="?p=2385">Personal Protective Equipment</option>
		<option value="?p=2388">Stress Management</option>
		<option value="?p=2390">Terrorism Threat</option>
		<option value="?p=2392">Vehicles + Occup. Driving</option>
		<option value="?p=2394">Violence + Aggression</option>
		<option value="?p=2396">Water Systems – Legionella</option>
		<option value="?p=2398">Wellbeing + O.H.</option>
		<option value="?p=2400">Work at Height</option>
		<option value="?p=2403">Workplace + Env. Conditions</option>
		<option value="?p=2405">Young Persons</option>
	</optgroup>';
	}
	return $str;
}
add_shortcode( 'syt_show_dropdown_options', 'syt_show_dropdown_options_func' );


//custom shortcode displaying the options part of a dropdown containg the quicklinks to downloadable templates
function syt_show_dropdown_downloads_func( $atts, $content = null ) {
    if(!empty($_SESSION['SYT_DEMO_USER']) && $_SESSION['SYT_DEMO_USER'])
	{
		$str = '<option value="#">Select a section...</option>
		<optgroup label = "Employee Declarations Templates" >
		<option  value="#" disabled="disabled">Vehicle Driver Declaration Template</option></optgroup>
		<optgroup label = "Fire Safety Templates"><option  value="#" disabled="disabled">Fire Action Procedure Example</option>
		<option  value="#" disabled="disabled">Fire Log Book Template</option></optgroup>
		<optgroup label = "Guidance Notes and Hazard Information"><option  value="#" disabled="disabled">Display Screen Equipment - Guidance on Good Practice</option>
		<option value="#" disabled="disabled">HSE Guidance - H&amp;S Made Simple</option>
		<option  value="#" disabled="disabled">HSE Guidance - Lone Working</option></optgroup>
		<optgroup label = "Hazard Identification Templates"><option  value="#" disabled="disabled">Hazard Identification Template</option></optgroup>
		<optgroup label = "Inspections, Checks, Investigations and Registers Templates"><option  value="#" disabled="disabled">Accident and Incident Investigation Template
		</option><option  value="#" disabled="disabled">Ladder Inspection Template</option>
		<option  value="#" disabled="disabled">Personal Protective Equipment (PPE) Register Template</option>
		<option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level0//Vehicle Check Template.docx">DEMO: Vehicle Check Template</option>
		<option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level0//Workplace Inspection Record Template.docx">DEMO: Workplace Inspection Record Template</option></optgroup>
		<optgroup label = "Legal Review Templates"><option  value="#" disabled="disabled">Legal and Current Issues Review Template</option></optgroup>
		<optgroup label = "Objectives and Programmes Templates"><option  value="#" disabled="disabled">HS&amp;W Objectives Template</option></optgroup>
		<optgroup label = "Risk Assessment Models and Templates"><option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level0//DSE Risk Assessment Template.docx">DEMO: DSE Risk Assessment</option>
		<option  value="#" disabled="disabled">Hazardous Substances (COSHH) Assessment Template</option>
		<option  value="#" disabled="disabled">Manual Handling Risk Assessment Template</option>
		<option  value="#" disabled="disabled">Expectant Mothers RTemplate</option>
		<option  value="#" disabled="disabled" > Stress Risk Assessment Template</option>
		</optgroup>
		<optgroup label = "Online Risk Assessment Generators" >
		<option  value="#" disabled="disabled">General Risk Assessment Generator</option>
		<option value="#" disabled="disabled">Dynamic Risk Assessment Generator</option>
		</optgroup>';
	}
	else
	{
		$str = '<option value="#">Select a section...</option>
		<option value = "?p=2838" >Issue A Document: Contents</option>
		<optgroup label = "Employee Declarations Templates" >
		<option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//Vehicle Driver Declaration Template.docx">Vehicle Driver Declaration</option></optgroup>
		<optgroup label = "Fire Safety Templates"><option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//Fire Action Procedure Example.docx">Fire Procedure Example</option>
		<option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//Fire Log Book Template.docx">Fire Log Book Template</option></optgroup>
		<optgroup label = "Guidance Notes and Hazard Information"><option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//DSE Guidance on Good Practice.docx">Display Screen Equipment Guidance</option>
		<option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//HS Law Leaflet from HSE.pdf">H&amp;S Law Leaflet from HSE</option>
		<option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//HSE Guidance HS Made Simple.pdf">HSE Guidance   H&amp;S Made Simple</option>
		<option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//HSE Guidance Lone Working.pdf">HSE Guidance   Lone Working</option></optgroup>
		<optgroup label = "Hazard Identification Templates"><option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//Hazard Identification Template.docx">Hazard Identification</option></optgroup>
		<optgroup label = "Inspections, Checks, Investigations and Registers"><option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//Accident and Incident Investigation Template.docx">Accident and Incident Investigation Template
		</option><option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//Ladder Inspection Template.docx">Ladder Inspection</option>
		<option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//Personal Protective Equipment PPE Register Template.docx">Personal Protective Equipment Register</option>
		<option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//Vehicle Check Template.docx">Vehicle Check Template</option>
		<option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//Workplace Inspection Record Template.docx">Workplace Inspection Record Template</option></optgroup>
		<optgroup label = "Legal Review Templates"><option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//Legal and Current Issues Review Template.docx">Legal and Current Issues Review </option></optgroup>
		<optgroup label = "Objectives and Programmes Templates"><option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//HSW Objectives Template.docx">HS&amp;W Objectives</option></optgroup>
		<optgroup label = "Risk Assessment Models and Templates"><option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//DSE Risk Assessment Template.docx">DSE Risk Assessment</option>
		<option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//Hazardous Substances COSHH Assessment Template.docx">Hazardous Substances (COSHH) Assessment</option>
		<option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//Manual Handling Risk Assessment Template.docx">Manual Handling Risk Assessment</option>
		<option value="http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//New and Expectant Mothers Risk Assessment Template.docx">New and Expectant Mothers Risk Assessment</option>
		<option value = "http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//Stress Risk Assessment Template.docx" > Stress Risk Assessment</option>
		</optgroup>
		<optgroup label = "Offline Risk Assessment Templates" >
		<option value = "http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//General Risk Assessment Template.docx" >General Risk Assessment Template</option>
		<option value = "http://www.safetyyoutrust.com/?s2member_file_download=access-s2member-level1//Dynamic Risk Assessment Template.docx" >Dynamic Risk Assessment Template</option>
		</optgroup>
		<optgroup label = "Online Risk Assessment Generators" >
		<option value = "?page_id=3043" >General Risk Assessment Generator</option>
		<option value = "?page_id=3623" >Dynamic Risk Assessment Generator</option>
		</optgroup>';
	}
	return $str;
}
add_shortcode( 'syt_show_dropdown_downloads', 'syt_show_dropdown_downloads_func' );

?>