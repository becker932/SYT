<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Callout
 * Description: Display Callout content
 */

class TB_Callout_Module extends Themify_Builder_Module {
	function __construct() {
		parent::__construct(array(
			'name' => __('Callout', 'themify'),
			'slug' => 'callout'
		));
	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_Callout_Module', 
	apply_filters( 'themify_builder_module_callout', array(
		'options' => array(
			array(
				'id' => 'mod_title_callout',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large'
			),
			array(
				'id' => 'layout_callout',
				'type' => 'layout',
				'label' => __('Callout Style', 'themify'),
				'options' => array(
					array('img' => 'callout-button-right.png', 'value' => 'button-right', 'label' => __('Button Right', 'themify')),
					array('img' => 'callout-button-left.png', 'value' => 'button-left', 'label' => __('Button Left', 'themify')),
					array('img' => 'callout-button-bottom.png', 'value' => 'button-bottom', 'label' => __('Button Bottom', 'themify')),
					array('img' => 'callout-button-bottom-center.png', 'value' => 'button-bottom-center', 'label' => __('Button Bottom Center', 'themify'))
				)
			),
			array(
				'id' => 'heading_callout',
				'type' => 'text',
				'label' => __('Callout Heading', 'themify'),
				'class' => 'xlarge'
			),
			array(
				'id' => 'text_callout',
				'type' => 'textarea',
				'label' => __('Callout Text', 'themify'),
				'class' => 'fullwidth'
			),
			array(
				'id' => 'color_callout',
				'type' => 'layout',
				'label' => __('Callout Color', 'themify'),
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
				'id' => 'appearance_callout',
				'type' => 'checkbox',
				'label' => __('Callout Appearance', 'themify'),
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
				'id' => 'action_btn_link_callout',
				'type' => 'text',
				'label' => __('Action Button Link', 'themify'),
				'class' => 'xlarge'
			),
			array(
				'id' => 'action_btn_text_callout',
				'type' => 'text',
				'label' => __('Action Button Text', 'themify'),
				'class' => 'medium'
				//'help' => __('If button text is empty = default text: More' ,'themify'),
				//'break' => true
			),
			array(
				'id' => 'action_btn_color_callout',
				'type' => 'layout',
				'label' => __('Action Button Color', 'themify'),
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
				'id' => 'action_btn_appearance_callout',
				'type' => 'checkbox',
				'label' => __('Action Button Appearance', 'themify'),
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
				'id' => 'background_image',
				'type' => 'image',
				'label' => __('Background Image', 'themify'),
				'class' => 'xlarge'
			),
			array(
				'id' => 'background_color',
				'type' => 'color',
				'label' => __('Background Color', 'themify'),
				'class' => 'small'
			),
			// Background repeat
			array(
				'id' 		=> 'background_repeat',
				'label'		=> __('Background Repeat', 'themify'),
				'type' 		=> 'select',
				'default'	=> '',
				'meta'		=> array(
					array('value' => 'repeat', 'name' => __('Repeat All', 'themify')),
					array('value' => 'repeat-x', 'name' => __('Repeat Horizontally', 'themify')),
					array('value' => 'repeat-y', 'name' => __('Repeat Vertically', 'themify')),
					array('value' => 'repeat-none', 'name' => __('Do not repeat', 'themify')),
					array('value' => 'fullcover', 'name' => __('Fullcover', 'themify'))
				)
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
				'id' => 'css_callout',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'description' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling', 'themify') )
			)
		),
		'styling_selector' => array(
			'.module-callout' => array( 
				'background_image', 'background_color', 'font_family', 'font_size', 'line_height', 'color', 'padding', 'margin', 'border_top', 'border_right', 'border_bottom', 'border_left'
			),
			'.module-callout a' => array( 'link_color', 'text_decoration' ),
			'.module-callout .callout-button' => array( 'font_family', 'color' )
		)
	) )
);