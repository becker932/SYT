<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Template Image
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */

$fields_default = array(
	'mod_title_image' => '',
	'style_image' => '',
	'url_image' => '',
	'appearance_image' => '',
	'image_size_image' => '',
	'width_image' => '',
	'height_image' => '',
	'title_image' => '',
	'link_image' => '',
	'param_image' => array(),
	'caption_image' => '',
	'css_image' => ''
);

if ( isset( $mod_settings['appearance_image'] ) ) 
	$mod_settings['appearance_image'] = $this->get_checkbox_data( $mod_settings['appearance_image'] );

if ( isset( $mod_settings['param_image'] ) ) 
	$mod_settings['param_image'] = explode( '|', $mod_settings['param_image'] );

$fields_args = wp_parse_args( $mod_settings, $fields_default );
extract( $fields_args, EXTR_SKIP );

$container_class = implode(' ', 
	apply_filters('themify_builder_module_classes', array(
		'module', 'module-' . $mod_name, $module_ID, $appearance_image, $style_image, $css_image
	) )
);
$lightbox = in_array( 'lightbox', $param_image ) ? true : false;
$zoom = in_array( 'zoom', $param_image ) ? true : false;
$newtab = in_array( 'newtab', $param_image ) ? true : false;
$link_attr = $lightbox ? 'class="lightbox-builder lightbox"' : '';
$link_attr .= $newtab ? ' target="_blank"' : '';
$image_alt = $caption_image ? wp_strip_all_tags( $caption_image ) : esc_attr( $title_image );

$param_image_src = 'src='.esc_url($url_image).'&w='.$width_image .'&h='.$height_image.'&alt='.$image_alt.'&ignore=true';
if ( $this->is_img_php_disabled() ) {
	// get image preset
	$preset = $image_size_image != '' ? $image_size_image : themify_get('setting-global_feature_size');
	if ( isset( $_wp_additional_image_sizes[ $preset ]) && $image_size_image != '') {
		$width_image = intval( $_wp_additional_image_sizes[ $preset ]['width'] );
		$height_image = intval( $_wp_additional_image_sizes[ $preset ]['height'] );
	} else {
		$width_image = $width_image != '' ? $width_image : get_option($preset.'_size_w');
		$height_image = $height_image != '' ? $height_image : get_option($preset.'_size_h');
	}
	$image = '<img src="'.esc_url($url_image).'" alt="'.$image_alt.'" width="'.$width_image.'" height="'.$height_image.'">';
} else {
	$image = themify_get_image($param_image_src);
}

// check whether link is image or url
if ( ! empty( $link_image ) ) {
	$check_img = $this->is_img_link( $link_image );
	if ( ! $check_img && $lightbox ) {
		$link_image = untrailingslashit( add_query_arg( array( 'iframe' => 'true', 'width' => '100%', 'height' => '100%' ), $link_image ) );
	}
}

?>
<!-- module image -->
<div id="<?php echo $module_ID; ?>" class="<?php echo esc_attr( $container_class ); ?>">
	
	<?php if ( $mod_title_image != '' ): ?>
	<h3 class="module-title"><?php echo $mod_title_image; ?></h3>
	<?php endif; ?>

	<?php do_action( 'themify_builder_before_template_content_render' ); ?>

	<div class="image-wrap">
		<?php if ( ! empty( $link_image ) ): ?>
		<a href="<?php echo esc_url( $link_image ); ?>" <?php echo $link_attr; ?>>
			<?php if ( $zoom ): ?>
			<span class="zoom"></span>
			<?php endif; ?>
			<?php echo $image; ?>
		</a>
		<?php else: ?>
			<?php echo $image; ?>
		<?php endif; ?>
	
	<?php if( 'image-overlay' != $style_image ): ?>
	</div>
	<!-- /image-wrap -->
	<?php endif; ?>

	<div class="image-content">
		<h3 class="image-title">
			<?php if ( ! empty( $link_image ) ): ?>
			<a href="<?php echo esc_url( $link_image ); ?>" <?php echo $link_attr; ?>>
				<?php echo $title_image; ?>
			</a>
			<?php else: ?>
			<?php echo $title_image; ?>
			<?php endif; ?>
		</h3>

		<div class="image-caption">
			<?php echo apply_filters( 'themify_builder_module_content', $caption_image ); ?>
		</div>
		<!-- /image-caption -->
	</div>
	<!-- /image-content -->

	<?php if( 'image-overlay' == $style_image ): ?>
	</div>
	<!-- /image-wrap -->
	<?php endif; ?>

	<?php do_action( 'themify_builder_after_template_content_render' ); ?>
</div>
<!-- /module image -->