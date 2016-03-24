 <?php themify_content_start(); //hook ?>
    <?php 
    add_action('the_content','SYT_Raptor_content_div');
	 function SYT_Raptor_content_div( $content ) {
        global $post;
        //echo 'test the contnet for raptor div';
        if(current_user_can('edit_post', $post->ID) && in_category('customised')) {
        	//echo 'is customisable';
            $content = "<div class='raptor-editable-post' data-post_id='{$post->ID}'>{$content}</div>";
        }

        return $content;
    }
    the_content();
   	?>
<?php themify_content_end(); //hook ?>


