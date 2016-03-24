<?php get_header(); ?>

<?php
/** Themify Default Variables
 *  @var object */
global $themify;
?>

<?php if(is_front_page() && !is_paged()){ get_template_part( 'includes/slider'); } ?>

<!-- body -->
<div id="body" class="clearfix">

	<!-- layout -->
	<div id="layout" class="pagewidth clearfix">

	<?php if(is_front_page() && !is_paged()){ get_template_part( 'includes/welcome-message'); } ?>

	<?php themify_content_before(); //hook ?>
	<!-- content -->
	<div id="content" class="clearfix">
    	<?php themify_content_start(); //hook ?>
		
		<?php 
		/////////////////////////////////////////////
		// 404							
		/////////////////////////////////////////////
		?>
		<?php if(is_404()): ?>
			<h1 class="page-title" itemprop="name"><?php _e('404','themify'); ?></h1>	
			<p><?php _e( 'Page not found.', 'themify' ); ?></p>	
		<?php endif; ?>

		
		<?php 
		/////////////////////////////////////////////
		// PAGE							
		/////////////////////////////////////////////
		?>
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div id="page-<?php the_ID(); ?>" class="type-page" itemscope itemtype="http://schema.org/Article">
			
			<!-- page-title -->
			<?php if($themify->page_title != "yes"): ?> 
				<h1 class="page-title" itemprop="name"><?php the_title(); ?></h1>
			<?php endif; ?>	
			<!-- /page-title -->
			
			<div class="page-content entry-content" itemprop="articleBody">
			
			<?php the_content(); ?>
			
			<?php wp_link_pages(array('before' => '<p><strong>'.__('Pages:','themify').'</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
			
			
			<!-- comments -->
			<?php if(!themify_check('setting-comments_pages') && $themify->query_category == ""): ?>
				<?php comments_template(); ?>
			<?php endif; ?>
			<!-- /comments -->
			
			</div>
			<!-- /.post-content -->
		
			</div><!-- /.type-page -->
		<?php endwhile; endif; ?>
		
		<?php 
		/////////////////////////////////////////////
		// Query Category							
		/////////////////////////////////////////////
		?>

		<?php 

		if(get_query_var('paged')):
			$paged = get_query_var('paged');
		elseif(get_query_var('page')):
			$paged = get_query_var('page');
		else:
			$paged = 1;
		endif;
		
		if($themify->query_category != ""): ?>
		
			<?php if(themify_get('section_categories') != 'yes'): ?>
			
				<?php query_posts( apply_filters( 'themify_query_posts_page_args', 'cat='.$themify->query_category.'&posts_per_page='.$themify->posts_per_page.'&paged='.$themify->paged.'&order='.$themify->order.'&orderby='.$themify->orderby ) ); ?>
				
					<?php if(have_posts()): ?>
						
						<!-- loops-wrapper -->
						<div id="loops-wrapper" class="loops-wrapper <?php echo $themify->layout . ' ' . $themify->post_layout; ?>">

							<?php while(have_posts()) : the_post(); ?>
								
								<?php get_template_part('includes/loop', 'query'); ?>
						
							<?php endwhile; ?>
												
						</div>
						<!-- /loops-wrapper -->

						<?php if ($themify->page_navigation != "yes"): ?>
							<?php get_template_part( 'includes/pagination'); ?>
						<?php endif; ?>
								
					<?php else : ?>	
					
					<?php endif; ?>

			<?php else: ?>
				
				<?php $categories = explode(",",str_replace(" ","",$themify->query_category)); ?>
				
				<?php foreach($categories as $category): ?>
				
				<?php $category = get_term_by(is_numeric($category)? 'id': 'slug', $category, 'category');
					$cats = get_categories( array( 'include' => isset( $category ) && isset( $category->term_id )? $category->term_id : 0, 'orderby' => 'id' ) ); ?>
				
				<?php foreach($cats as $cat): ?>
					
				<?php query_posts( apply_filters( 'themify_query_posts_page_args', 'cat='.$cat->cat_ID.'&posts_per_page='.$themify->posts_per_page.'&paged='.$themify->paged.'&order='.$themify->order.'&orderby='.$themify->orderby ) ); ?>
			
					<?php if(have_posts()): ?>
						
						<!-- category-section -->
						<div class="category-section clearfix <?php echo $cat->slug; ?>-category">

							<h3 class="category-section-title"><a href="<?php echo esc_url( get_category_link($cat->cat_ID) ); ?>" title="<?php _e('View more posts', 'themify'); ?>"><?php echo $cat->cat_name; ?></a></h3>

							<!-- loops-wrapper -->
							<div id="loops-wrapper" class="loops-wrapper <?php echo $themify->layout . ' ' . $themify->post_layout; ?>">
							<?php while(have_posts()) : the_post(); ?>
								
								<?php get_template_part('includes/loop', 'query'); ?>
						
							<?php endwhile; ?>
							</div>
							<!-- /loops-wrapper -->

							<?php if ($themify->page_navigation != "yes"): ?>
								<?php get_template_part( 'includes/pagination'); ?>
							<?php endif; ?>

						</div>
						<!-- /category-section -->
								
					<?php else : ?>	
					
					<?php endif; ?>
				
				<?php endforeach; ?>
				
				<?php endforeach; ?>
			
			<?php endif; ?>
			
		<?php endif; ?>
		<?php wp_reset_query(); ?>
        
        <?php themify_content_end(); //hook ?>
	</div>
	<!-- /content -->
    <?php themify_content_after() //hook; ?>
	
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
