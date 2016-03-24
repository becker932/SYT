<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Template Box
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */

$fields_default = array(
	'mod_title_box' => '',
	'content_box' => '',
	'appearance_box' => '',
	'color_box' => '',
	'add_css_box' => '',
	'background_repeat' => ''
);

if ( isset( $mod_settings['appearance_box'] ) ) 
	$mod_settings['appearance_box'] = $this->get_checkbox_data( $mod_settings['appearance_box'] );

$fields_args = wp_parse_args( $mod_settings, $fields_default );
extract( $fields_args, EXTR_SKIP );

$container_class = implode(' ', 
	apply_filters('themify_builder_module_classes', array(
		'module', 'module-' . $mod_name, $module_ID
	) ) 
);
$inner_container_classes = implode(' ', apply_filters( 'themify_builder_module_inner_classes', array(
		'module-' . $mod_name . '-content',	'ui', $appearance_box, $color_box, $add_css_box, $background_repeat
	) )
);
?>
<!-- module box -->
<div id="<?php echo $module_ID; ?>" class="<?php echo esc_attr( $container_class ); ?>">
	<?php if ( $mod_title_box != '' ): ?>
	<h3 class="module-title"><?php echo $mod_title_box; ?></h3>
	<?php endif; ?>
	
	<?php do_action( 'themify_builder_before_template_content_render' ); ?>
	
	<div class="<?php echo esc_attr( $inner_container_classes ); ?>">
		<?php echo apply_filters( 'themify_builder_module_content', $content_box ); ?>
	</div>

	<?php do_action( 'themify_builder_after_template_content_render' ); ?>
</div>
<!-- /module box -->