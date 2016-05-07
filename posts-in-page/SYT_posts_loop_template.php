 <?php themify_content_start(); //hook ?>
    <?php 
    add_action('the_content','SYT_editable_content_div');
	 function SYT_editable_content_div( $content ) {
        global $post;
        //echo 'test the contnet for editable div';
        error_log("CHECK CONTENT FPR                     editable DIV");
        if(current_user_can('edit_post', $post->ID) && in_category('customisable') || current_user_can("subscriber" )&& in_category('customisable')) {
        	//echo 'is customisable';
            error_log("ADD editable DIV");
           // $content = "<div class='editable-editable-post' data-post_id='{$post->ID}'>{$content}</div>";
            $post_id   = $post->ID;
            $post      = get_post( $post_id, OBJECT, 'edit' );
            $content   = $content;
            $editor_id = 'editpost';

            wp_editor( $content, $editor_id );
        }

        return $content;
    }
    the_content();
   	?>
<?php themify_content_end(); //hook ?>


