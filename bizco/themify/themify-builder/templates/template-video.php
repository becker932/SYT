<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Template Video
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */

$fields_default = array(
	'mod_title_video' => '',
	'style_video' => 'video-top',
	'url_video' => '',
	'width_video' => '',
	'unit_video' => '',
	'title_video' => '',
	'title_link_video' => false,
	'caption_video' => '',
	'css_video' => ''
);

$fields_args = wp_parse_args( $mod_settings, $fields_default );
extract( $fields_args, EXTR_SKIP );

$video_maxwidth = ( empty( $width_video ) ) ? '' : $width_video . $unit_video;
$container_class = implode(' ', 
	apply_filters('themify_builder_module_classes', array(
		'module', 'module-' . $mod_name, $module_ID, $style_video, $css_video
	) )
);
?>

<!-- module video -->
<div id="<?php echo $module_ID; ?>" class="<?php echo esc_attr( $container_class ); ?>">
	
	<?php if ( $mod_title_video != '' ): ?>
	<h3 class="module-title"><?php echo $mod_title_video; ?></h3>
	<?php endif; ?>

	<?php do_action( 'themify_builder_before_template_content_render' ); ?>

	<div class="video-wrap" <?php echo $video_maxwidth != '' ? 'style="max-width:'.$video_maxwidth.';"' : ''; ?>>
		<?php echo themify_parse_video_embed_vars( wp_oembed_get( esc_url( $url_video ) ), esc_url( $url_video ) ); ?>
	</div>
	<!-- /video-wrap -->
	<div class="video-content">
		<h3 class="video-title">
			<?php if ( $title_link_video ) : ?>
			<a href="<?php echo esc_url( $title_link_video ); ?>"><?php echo $title_video; ?></a>
			<?php else: ?>
			<?php echo $title_video; ?>
			<?php endif; ?>
		</h3>
		<div class="video-caption">
			<?php echo apply_filters( 'themify_builder_module_content', $caption_video); ?>
		</div>
		<!-- /video-caption -->
	</div>
	<!-- /video-content -->
	<?php do_action( 'themify_builder_after_template_content_render' ); ?>
</div>
<!-- /module video -->