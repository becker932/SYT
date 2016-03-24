 <?php themify_content_start(); //hook ?>
   SOME OLD SHIZZLE <?php 
    add_action('the_content','SYT_Raptor_content_div');
	 function SYT_Raptor_content_div( $content ) {
        global $post;
        //echo 'test the contnet for raptor div';
        error_log("CHECK CONTENT FPR RAPTOR DIV");
        if(current_user_can('edit_post', $post->ID) && in_category('customisable') || current_user_can("subscriber" )&& in_category('customisable')) {
        	//echo 'is customisable';
            error_log("ADD RAPTOR DIV");
            $content = "<div class='raptor-editable-post' data-post_id='{$post->ID}'>{$content}</div>";
        }

        return $content;
    }
    the_content();
   	?>
<?php themify_content_end(); //hook ?>


