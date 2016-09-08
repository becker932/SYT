<?php
$args = array(
	'post_type' => 'slider',
	'posts_per_page' => '-1'
);
if(themify_get('setting-slider_posts_category') != 0) {
	$args['tax_query'] = array(
		array(
			'taxonomy' => 'slider-category',
			'field' => 'id',
			'terms' => themify_get('setting-slider_posts_category')
		)
	);
}
if(themify_check('setting-slider_posts_slides')) {
	$args['posts_per_page'] = themify_get('setting-slider_posts_slides');
}
$slides = new WP_Query( apply_filters( 'themify_slider_query_vars', $args ) );

if ( $slides->have_posts() ) : ?>
 
<div id="sliderwrap" class="clearfix">

	<?php themify_slider_before(); //hook ?>
	<div id="slider" class="pagewidth">
        <?php themify_slider_start(); //hook ?>
		<ul class="slides">
			
			<?php while ( $slides->have_posts()) : $slides->the_post(); ?>
				 
				<li id="slider-<?php echo esc_attr( $slides->post->ID ); ?>" <?php post_class(themify_get('layout')); ?>> 
				
				<?php $link = themify_get_featured_image_link('no_permalink=true'); ?>

				<?php 
				$before = '';
				$after = '';
				if( $link != '') {
					$before = '<a href="' . $link . '" title="' . get_the_title() . '">';
					$zoom_icon = themify_check('lightbox_icon')? '<span class="zoom"></span>': '';
					$after = $zoom_icon . '</a>' . $after;
				}  ?>

				<?php 
								  
				if(themify_get('layout') == 'slider-image-only') {
				
					echo $before . themify_get_image('w=978&h=400') . $after;
				
				} else if(themify_get('layout') == 'slider-content-only') {
				
					the_content();
				
				} else if(themify_get('layout') == 'slider-image-caption') {
				
					echo '<div class="image-caption-wrap">';
						echo $before . themify_get_image('w=978&h=400') . $after;
						echo '<div class="caption">';
							echo '<h3>' . $before . get_the_title() . $after . '</h3>';
							the_content();
						echo '</div>';
					echo '</div>';
				
				} else {
				
					echo $before . themify_get_image('w=470&h=400&class=slide-feature-image') . $after;
					echo '<div class="slide-content">';
						echo '<h3>' . $before . get_the_title() . $after . '</h3>';
						the_content();
					echo '</div>';
				}
				?>

			</li>
				<?php endwhile; ?>
		</ul>
        <?php themify_slider_end(); //hook ?>
	</div>
	<!--/slider -->
    <?php themify_slider_after(); //hook ?>
</div>
<!--/sliderwrap -->
<?php endif; ?>
<?php wp_reset_postdata(); ?>

