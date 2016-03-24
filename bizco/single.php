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
        	<?php themify_content_start(); //hook ?>
			
			<?php get_template_part( 'includes/loop' , 'single'); ?>
	
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