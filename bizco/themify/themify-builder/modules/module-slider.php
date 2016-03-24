<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Slider
 * Description: Display slider content
 */
class TB_Slider_Module extends Themify_Builder_Module {
	function __construct() {
		parent::__construct(array(
			'name' => __('Slider', 'themify'),
			'slug' => 'slider'
		));

		///////////////////////////////////////
		// Load Post Type
		///////////////////////////////////////
		$this->meta_box = $this->set_metabox();
		$this->initialize_cpt( array(
			'plural' => __('Slides', 'themify'),
			'singular' => __('Slide', 'themify'),
			'supports' => array('title', 'editor', 'author', 'custom-fields'),
			'menu_icon' => 'dashicons-slides'
		));

		if ( ! shortcode_exists( 'themify_'. $this->slug .'_posts' ) ) {
			add_shortcode( 'themify_'.$this->slug.'_posts', array( $this, 'do_shortcode' ) );
		}
	}

	function set_metabox() {
		global $ThemifyBuilder;

		/** Slider Meta Box Options */
		$meta_box = array(
			// Feature Image
			Themify_Builder_Model::$post_image,
			// Featured Image Size
			Themify_Builder_Model::$featured_image_size,
			// Image Width
			Themify_Builder_Model::$image_width,
			// Image Height
			Themify_Builder_Model::$image_height,
			// External Link
			Themify_Builder_Model::$external_link,
			// Lightbox Link
			Themify_Builder_Model::$lightbox_link,
			array(
				'name' 		=> 'video_url',
				'title' 	=> __('Video URL', 'themify'),
				'description' => __('URL to embed a video instead of featured image', 'themify'),
				'type' 		=> 'textbox',
				'meta'		=> array()
			)
		);
		return $meta_box;
	}

