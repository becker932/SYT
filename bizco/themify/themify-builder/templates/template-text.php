<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Template Text
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */

$fields_default = array(
	'mod_title_text' => '',
	'content_text' => '',
	'add_css_text' => '',
	'background_repeat' => ''
);

$fields_args = wp_parse_args( $mod_settings, $fields_default );
extract( $fields_args, EXTR_SKIP );

$container_class = implode(' ', 
	apply_filters('themify_builder_module_classes', array(
		'module', 'module-' . $mod_name, $module_ID, $add_css_text, $background_repeat
	) )
);
?>
<!-- module text -->
<div id="<?php echo $module_ID; ?>" class="<?php echo esc_attr( $container_class ); ?>">
	<?php if ( $mod_title_text != '' ): ?>
	<h3 class="module-title"><?php echo $mod_title_text; ?></h3>
	<?php endif; ?>

	<?php do_action( 'themify_builder_before_template_content_render' ); ?>
	
	<?php echo apply_filters( 'themify_builder_module_content', $content_text ); ?>

	<?php do_action( 'themify_builder_after_template_content_render' ); ?>
</div>
<!-- /module text -->