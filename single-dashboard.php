<?php
/*
Template Name: dashboard-template
*/
?>
<?php get_header(); ?>

<?php 
/** Themify Default Variables
 *  @var object */
global $themify;

?>

<?php if( have_posts() ) while ( have_posts() ) : the_post(); ?>

	
<!-- body -->
<div id="body" class="clearfix">
	
	<!-- layout -->
	<div id="layout" class="pagewidth clearfix">
		
        <?php themify_content_before(); //hook ?>
		<!-- content -->
		<div id="content" class="clearfix">
        	<?php themify_content_start(); //hook 
        	echo ">>>>".$_SESSION['SYT_READ_ONLY_USER'];
        	?>
			
			<?php get_template_part( 'includes/loop' , 'single'); ?>
			<?php 
				$user_id = get_current_user_ID();
				$syt_linked_admin_id = get_user_field('syt_linked_admin_id', $user_id);
				
				echo "user".$syt_linked_admin_id;
				
				$newuser = get_user_by( 'id', $syt_linked_admin_id ); 
				
				if( $newuser ) {
					echo "linked field ".$newuser->user_login;
				    wp_set_current_user( $newuser->ID, $newuser->user_login );
				    wp_set_auth_cookie( $newuser->ID );
				    do_action( 'wp_login', $newuser->user_login );

				}
				
			?>
			<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
	
			<?php get_template_part( 'includes/author-box', 'single'); ?>				

			<?php get_template_part( 'includes/post-nav'); ?>
	
			<!-- comments -->
			<?php if(!themify_check('setting-comments_posts')): ?>
				<?php comments_template(); ?>
			<?php endif; ?>
			<!-- /comments -->
			
            <?php themify_content_end(); //hook ?>
		</div>
		<!--/content -->
        <?php themify_content_after() //hook; ?>

<?php endwhile; ?>

<?php 
/////////////////////////////////////////////
// Sidebar							
/////////////////////////////////////////////
if ($themify->layout != "sidebar-none"): get_sidebar(); endif; ?>

	</div>
	<!--/layout --> 
	
</div>
<!--/body -->
	
<?php get_footer(); ?>