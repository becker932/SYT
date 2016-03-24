<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Template Accordion
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */

$fields_default = array(
	'mod_title_accordion' => '',
	'layout_accordion' => 'plus-icon-button',
	'expand_collapse_accordion' => 'toggle',
	'color_accordion' => '',
	'accordion_appearance_accordion' => '',
	'content_accordion' => array(),
	'css_accordion' => ''
);

if ( isset( $mod_settings['accordion_appearance_accordion'] ) )
	$mod_settings['accordion_appearance_accordion'] = $this->get_checkbox_data( $mod_settings['accordion_appearance_accordion'] );

$fields_args = wp_parse_args( $mod_settings, $fields_default );
extract( $fields_args, EXTR_SKIP );

$container_class = implode(' ',
	apply_filters( 'themify_builder_module_classes', array( 
		'module', 'module-' . $mod_name, $module_ID, $css_accordion
	) ) 
);
$ui_class = implode(' ', array( 'ui', 'module-' . $mod_name, $layout_accordion, $accordion_appearance_accordion, $color_accordion ) );
?>
<!-- module accordion -->
<div id="<?php echo $module_ID; ?>" class="<?php echo esc_attr( $container_class ); ?>" data-behavior="<?php echo $expand_collapse_accordion; ?>">
	
	<?php if ( $mod_title_accordion != '' ): ?>
	<h3 class="module-title"><?php echo $mod_title_accordion; ?></h3>
	<?php endif; ?>

	<?php do_action( 'themify_builder_before_template_content_render' ); ?>

	<ul class="<?php echo $ui_class; ?>">
		<?php foreach ( $content_accordion as $content ): ?>
		<li>
			<div class="accordion-title"><a href="#"><?php echo $content['title_accordion']; ?></a></div>
			<div class="accordion-content <?php echo $content['default_accordion'] != 'open' ? 'default-closed' : ''; ?> clearfix">
				<?php
					if ( isset( $content['text_accordion'] ) ) {
						echo apply_filters( 'themify_builder_module_content', $content['text_accordion'] );
					}
				?>
			</div>
		</li>
		<?php endforeach; ?>
	</ul>

	<?php do_action( 'themify_builder_after_template_content_render' ); ?>
	
</div>
<!-- /module accordion -->