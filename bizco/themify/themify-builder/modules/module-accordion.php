<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Accordion
 * Description: Display Accordion content
 */

class TB_Accordion_Module extends Themify_Builder_Module {
	function __construct() {
		parent::__construct( array(
			'name' => __('Accordion', 'themify'),
			'slug' => 'accordion'
		));
	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_Accordion_Module',
	apply_filters( 'themify_builder_module_accordion', array(
		'options' => array(
			array(
				'id' => 'mod_title_accordion',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large'
			),
			array(
				'id' => 'layout_accordion',
				'type' => 'layout',
				'label' => __('Accordion layout', 'themify'),
				'options' => array(
					array('img' => 'accordion-default.png', 'value' => 'default', 'label' => __('Plus Icon Button', 'themify')),
					array('img' => 'accordion-separate.png', 'value' => 'separate', 'label' => __('Pipe', 'themify'))
				)
			),
			array(
				'id' => 'expand_collapse_accordion',
				'type' => 'radio',
				'label' => __('Expand / Collapse', 'themify'),
				'default' => 'toggle',
				'options' => array(
					'toggle' => __('Toggle <small>(only clicked item is toggled)</small>', 'themify'),
					'accordion' => __('Accordion <small>(collapse all, but keep clicked item expanded)</small>', 'themify')
				),
				'break' => true
			),
			array(
				'id' => 'color_accordion',
				'type' => 'layout',
				'label' => __('Accordion Color', 'themify'),
				'options' => array(
					array('img' => 'color-default.png', 'value' => 'default', 'label' => __('default', 'themify')),
					array('img' => 'color-black.png', 'value' => 'black', 'label' => __('black', 'themify')),
					array('img' => 'color-grey.png', 'value' => 'gray', 'label' => __('gray', 'themify')),
					array('img' => 'color-blue.png', 'value' => 'blue', 'label' => __('blue', 'themify')),
					array('img' => 'color-light-blue.png', 'value' => 'light-blue', 'label' => __('light-blue', 'themify')),
					array('img' => 'color-green.png', 'value' => 'green', 'label' => __('green', 'themify')),
					array('img' => 'color-light-green.png', 'value' => 'light-green', 'label' => __('light-green', 'themify')),
					array('img' => 'color-purple.png', 'value' => 'purple', 'label' => __('purple', 'themify')),
					array('img' => 'color-light-purple.png', 'value' => 'light-purple', 'label' => __('light-purple', 'themify')),
					array('img' => 'color-brown.png', 'value' => 'brown', 'label' => __('brown', 'themify')),
					array('img' => 'color-orange.png', 'value' => 'orange', 'label' => __('orange', 'themify')),
					array('img' => 'color-yellow.png', 'value' => 'yellow', 'label' => __('yellow', 'themify')),
					array('img' => 'color-red.png', 'value' => 'red', 'label' => __('red', 'themify')),
					array('img' => 'color-pink.png', 'value' => 'pink', 'label' => __('pink', 'themify'))
				)
			),
			array(
				'id' => 'accordion_appearance_accordion',
				'type' => 'checkbox',
				'label' => __('Accordion Appearance', 'themify'),
				'default' => array(
					'rounded', 
					'gradient'
				),
				'options' => array(
					array( 'name' => 'rounded', 'value' => __('Rounded', 'themify')),
					array( 'name' => 'gradient', 'value' => __('Gradient', 'themify')),
					array( 'name' => 'glossy', 'value' => __('Glossy', 'themify')),
					array( 'name' => 'embossed', 'value' => __('Embossed', 'themify')),
					array( 'name' => 'shadow', 'value' => __('Shadow', 'themify'))
				)
			),
			array(
				'id' => 'content_accordion',
				'type' => 'builder',
				'options' => array(
					array(
						'id' => 'title_accordion',
						'type' => 'text',
						'label' => __('Accordion Title', 'themify'),
						'class' => 'large'
					),
					array(
						'id' => 'text_accordion',
						'type' => 'wp_editor',
						'label' => false,
						'class' => 'fullwidth',
						'rows' => 6
					),
					array(
						'id' => 'default_accordion',
						'type' => 'radio',
						'label' => __('Default', 'themify'),
						'default' => 'toggle',
						'options' => array(
							'closed' => __('closed', 'themify'),
							'open' => __('open', 'themify')
						)
					)
				)
			)
		),
		// Styling
		'styling' => array(
			array(
				'id' => 'separator_image_background',
				'title' => '',
				'description' => '',
				'type' => 'separator',
				'meta' => array('html'=>'<h4>'.__('Background', 'themify').'</h4>'),
			),
			array(
				'id' => 'background_color',
				'type' => 'color',
				'label' => __('Background Color', 'themify'),
				'class' => 'small'
			),
			// Font
			array(
				'type' => 'separator',
				'meta' => array('html'=>'<hr />')
			),
			array(
				'id' => 'separator_font',
				'type' => 'separator',
				'meta' => array('html'=>'<h4>'.__('Font', 'themify').'</h4>'),
			),
			array(
				'id' => 'font_family',
				'type' => 'font_select',
				'label' => __('Font Family', 'themify'),
				'class' => 'font-family-select'
			),
			array(
				'id' => 'font_color',
				'type' => 'color',
				'label' => __('Font Color', 'themify'),
				'class' => 'small'
			),
			array(
				'id' => 'multi_font_size',
				'type' => 'multi',
				'label' => __('Font Size', 'themify'),
				'fields' => array(
					array(
						'id' => 'font_size',
						'type' => 'text',
						'class' => 'xsmall'
					),
					array(
						'id' => 'font_size_unit',
						'type' => 'select',
						'meta' => array(
							array('value' => '', 'name' => ''),
							array('value' => 'px', 'name' => __('px', 'themify')),
							array('value' => 'em', 'name' => __('em', 'themify'))
						)
					)
				)
			),
			array(
				'id' => 'multi_line_height',
				'type' => 'multi',
				'label' => __('Line Height', 'themify'),
				'fields' => array(
					array(
						'id' => 'line_height',
						'type' => 'text',
						'class' => 'xsmall'
					),
					array(
						'id' => 'line_height_unit',
						'type' => 'select',
						'meta' => array(
							array('value' => '', 'name' => ''),
							array('value' => 'px', 'name' => __('px', 'themify')),
							array('value' => 'em', 'name' => __('em', 'themify')),
							array('value' => '%', 'name' => __('%', 'themify'))
						)
					)
				)
			),
			// Link
			array(
				'type' => 'separator',
				'meta' => array('html'=>'<hr />')
			),
			array(
				'id' => 'separator_link',
				'type' => 'separator',
				'meta' => array('html'=>'<h4>'.__('Link', 'themify').'</h4>'),
			),
			array(
				'id' => 'link_color',
				'type' => 'color',
				'label' => __('Color', 'themify'),
				'class' => 'small'
			),
			array(
				'id' => 'text_decoration',
				'type' => 'select',
				'label' => __( 'Text Decoration', 'themify' ),
				'meta'	=> array(
					array('value' => '',   'name' => '', 'selected' => true),
					array('value' => 'underline',   'name' => __('Underline', 'themify')),
					array('value' => 'overline', 'name' => __('Overline', 'themify')),
					array('value' => 'line-through',  'name' => __('Line through', 'themify')),
					array('value' => 'none',  'name' => __('None', 'themify'))
				)
			),
			// Padding
			array(
				'type' => 'separator',
				'meta' => array('html'=>'<hr />')
			),
			array(
				'id' => 'separator_padding',
				'type' => 'separator',
				'meta' => array('html'=>'<h4>'.__('Padding', 'themify').'</h4>'),
			),
			array(
				'id' => 'multi_padding',
				'type' => 'multi',
				'label' => __('Padding', 'themify'),
				'fields' => array(
					array(
						'id' => 'padding_top',
						'type' => 'text',
						'description' => __('top', 'themify'),
						'class' => 'xsmall'
					),
					array(
						'id' => 'padding_right',
						'type' => 'text',
						'description' => __('right', 'themify'),
						'class' => 'xsmall'
					),
					array(
						'id' => 'padding_bottom',
						'type' => 'text',
						'description' => __('bottom', 'themify'),
						'class' => 'xsmall'
					),
					array(
						'id' => 'padding_left',
						'type' => 'text',
						'description' => __('left (px)', 'themify'),
						'class' => 'xsmall'
					)
				)
			),
			// Margin
			array(
				'type' => 'separator',
				'meta' => array('html'=>'<hr />')
			),
			array(
				'id' => 'separator_margin',
				'type' => 'separator',
				'meta' => array('html'=>'<h4>'.__('Margin', 'themify').'</h4>'),
			),
			array(
				'id' => 'multi_margin',
				'type' => 'multi',
				'label' => __('Margin', 'themify'),
				'fields' => array(
					array(
						'id' => 'margin_top',
						'type' => 'text',
						'description' => __('top', 'themify'),
						'class' => 'xsmall'
					),
					array(
						'id' => 'margin_right',
						'type' => 'text',
						'description' => __('right', 'themify'),
						'class' => 'xsmall'
					),
					array(
						'id' => 'margin_bottom',
						'type' => 'text',
						'description' => __('bottom', 'themify'),
						'class' => 'xsmall'
					),
					array(
						'id' => 'margin_left',
						'type' => 'text',
						'description' => __('left (px)', 'themify'),
						'class' => 'xsmall'
					)
				)
			),
			// Border
			array(
				'type' => 'separator',
				'meta' => array('html'=>'<hr />')
			),
			array(
				'id' => 'separator_border',
				'type' => 'separator',
				'meta' => array('html'=>'<h4>'.__('Border', 'themify').'</h4>'),
			),
			array(
				'id' => 'multi_border_top',
				'type' => 'multi',
				'label' => __('Border', 'themify'),
				'fields' => array(
					array(
						'id' => 'border_top_color',
						'type' => 'color',
						'class' => 'small'
					),
					array(
						'id' => 'border_top_width',
						'type' => 'text',
						'description' => 'px',
						'class' => 'xsmall'
					),
					array(
						'id' => 'border_top_style',
						'type' => 'select',
						'description' => __('top', 'themify'),
						'meta' => array(
							array( 'value' => '', 'name' => '' ),
							array( 'value' => 'solid', 'name' => __( 'Solid', 'themify' ) ),
							array( 'value' => 'dashed', 'name' => __( 'Dashed', 'themify' ) ),
							array( 'value' => 'dotted', 'name' => __( 'Dotted', 'themify' ) ),
							array( 'value' => 'double', 'name' => __( 'Double', 'themify' ) )
						)
					)
				)
			),
			array(
				'id' => 'multi_border_right',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'border_right_color',
						'type' => 'color',
						'class' => 'small'
					),
					array(
						'id' => 'border_right_width',
						'type' => 'text',
						'description' => 'px',
						'class' => 'xsmall'
					),
					array(
						'id' => 'border_right_style',
						'type' => 'select',
						'description' => __('right', 'themify'),
						'meta' => array(
							array( 'value' => '', 'name' => '' ),
							array( 'value' => 'solid', 'name' => __( 'Solid', 'themify' ) ),
							array( 'value' => 'dashed', 'name' => __( 'Dashed', 'themify' ) ),
							array( 'value' => 'dotted', 'name' => __( 'Dotted', 'themify' ) ),
							array( 'value' => 'double', 'name' => __( 'Double', 'themify' ) )
						)
					)
				)
			),
			array(
				'id' => 'multi_border_bottom',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'border_bottom_color',
						'type' => 'color',
						'class' => 'small'
					),
					array(
						'id' => 'border_bottom_width',
						'type' => 'text',
						'description' => 'px',
						'class' => 'xsmall'
					),
					array(
						'id' => 'border_bottom_style',
						'type' => 'select',
						'description' => __('bottom', 'themify'),
						'meta' => array(
							array( 'value' => '', 'name' => '' ),
							array( 'value' => 'solid', 'name' => __( 'Solid', 'themify' ) ),
							array( 'value' => 'dashed', 'name' => __( 'Dashed', 'themify' ) ),
							array( 'value' => 'dotted', 'name' => __( 'Dotted', 'themify' ) ),
							array( 'value' => 'double', 'name' => __( 'Double', 'themify' ) )
						)
					)
				)
			),
			array(
				'id' => 'multi_border_left',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'border_left_color',
						'type' => 'color',
						'class' => 'small'
					),
					array(
						'id' => 'border_left_width',
						'type' => 'text',
						'description' => 'px',
						'class' => 'xsmall'
					),
					array(
						'id' => 'border_left_style',
						'type' => 'select',
						'description' => __('left', 'themify'),
						'meta' => array(
							array( 'value' => '', 'name' => '' ),
							array( 'value' => 'solid', 'name' => __( 'Solid', 'themify' ) ),
							array( 'value' => 'dashed', 'name' => __( 'Dashed', 'themify' ) ),
							array( 'value' => 'dotted', 'name' => __( 'Dotted', 'themify' ) ),
							array( 'value' => 'double', 'name' => __( 'Double', 'themify' ) )
						)
					)
				)
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>')
			),
			array(
				'id' => 'css_accordion',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'description' => sprintf( '<br/><small>%s</small>', __( 'Add additional CSS class(es) for custom styling', 'themify') )
			)
		),
		'styling_selector' => array(
			' .ui.module-accordion' => array( 'font_size', 'line_height', 'font_family', 'color', 'margin' ),
			' .ui.module-accordion a' => array( 'link_color', 'text_decoration' ), 
			' .ui.module-accordion .accordion-title a' => array( 'background_color', 'padding' ),
			' .ui.module-accordion .accordion-content' => array( 'background_color', 'padding', 'border_top', 'border_right', 'border_bottom', 'border_left' )
		)
	) )
);