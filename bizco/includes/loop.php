<?php if(!is_single()) { global $more; $more = 0; } //enable more link ?>

<?php
/** Themify Default Variables
 *  @var object */
global $themify;
?>

<?php themify_post_before(); //hook ?>

<div id="post-<?php the_ID(); ?>" itemscope itemtype="http://schema.org/Article" <?php post_class("post clearfix " . $themify->get_categories_as_classes(get_the_ID())); ?>>

	<?php themify_post_start(); // hook ?>
	
	<?php if ( $themify->hide_image != 'yes' ) : ?>
		
		<?php themify_before_post_image(); // Hook ?>
		
		<?php if ( themify_get( 'video_url' ) != '' ) : ?>
			
			<?php
				global $wp_embed;
				echo $wp_embed->run_shortcode('[embed]' . themify_get('video_url') . '[/embed]');
			?>

		<?php elseif( $post_image = themify_get_image($themify->auto_featured_image . $themify->image_setting . "w=".$themify->width."&h=".$themify->height) ) : ?>
			
			<figure class="post-image <?php echo $themify->image_align; ?>">
				<?php if( 'yes' == $themify->unlink_image): ?>
					<?php echo $post_image; ?>
				<?php else: ?>
					<a href="<?php echo themify_get_featured_image_link(); ?>"><?php echo $post_image; ?><?php themify_zoom_icon(); ?></a>
				<?php endif; // unlink image ?>
			</figure>
		
		<?php endif; // video else image ?>
		
		<?php themify_after_post_image(); // Hook ?>

	<?php endif; // hide image ?>

	<div class="post-content">
		
		<?php if($themify->hide_title != 'yes'): ?>
			<?php themify_before_post_title(); // Hook ?>
			<?php if($themify->unlink_title == 'yes'): ?>
				<h1 class="post-title entry-title" itemprop="name"><?php the_title(); ?></h1>
			<?php else: ?>
				<h1 class="post-title entry-title" itemprop="name"><a href="<?php echo themify_get_featured_image_link(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
			<?php endif; //unlink post title ?>
			<?php themify_after_post_title(); // Hook ?> 
		<?php endif; //post title ?> 
		
		<p class="post-meta entry-meta"> 
			<?php if($themify->hide_date != "yes"): ?>
				<span class="post-date entry-date updated" itemprop="datePublished"><?php the_time(apply_filters('themify_loop_date', 'F j, Y')) ?></span>
			<?php endif; ?>
			<?php if($themify->hide_meta != 'yes'): ?>
				<span class="post-author"><em><?php _e('by', 'themify'); ?></em> <?php echo themify_get_author_link(); ?> &bull;</span>
				<span class="post-category"><?php the_category(', ') ?> &bull;</span>
				<?php the_tags(' <span class="post-tag">' . __('Tags:','themify') . ' ', ', ', '</span> &bull;'); ?>
				<?php  if( !themify_get('setting-comments_posts') && comments_open() ) : ?>
					<span class="post-comment"><?php comments_popup_link( __( '0 Comments', 'themify' ), __( '1 Comment', 'themify' ), __( '% Comments', 'themify' ) ); ?></span>
				<?php endif; //post comment ?>
			<?php endif; //hide meta ?>   
		</p>
		 

		<div class="entry-content" itemprop="articleBody">

		<?php if ( 'excerpt' == $themify->display_content && ! is_attachment() ) : ?>
	
			<?php the_excerpt(); ?>

			<?php if( themify_check('setting-excerpt_more') ) : ?>
				<p><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute('echo=0'); ?>" class="more-link"><?php echo themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify') ?></a></p>
			<?php endif; ?>
	
		<?php elseif ( 'none' == $themify->display_content && ! is_attachment() ) : ?>
	
		<?php else: ?>
		
			<?php the_content(themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify')); ?>
		
		<?php endif; //display content ?>

		</div><!-- /.entry-content -->
		
	</div>
	<!-- /post-content -->

<?php themify_post_end(); //hook ?>
</div>
<!-- /post -->
<?php themify_post_after(); //hook ?>