	function do_shortcode( $atts ) {
		global $ThemifyBuilder;

		extract( shortcode_atts( array(
			'visible' => '1',
			'scroll' => '1',
			'auto' => 0,
			'pause_hover' => 'no',
			'wrap' => 'yes',
			'excerpt_length' => '20',
			'speed' => 'normal',
			'slider_nav' => 'yes',
			'pager' => 'yes',
			'limit' => 5,
			'category' => 0,
			'image' => 'yes',
			'image_w' => '240px',
			'image_h' => '180px',
			'more_text' => __('More...', 'themify'),
			'title' => 'yes',
			'display' => 'none',
			'post_meta' => 'no',
			'post_date' => 'no',
			'width' => '',
			'height' => '',
			'class' => '',
			'unlink_title' => 'no',
			'unlink_image' => 'no',
			'image_size' => 'thumbnail',
			'post_type' => 'post',
			'taxonomy' => 'category',
			'order' => 'DESC',
			'orderby' => 'date',
			'effect' => 'scroll',
			'style' => 'slider-default'
		), $atts ) );

		$sync = array(
			'mod_title_slider' => '',
			'layout_display_slider' => 'slider',
			'slider_category_slider' => $category,
			'posts_per_page_slider' => $limit,
			'offset_slider' => '',
			'display_slider' => $display,
			'hide_post_title_slider' => $title == 'yes' ? 'no' : 'yes',
			'unlink_post_title_slider' => $unlink_title,
			'hide_feat_img_slider' => '',
			'unlink_feat_img_slider' => $unlink_image,
			'layout_slider' => $style,
			'image_size_slider' => $image_size,
			'img_w_slider' => $image_w,
			'img_h_slider' => $image_h,
			'visible_opt_slider' => $visible,
			'auto_scroll_opt_slider' => $auto,
			'scroll_opt_slider' => $scroll,
			'speed_opt_slider' => $speed,
			'effect_slider' => $effect,
			'pause_on_hover_slider' => $pause_hover,
			'wrap_slider' => $wrap,
			'show_nav_slider' => $pager,
			'show_arrow_slider' => $slider_nav,
			'left_margin_slider' => '',
			'right_margin_slider' => '',
			'css_slider' => $class
		);
		$module = array(
			'module_ID' => $this->slug . '-' . rand(0,10000),
			'mod_name' => $this->slug,
			'settings' => $sync
		);

		return $ThemifyBuilder->retrieve_template( 'template-' . $this->slug .'-' . $this->slug . '.php', $module, '', '', false );
	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
$visible_opt = array(1 => 1, 2, 3, 4, 5, 6, 7);
$auto_scroll_opt = array(
	'off' => __( 'Off', 'themify' ),
	1 => __( '1 sec', 'themify' ),
	2 => __( '2 sec', 'themify' ),
	3 => __( '3 sec', 'themify' ),
	4 => __( '4 sec', 'themify' ),
	5 => __( '5 sec', 'themify' ),
	6 => __( '6 sec', 'themify' ),
	7 => __( '7 sec', 'themify' ),
	8 => __( '8 sec', 'themify' ),
	9 => __( '9 sec', 'themify' ),
	10 => __( '10 sec', 'themify' )
);
$image_sizes = themify_get_image_sizes_list( false );
Themify_Builder_Model::register_module( 'TB_Slider_Module',
	apply_filters( 'themify_builder_module_slider', array(
		'options' => array(
			array(
				'id' => 'mod_title_slider',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large'
			),
			array(
				'id' => 'layout_display_slider',
				'type' => 'radio',
				'label' => __('Display', 'themify'),
				'options' => array(
					'blog' => __('Blog Posts', 'themify'),
					'slider' => __('Slider Posts', 'themify'),
					'portfolio' => __('Portfolio', 'themify'),
					'testimonial' => __('Testimonial', 'themify'),
					'image' => __('Images', 'themify'),
					'video' => __('Videos', 'themify'),
					'text' => __('Text', 'themify')
				),
				'default' => 'blog',
				'option_js' => true
			),
			///////////////////////////////////////////
			// Blog post option
			///////////////////////////////////////////
			array(
				'id' => 'blog_category_slider',
				'type' => 'query_category',
				'label' => __('Category', 'themify'),
				'options' => array(),
				'help' => sprintf(__('Add more <a href="%s" target="_blank">blog posts</a>', 'themify'), admin_url('post-new.php')),
				'wrap_with_class' => 'tf-group-element tf-group-element-blog'
			),
			array(
				'id' => 'slider_category_slider',
				'type' => 'query_category',
				'label' => __('Category', 'themify'),
				'options' => array(
					'taxonomy' => 'slider-category'
				),
				'help' => sprintf(__('Add more <a href="%s" target="_blank">slider posts</a>', 'themify'), admin_url('post-new.php?post_type=slider')),
				'wrap_with_class' => 'tf-group-element tf-group-element-slider'
			),
			array(
				'id' => 'portfolio_category_slider',
				'type' => 'query_category',
				'label' => __('Category', 'themify'),
				'options' => array(
					'taxonomy' => 'portfolio-category'
				),
				'help' => sprintf(__('Add more <a href="%s" target="_blank">portfolio posts</a>', 'themify'), admin_url('post-new.php?post_type=portfolio')),
				'wrap_with_class' => 'tf-group-element tf-group-element-portfolio'
			),
			array(
				'id' => 'testimonial_category_slider',
				'type' => 'query_category',
				'label' => __('Category', 'themify'),
				'options' => array(
					'taxonomy' => 'testimonial-category'
				),
				'help' => sprintf(__('Add more <a href="%s" target="_blank">testimonial posts</a>', 'themify'), admin_url('post-new.php?post_type=testimonial')),
				'wrap_with_class' => 'tf-group-element tf-group-element-testimonial'
			),
			array(
				'id' => 'posts_per_page_slider',
				'type' => 'text',
				'label' => __('Query', 'themify'),
				'class' => 'xsmall',
				'help' => __('number of posts to query', 'themify'),
				'wrap_with_class' => 'tf-group-element tf-group-element-blog tf-group-element-portfolio tf-group-element-slider tf-group-element-testimonial'
			),
			array(
				'id' => 'offset_slider',
				'type' => 'text',
				'label' => __('Offset', 'themify'),
				'class' => 'xsmall',
				'help' => __('number of post to displace or pass over', 'themify'),
				'wrap_with_class' => 'tf-group-element tf-group-element-blog tf-group-element-portfolio tf-group-element-slider tf-group-element-testimonial'
			),
			array(
				'id' => 'display_slider',
				'type' => 'select',
				'label' => __('Display', 'themify'),
				'options' => array(
					'content' => __('Content', 'themify'),
					'excerpt' => __('Excerpt', 'themify'),
					'none' => __('None', 'themify')
				),
				'wrap_with_class' => 'tf-group-element tf-group-element-blog tf-group-element-slider tf-group-element-portfolio tf-group-element-testimonial'
			),
			array(
				'id' => 'hide_post_title_slider',
				'type' => 'select',
				'label' => __('Hide Post Title', 'themify'),
				'empty' => array(
					'val' => '',
					'label' => ''
				),
				'options' => array(
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				),
				'wrap_with_class' => 'tf-group-element tf-group-element-blog tf-group-element-slider tf-group-element-portfolio tf-group-element-testimonial'
			),
			array(
				'id' => 'unlink_post_title_slider',
				'type' => 'select',
				'label' => __('Unlink Post Title', 'themify'),
				'empty' => array(
					'val' => '',
					'label' => ''
				),
				'options' => array(
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				),
				'wrap_with_class' => 'tf-group-element tf-group-element-blog tf-group-element-slider tf-group-element-portfolio'
			),
			array(
				'id' => 'hide_feat_img_slider',
				'type' => 'select',
				'label' => __('Hide Featured Image', 'themify'),
				'empty' => array(
					'val' => '',
					'label' => ''
				),
				'options' => array(
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				),
				'wrap_with_class' => 'tf-group-element tf-group-element-blog tf-group-element-slider tf-group-element-portfolio tf-group-element-testimonial'
			),
			array(
				'id' => 'unlink_feat_img_slider',
				'type' => 'select',
				'label' => __('Unlink Featured Image', 'themify'),
				'empty' => array(
					'val' => '',
					'label' => ''
				),
				'options' => array(
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				),
				'wrap_with_class' => 'tf-group-element tf-group-element-blog tf-group-element-slider tf-group-element-portfolio'
			),

			///////////////////////////////////////////
			// Image post option
			///////////////////////////////////////////
			array(
				'id' => 'img_content_slider',
				'type' => 'builder',
				'options' => array(
					array(
						'id' => 'img_url_slider',
						'type' => 'image',
						'label' => __('Image URL', 'themify'),
						'class' => 'xlarge'
					),
					array(
						'id' => 'img_title_slider',
						'type' => 'text',
						'label' => __('Image Title', 'themify'),
						'class' => 'fullwidth'
					),
					array(
						'id' => 'img_link_slider',
						'type' => 'text',
						'label' => __('Image Link', 'themify'),
						'class' => 'fullwidth'
					),
					array(
						'id' => 'img_caption_slider',
						'type' => 'textarea',
						'label' => __('Image Caption', 'themify'),
						'class' => 'fullwidth',
						'rows' => 6
					)
				),
				'wrap_with_class' => 'tf-group-element tf-group-element-image'
			),

			///////////////////////////////////////////
			// Video post option
			///////////////////////////////////////////
			array(
				'id' => 'video_content_slider',
				'type' => 'builder',
				'options' => array(
					array(
						'id' => 'video_url_slider',
						'type' => 'text',
						'label' => __('Video URL', 'themify'),
						'class' => 'xlarge',
						'help' => array(
							'new_line' => true,
							'text' => __('YouTube, Vimeo, etc', 'themify')
						)
					),
					array(
						'id' => 'video_title_slider',
						'type' => 'text',
						'label' => __('Video Title', 'themify'),
						'class' => 'fullwidth'
					),
					array(
						'id' => 'video_title_link_slider',
						'type' => 'text',
						'label' => __('Video Title Link', 'themify'),
						'class' => 'fullwidth'
					),
					array(
						'id' => 'video_caption_slider',
						'type' => 'textarea',
						'label' => __('Video Caption', 'themify'),
						'class' => 'fullwidth',
						'rows' => 6
					),
					array(
						'id' => 'video_width_slider',
						'type' => 'text',
						'label' => __('Video Width', 'themify'),
						'class' => 'xsmall'
					)
				),
				'wrap_with_class' => 'tf-group-element tf-group-element-video'
			),

			///////////////////////////////////////////
			// Text Slider option
			///////////////////////////////////////////
			array(
				'id' => 'text_content_slider',
				'type' => 'builder',
				'options' => array(
					array(
						'id' => 'text_caption_slider',
						'type' => 'wp_editor',
						'label' => false,
						'class' => 'fullwidth builder-field',
						'rows' => 6
					)
				),
				'wrap_with_class' => 'tf-group-element tf-group-element-text'
			),

			array(
				'id' => 'layout_slider',
				'type' => 'layout',
				'label' => __('Slider Layout', 'themify'),
				'separated' => 'top',
				'options' => array(
					array('img' => 'slider-default.png', 'value' => 'slider-default', 'label' => __('Slider Default', 'themify')),
					array('img' => 'slider-image-top.png', 'value' => 'slider-overlay', 'label' => __('Slider Overlay', 'themify')),
					array('img' => 'slider-caption-overlay.png', 'value' => 'slider-caption-overlay', 'label' => __('Slider Caption Overlay', 'themify'))
				)
			),
			array(
				'id' => 'image_size_slider',
				'type' => 'select',
				'label' => $this->is_img_php_disabled() ? __('Image Size', 'themify') : false,
				'empty' => array(
					'val' => '',
					'label' => ''
				),
				'hide' => $this->is_img_php_disabled() ? false : true,
				'options' => $image_sizes,
				'wrap_with_class' => 'tf-group-element tf-group-element-blog tf-group-element-slider tf-group-element-portfolio tf-group-element-image'
			),
			array(
				'id' => 'img_w_slider',
				'type' => 'text',
				'label' => __('Image Width', 'themify'),
				'class' => 'xsmall',
				'help' => 'px',
				'wrap_with_class' => 'tf-group-element tf-group-element-blog tf-group-element-slider tf-group-element-portfolio tf-group-element-image'
			),
			array(
				'id' => 'img_h_slider',
				'type' => 'text',
				'label' => __('Image Height', 'themify'),
				'class' => 'xsmall',
				'help' => 'px',
				'wrap_with_class' => 'tf-group-element tf-group-element-blog tf-group-element-slider tf-group-element-portfolio tf-group-element-image'
			),

			array(
				'id' => 'slider_option_slider',
				'type' => 'slider',
				'label' => __('Slider Options', 'themify'),
				'options' => array(
					array(
						'id' => 'visible_opt_slider',
						'type' => 'select',
						'default' => 1,
						'options' => $visible_opt,
						'help' => __('Visible', 'themify')
					),
					array(
						'id' => 'auto_scroll_opt_slider',
						'type' => 'select',
						'default' => 4,
						'options' => $auto_scroll_opt,
						'help' => __('Auto Scroll', 'themify')
					),
					array(
						'id' => 'scroll_opt_slider',
						'type' => 'select',
						'options' => $visible_opt,
						'help' => __('Scroll', 'themify')
					),
					array(
						'id' => 'speed_opt_slider',
						'type' => 'select',
						'options' => array(
							'normal' => __('Normal', 'themify'),
							'fast' => __('Fast', 'themify'),
							'slow' => __('Slow', 'themify')
						),
						'help' => __('Speed', 'themify')
					),
					array(
						'id' => 'effect_slider',
						'type' => 'select',
						'options' => array(
							'scroll' => __('Slide', 'themify'),
							'fade' => __('Fade', 'themify'),
							'continuously' => __('Continuously', 'themify')
						),
						'help' => __('Effect', 'themify')
					),
					array(
						'id' => 'pause_on_hover_slider',
						'type' => 'select',
						'options' => array(
							'resume' => __('Resume', 'themify'),
							'immediate' => __('Immediate', 'themify'),
							'false' => __('Disable', 'themify')
						),
						'help' => __('Pause On Hover', 'themify')
					),
					array(
						'id' => 'wrap_slider',
						'type' => 'select',
						'help' => __('Wrap', 'themify'),
						'options' => array(
							'yes' => __('Yes', 'themify'),
							'no' => __('No', 'themify')
						)
					),
					array(
						'id' => 'show_nav_slider',
						'type' => 'select',
						'help' => __('Show slider pagination', 'themify'),
						'options' => array(
							'yes' => __('Yes', 'themify'),
							'no' => __('No', 'themify')
						)
					),
					array(
						'id' => 'show_arrow_slider',
						'type' => 'select',
						'help' => __('Show slider arrow buttons', 'themify'),
						'options' => array(
							'yes' => __('Yes', 'themify'),
							'no' => __('No', 'themify')
						)
					),
					array(
						'id' => 'left_margin_slider',
						'type' => 'text',
						'class' => 'xsmall',
						'unit' => 'px',
						'help' => __('Left margin space between slides', 'themify')
					),
					array(
						'id' => 'right_margin_slider',
						'type' => 'text',
						'class' => 'xsmall',
						'unit' => 'px',
						'help' => __('Right margin space between slides', 'themify')
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
				'id' => 'css_slider',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'description' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling', 'themify') )
			)
		),
		'styling_selector' => array(
			'.module-slider' => array(
				'background_color', 'padding', 'margin', 'border_top', 'border_right', 'border_bottom', 'border_left'
			),
			'.module-slider .slide-content' => array(
				 'font_family', 'font_size', 'line_height', 'color'
			),
			'.module-slider a' => array( 'link_color', 'text_decoration' ),
			'.module-slider .slide-content .slide-title' => array(
				 'font_family', 'color'
			),
			'.module-slider .slide-content .slide-title a' => array(
				 'font_family', 'color'
			)
		)
	) )
);