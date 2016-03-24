<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Widget
 * Description: Display any available widgets
 */
class TB_Widget_Module extends Themify_Builder_Module {
	function __construct() {
		parent::__construct(array(
			'name' => __('Widget', 'themify'),
			'slug' => 'widget'
		));

		add_action( 'themify_builder_lightbox_fields', array( $this, 'widget_fields' ), 10, 2 );
		add_action( 'wp_ajax_module_widget_get_form', array( $this, 'widget_get_form' ), 10 );
	}

	function widget_fields( $field, $mod_name ) {
		global $wp_widget_factory;
		$output = '';

		if ( $mod_name != 'widget' ) return;

		switch ( $field['type'] ) {
			case 'widget_select':
				$output .= '<select name="'.$field['id'].'" id="'.$field['id'].'" class="tfb_lb_option module-widget-select-field">';
				$output .= '<option></option>';
				foreach ($wp_widget_factory->widgets as $class => $widget ) {
					$output .= '<option value="'.$class.'" data-idbase="'.$widget->id_base.'">'.$widget->name.'</option>';
				}
				$output .= '</select>';
			break;
			
			case 'widget_form':
			$output .= '<div id="'.$field['id'].'" class="module-widget-form-container module-widget-form-placeholder tfb_lb_option"></div>';
			break;	
		}
		echo $output;
	}

	function widget_get_form() {
		if ( ! wp_verify_nonce( $_POST['tfb_load_nonce'], 'tfb_load_nonce' ) ) die(-1);
		
		global $wp_widget_factory;
		require_once ABSPATH . 'wp-admin/includes/widgets.php';

		$class = $_POST['load_class'];
		if ( $class == '') die(-1);

		$get_instance = isset( $_POST['widget_instance'] ) ? $_POST['widget_instance'] : '';
		$instance = array();
		if ( is_array( $get_instance ) && count( $get_instance ) > 0 ) {
			foreach ( $get_instance as $k => $s ) {
				$instance = $s;
			}
		}

		$widget = new $class();
		$widget->number = next_widget_id_number( $_POST['id_base'] );

		ob_start();
		$widget->form($instance);
		$form = ob_get_clean();

		$widget->form = $form;

		echo $widget->form;
		echo '<br/>';
		die();
	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_Widget_Module',
	apply_filters( 'themify_builder_module_widget', array(
		'options' => array(
			array(
				'id' => 'mod_title_widget',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large'
			),
			array(
				'id' => 'class_widget',
				'type' => 'widget_select',
				'label' => __('Select Widget', 'themify'),
				'class' => 'large',
				'help' => __('Select Available Widgets', 'themify'),
				'separated' => 'bottom',
				'break' => true
			),
			array(
				'id' => 'instance_widget',
				'type' => 'widget_form',
				'label' => false
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
				'type' => 'select',
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
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>')
			),
			array(
				'id' => 'custom_css_widget',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'description' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling', 'themify') ),
				'class' => 'large exclude-from-reset-field'
			)
		),
		'styling_selector' => array(
			'.module-widget' => array(
				'background_image', 'background_color', 'padding', 'margin'
			),
			'.module-widget a' => array( 'link_color', 'text_decoration' ),
			'.module-widget' => array(
				'font_family', 'font_size', 'line_height', 'color'
			),
			'.module-widget a' => array(
				'font_family', 'font_size', 'line_height', 'color'
			)
		)
	) )
);