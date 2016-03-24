<?php get_header(); ?>

<?php 
/** Themify Default Variables
 *  @var object */
global $themify;

?>

<?php 
if( have_posts() ) while ( have_posts() ) : the_post(); 

global $SYT_static_query;
global $SYT_custom_query;
$SYT_static_query = $post->ID;
if(get_query_var( 'revPcID' ))
{
	 $SYT_custom_query = get_query_var( 'revPcID' );
}
?>

	
<!-- body -->
<div id="body" class="clearfix">
	
	<!-- layout -->
	<div id="layout" class="pagewidth clearfix">
		
        <?php themify_content_before(); //hook ?>
		<!-- content -->
		<div id="content" class="clearfix">
        	<?php themify_content_start(); //hook ?>
			
			<?php 
			if(get_query_var( 'revSID' ))
			{
				$oldPost= get_post((int)get_query_var( 'revSID' )); 
				if($oldPost){
					global $post;
				    $post = $oldPost;
				    setup_postdata( $post );
				    if(get_query_var( 'liveDraft' ))
				    {
				    	$docIsDraft =' the current live version of this document.';
				    }else
				    {
				    	$docIsDraft =' an old version of this document.';
				    }
					echo do_shortcode('[themify_box style="orange rounded shadow"]<span style="font-family: Arial, serif;"><b>Note: You are viewing revision '.get_query_var( 'revNo' ).' - which is '.$docIsDraft.'</b></span>[/themify_box]');
				} 
				else
				{
					echo do_shortcode('[themify_box style="red rounded shadow"]<span style="font-family: Arial, serif;"><b>Error: Invalid revision requested. Please contact SYT support.</b></span>[/themify_box]');
				}
				// do a query for the requested revision, if it exists then produce warning message and replace the content
			}
			echo "<div id='draftBanner' style='color:#ffffff;margin-left: auto; margin-right: auto; width: 100%;background-color:orange'></div>";
			echo do_shortcode('[themify_box style="light-blue rounded shadow"]<span style="font-family: Arial, serif;"><b>OCCUPATIONAL HEALTH, SAFETY &amp; WELFARE CONTROL MANUAL</b></span>[/themify_box]');?>
			<?php echo do_shortcode('[themify_col grid="3-1  first"]<b>SECTION:</b> '.get_field("cm_section").'[/themify_col]
			[themify_col grid="3-1 "]<b>18001 Ref:</b> '.get_field("cm_18001ref").'[/themify_col]
			[themify_col grid="3-1 "][/themify_col]'); ?>
<br><hr><br>
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