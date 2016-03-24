<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Template Divider
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */

$fields_default = array(
	'mod_title_divider' => '',
	'style_divider' => '',
	'stroke_w_divider' => '',
	'color_divider' => '',
	'top_margin_divider' => '',
	'bottom_margin_divider' => '',
	'css_divider' => ''
);

if ( isset( $mod_settings['stroke_w_divider'] ) ) 
	$mod_settings['stroke_w_divider'] = 'border-width: '.$mod_settings['stroke_w_divider'].'px;';

if ( isset( $mod_settings['color_divider'] ) ) 
	$mod_settings['color_divider'] = 'border-color: #'.$mod_settings['color_divider'].';';

if ( isset( $mod_settings['top_margin_divider'] ) ) 
	$mod_settings['top_margin_divider'] = 'margin-top: '.$mod_settings['top_margin_divider'].'px;';

if ( isset( $mod_settings['bottom_margin_divider'] ) ) 
	$mod_settings['bottom_margin_divider'] = 'margin-bottom: '.$mod_settings['bottom_margin_divider'].'px;';

$fields_args = wp_parse_args( $mod_settings, $fields_default );
extract( $fields_args, EXTR_SKIP );

$style = $stroke_w_divider . $color_divider . $top_margin_divider . $bottom_margin_divider;
$container_class = implode(' ', 
	apply_filters('themify_builder_module_classes', array(
		'module', 'module-' . $mod_name, $module_ID, $style_divider, $css_divider
	) )
);
?>
<!-- module divider -->
<div id="<?php echo $module_ID; ?>" class="<?php echo esc_attr( $container_class ); ?>" style="<?php echo $style; ?>">
	<?php if ( $mod_title_divider != '' ): ?>
	<h3 class="module-title"><?php echo $mod_title_divider; ?></h3>
	<?php endif; ?>
</div>
<!-- /module divider -->