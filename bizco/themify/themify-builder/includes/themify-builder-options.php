<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'themify_builder_module_settings_field' ) ) {
	/**
	 * Module Settings Fields
	 * @param array $module_options 
	 * @return string
	 */
	function themify_builder_module_settings_field( $module_options, $module_name ) {
		foreach ( $module_options as $field ):
			
			if( isset( $field['separated'] ) && $field['separated'] == 'top' ): ?>
				<hr />
			<?php endif; ?>

			<?php if( $field['type'] != 'builder' && ( !isset($field['hide']) || $field['hide'] == false) ): ?>
			<div class="themify_builder_field <?php echo (isset($field['wrap_with_class'])) ? $field['wrap_with_class'] : ''; ?>">
			<?php endif; ?>

				<?php if(isset($field['id']) && isset($field['label']) && $field['label'] != false): ?>
					<div class="themify_builder_label"><?php echo $field['label']; ?></div>
				<?php endif; ?>

			<?php 
				if('wp_editor' == $field['type']){
					wp_editor( '', $field['id'], array('editor_class' => $field['class'] . ' tfb_lb_wp_editor tfb_lb_option', 'textarea_rows' => 20));
				}
				
				elseif( 'builder' == $field['type'] ) { ?>

				<div class="<?php echo (isset($field['wrap_with_class'])) ? $field['wrap_with_class'] : ''; ?>">
				<hr />

				<div id="<?php echo $field['id']; ?>" class="themify_builder_module_opt_builder_wrap themify_builder_row_js_wrapper tfb_lb_option">
					
					<div class="themify_builder_row clearfix">
					
						<div class="themify_builder_row_top">
							<div class="row_menu">
								<div class="menu_icon">
								</div>
								<ul style="display: none;" class="themify_builder_dropdown">
									<li><a href="#" class="themify_builder_duplicate_row"><?php _e('Duplicate', 'themify') ?></a></li>
									<li><a href="#" class="themify_builder_delete_row"><?php _e('Delete', 'themify') ?></a></li>
								</ul>
							</div>
							<!-- /row_menu -->
							<div class="toggle_row"></div><!-- /toggle_row -->
						</div>
						<!-- /row_top -->
						
						<div class="themify_builder_row_content">

							<?php foreach( $field['options'] as $option ): ?>
							<div class="themify_builder_field <?php echo (isset($field['wrap_with_class'])) ? $field['wrap_with_class'] : ''; ?>">
								
								<?php if( isset($option['label']) && $option['label'] != false ): ?>
								<div class="themify_builder_label"><?php echo $option['label']; ?></div><!-- /themify_builder_input_title -->
								<?php endif; ?>
								
								<div class="themify_builder_input"<?php echo $option['type'] == 'wp_editor' ? ' style="width:100%;"' : ''; ?>>

									<?php if( $option['type'] == 'text' ): ?>
									<input name="<?php echo $option['id']; ?>" class="<?php echo $option['class']; ?> tfb_lb_option_child" type="text" data-input-id="<?php echo $option['id']; ?>" />
									
									<?php elseif( 'image' == $option['type'] ): ?>
									<input data-input-id="<?php echo $option['id']; ?>" name="<?php echo $option['id'] ?>" placeholder="<?php if(isset($option['value'])) echo $option['value']; ?>" class="<?php echo $option['class']; ?> themify-builder-uploader-input tfb_lb_option_child" type="text" /><br />
									
									<div class="small">

										<?php if ( is_multisite() && !is_upload_space_available() ): ?>
											<?php echo sprintf( __( 'Sorry, you have filled your %s MB storage quota so uploading has been disabled.', 'themify' ), get_space_allowed() ); ?>
										<?php else: ?>
										<div class="themify-builder-plupload-upload-uic hide-if-no-js tf-upload-btn" id="<?php echo $option['id']; ?>themify-builder-plupload-upload-ui">
												<input id="<?php echo $option['id']; ?>themify-builder-plupload-browse-button" type="button" value="<?php esc_attr_e(__('Upload', 'themify') ); ?>" class="builder_button" />
												<span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce($option['id'] . 'themify-builder-plupload'); ?>"></span>
										</div> <?php _e( 'or', 'themify' ); ?> <a href="#" class="themify-builder-media-uploader tf-upload-btn" data-uploader-title="<?php _e('Upload an Image', 'themify') ?>" data-uploader-button-text="<?php _e('Insert file URL', 'themify') ?>"><?php _e('Browse Library', 'themify') ?></a>

										<?php endif; ?>

									</div>
									
									<p class="thumb_preview">
										<span class="img-placeholder"></span>
										<a href="#" class="themify_builder_icon small delete themify-builder-delete-thumb"></a>
									</p>

									<?php elseif( $option['type'] == 'textarea' ): ?>
									<textarea name="<?php echo $option['id']; ?>" class="<?php echo $option['class']; ?> tfb_lb_option_child" <?php echo (isset($option['rows'])) ? 'rows="'.$option['rows'].'"' : ''; ?> data-input-id="<?php echo $option['id']; ?>"></textarea><br />
									
									<?php if( isset($option['radio']) ): ?>
									<div data-input-id="<?php echo $option['radio']['id']; ?>" class="tfb_lb_option_child tf-radio-choice">
									<?php echo $option['radio']['label']; ?> 
									<?php foreach( $option['radio']['options'] as $k => $v ): ?>
									<input id="<?php echo $option['radio']['id'] .'_'. $k; ?>" type="radio" name="<?php echo $option['radio']['id']; ?>" class="themify-builder-radio-dnd" value="<?php echo $k; ?>" /> 
									<label for="<?php echo $option['radio']['id'] .'_'. $k; ?>" class="pad-right themify-builder-radio-dnd-label"><?php echo $k; ?></label>
									<?php endforeach; ?>
									</div>
									<?php endif; // endif radio input ?>
									
									<?php
									elseif('wp_editor' == $option['type']):
										wp_editor( '', $option['id'], array('editor_class' => $option['class'] . ' tfb_lb_wp_editor tfb_lb_option_child', 'textarea_rows' => 20));
									?>

									<?php elseif( 'radio' == $option['type'] ): ?>
										<div data-input-id="<?php echo $option['id']; ?>" class="tfb_lb_option_child tf-radio-choice">
										<?php foreach( $option['options'] as $k => $v ): ?>
										<input id="<?php echo $option['id'] .'_'. $k; ?>" type="radio" name="<?php echo $option['id']; ?>" class="themify-builder-radio-dnd" value="<?php echo $k; ?>" /> 
										<label for="<?php echo $option['id'] .'_'. $k; ?>" class="pad-right themify-builder-radio-dnd-label"><?php echo $k; ?></label>
										<?php endforeach; ?>
										</div>
									<?php endif; // endif radio input ?>
									
									<?php if( isset($option['help']) ): ?>
										<?php if( isset($option['help']['new_line'])): ?>
										<br />
										<?php endif; ?>
										<small><?php echo $option['help']['text']; ?></small>
									<?php endif; ?>

								</div><!-- /themify_builder_input -->
							
							</div>
							<!-- /themify_builder_field -->
							
							<?php endforeach; ?>		

						</div>
						<!-- /themify_builder_row_content -->

					</div>
					<!-- /builder_row -->

				</div>
				<!-- /themify_builder_module_opt_builder_wrap -->
					
				<p class="add_new"><a href="#"><span class="themify_builder_icon add"></span><?php _e('Add new row', 'themify') ?></a></p>
				</div>
				<!-- /builder wrapper -->
				<?php
				}

				else{
			?>
				<div class="themify_builder_input<?php echo isset($field['pushed']) && $field['pushed'] != '' ? ' '.$field['pushed'] : ''; ?>">
					<?php if( 'text' == $field['type'] ): ?>
					<?php
						$add_class = ( isset($field['colorpicker']) && $field['colorpicker'] == true ) ? ' builderColorSelectInput' : '';
					?>
					<?php if( isset($field['colorpicker']) && $field['colorpicker'] == true ): ?>
					<span class="builderColorSelect"><span></span></span> 
					<?php endif; ?>
					<input id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" value="<?php if(isset($field['value'])) echo $field['value']; ?>" class="<?php echo $field['class'] . $add_class; ?> tfb_lb_option" type="text" />

					<?php if( isset($field['unit']) ): ?>
						<select id="<?php echo $field['unit']['id']; ?>" class="tfb_lb_option">
							<?php foreach($field['unit']['options'] as $u): ?>
							<option value="<?php echo $u['value']; ?>" <?php echo ($field['unit']['selected'] == $u['value']) ? 'selected="selected"':''; ?>><?php echo $u['value']; ?></option>
							<?php endforeach; ?>
						</select>
					<?php endif; // unit ?>

					<?php elseif( 'radio' == $field['type'] ): ?>
					<?php
					$option_js = (isset($field['option_js']) && $field['option_js'] == true) ? 'tf-option-checkbox-js' : '';
					$option_js_wrap = (isset($field['option_js']) && $field['option_js'] == true) ? 'tf-option-checkbox-enable' : '';
					?>
						<div id="<?php echo $field['id']; ?>" class="tfb_lb_option tf-radio-input-container <?php echo $option_js_wrap; ?>">
							<?php foreach($field['options'] as $k => $v): ?>
							<?php
								$default_checked = (isset($field['default']) && $field['default'] == $k) ? 'checked="checked"' : '';
								$data_el = (isset($field['option_js']) && $field['option_js'] == true) ? 'data-selected="tf-group-element-'.$k.'"' : '';
							?>
							<input id="<?php echo $field['id'].'_'.$k; ?>" name="<?php echo $field['id']; ?>" type="radio" class="<?php echo $option_js; ?>" value="<?php echo $k; ?>" <?php echo $default_checked .' '.$data_el; ?>/>
							<label for="<?php echo $field['id'].'_'.$k; ?>" class="pad-right"><?php echo $v; ?></label> 
							
							<?php if( isset($field['break']) && $field['break'] == true ): ?>
							<br />
							<?php endif; ?>

							<?php endforeach; ?>
						</div>

					<?php elseif( 'layout' == $field['type'] ): ?>
					<p id="<?php echo $field['id']; ?>" class="layout_icon tfb_lb_option themify-layout-icon">
						<?php foreach($field['options'] as $option): ?>
						<a href="#" id="<?php echo $option['value']; ?>" title="<?php echo $option['label']; ?>" class="tfl-icon">
							<img src="<?php echo THEMIFY_URI . '/themify-builder'; ?>/img/builder/<?php echo $option['img']?>" alt="<?php echo $option['label']; ?>" />
						</a>
						<?php endforeach; ?>
					</p>

					<?php elseif( 'image' == $field['type'] ): ?>
					<input id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" placeholder="<?php if(isset($field['value'])) echo $field['value']; ?>" class="<?php echo $field['class']; ?> themify-builder-uploader-input tfb_lb_option" type="text" /><br />
					
					<div class="small">

						<?php if ( is_multisite() && !is_upload_space_available() ): ?>
							<?php echo sprintf( __( 'Sorry, you have filled your %s MB storage quota so uploading has been disabled.', 'themify' ), get_space_allowed() ); ?>
						<?php else: ?>
						<div class="themify-builder-plupload-upload-uic hide-if-no-js tf-upload-btn" id="<?php echo $field['id']; ?>themify-builder-plupload-upload-ui">
								<input id="<?php echo $field['id']; ?>themify-builder-plupload-browse-button" type="button" value="<?php esc_attr_e(__('Upload', 'themify') ); ?>" class="builder_button" />
								<span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce($field['id'] . 'themify-builder-plupload'); ?>"></span>
						</div> <?php _e('or', 'themify') ?> <a href="#" class="themify-builder-media-uploader tf-upload-btn" data-uploader-title="<?php _e('Upload an Image', 'themify') ?>" data-uploader-button-text="<?php _e('Insert file URL', 'themify') ?>"><?php _e('Browse Library', 'themify') ?></a>

						<?php endif; ?>

					</div>
					
					<p class="thumb_preview">
						<span class="img-placeholder"></span>
						<a href="#" class="themify_builder_icon small delete themify-builder-delete-thumb"></a>
					</p>
					
					<?php elseif( 'checkbox' == $field['type'] ): ?>

						<div id="<?php echo $field['id']; ?>" class="tfb_lb_option themify-checkbox">
						<?php foreach( $field['options'] as $opt): ?>
							<?php
								$checkbox_checked = '';
								if( isset($field['default']) && is_array($field['default']) ) {
									$checkbox_checked = in_array($opt['name'], $field['default']) ? 'checked="checked"' : '';
								}
								elseif( isset($field['default']) ) {
									$checkbox_checked = checked( $field['default'], $opt['name'], false );
								}
							?>
							<input id="<?php echo $field['id'] . '_' . $opt['name']; ?>" name="<?php echo $field['id']; ?>[]" type="checkbox" class="tf-checkbox" value="<?php echo $opt['name']?>" <?php echo $checkbox_checked; ?> /> 
							<label for="<?php echo $field['id'] . '_' . $opt['name']; ?>" class="pad-right"><?php echo $opt['value']; ?></label>
							
							<?php if( isset($opt['help']) ): ?>
							<small><?php echo $opt['help']; ?></small>
							<?php endif; ?>
							
							<?php if( !isset($field['new_line']) || $field['new_line'] == true ): ?>
							<br />
							<?php endif; ?>

						<?php endforeach; ?>
						</div>

					<?php elseif( 'textarea' == $field['type'] ): ?>
					<textarea id="<?php echo $field['id']; ?>" name="<?php echo $field['id']; ?>" class="<?php echo $field['class']; ?> tfb_lb_option" row="3" type="text"></textarea>

					<?php elseif( 'select' == $field['type'] ): ?>
					
					<?php if( !isset($field['hide']) || $field['hide'] == false ): ?>
						<select id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" class="tfb_lb_option" >
							<?php if( isset($field['empty']) ): ?>
								<option value="<?php echo $field['empty']['val']; ?>"><?php echo $field['empty']['label']; ?></option>
							<?php endif; ?>
							
							<?php
							foreach ($field['options'] as $key => $value) {
								$selected = ( isset($field['default']) && $field['default'] == $value ) ? ' selected="selected"' : '';
								echo '<option value="' . $key . '" '.$selected.'>' . $value . '</option>';
							}
							?>
						</select>
					<?php endif; // isset hide ?>
					
					<?php if( isset($field['help']) ): ?>
					<br />
					<?php endif; // isset help ?>

					<?php elseif( 'selectbasic' == $field['type'] ): ?>
					<select id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" class="tfb_lb_option" >
						<?php
						foreach ($field['options'] as $value) {
							$selected = ( isset($field['default']) && $field['default'] == $value ) ? ' selected="selected"' : '';
							echo '<option value="' . $value . '" '.$selected.'>' . $value . '</option>';
						}
						?>
					</select>

					<?php elseif( 'select_menu' == $field['type'] ): ?>
					<select id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" class="tfb_lb_option select_menu_field" >
						<option value=""><?php _e('Select a Menu...', 'themify') ?></option>
						<?php
						foreach ($field['options'] as $key => $value) {
							$selected = ( isset($field['default']) && $field['default'] == $value ) ? ' selected="selected"' : '';
							echo '<option value="' . $value->slug . '" '.$selected.' data-termid="'. $value->term_id .'">' . $value->name . '</option>';
						}
						?>
					</select>

					<?php elseif( 'query_category' == $field['type'] ): ?>
					<?php
						$terms_tax = isset($field['options']['taxonomy'])? $field['options']['taxonomy']: 'category';			
						$terms_options = '';
						$terms_by_tax = get_terms($terms_tax);
						$terms_list = array();
						$terms_list['0'] = array(
							'title' => __('All Categories', 'themify'),										
							'slug'	=> '0'
						);
						foreach ($terms_by_tax as $term) {
							$terms_list[$term->term_id] = array(
								'title' => $term->name,
								'slug'	=> $term->slug
							);
						}
						foreach ($terms_list as $term_id => $term) {
							$term_selected = '';
							$terms_options .= sprintf(
								'<option value="%s" data-termid="%s" %s>%s</option>',
								$term['slug'],
								$term_id,
								$term_selected,
								$term['title']
							);
						}
						?>
						<select id="<?php echo $field['id'].'_dropdown'; ?>" class="query_category_single">
							<option></option>
							<?php echo $terms_options; ?>
						</select>
					 <?php _e('or', 'themify') ?>
					<input class="small query_category_multiple" type="text" /><br /><small><?php _e('multiple category IDs (eg. 2,5,8) or slug (eg. news,blog,featured)', 'themify'); ?></small><br />
					<input type="hidden" id="<?php echo $field['id']; ?>" name="<?php echo $field['id']; ?>" value="" class="tfb_lb_option themify-option-query-cat" />

					<?php
					///////////////////////////////////////////
					// Query category single field
					///////////////////////////////////////////
					elseif( 'query_category_single' == $field['type'] ): ?>
					<?php
						echo preg_replace('/>/', '><option></option>',
						wp_dropdown_categories(
						array(
							'taxonomy' => isset($field['options']['taxonomy'])?$field['options']['taxonomy']: 'category', 
							'class' => 'tfb_lb_option',
							'show_option_all' => __('All Categories', 'themify'),
							'hide_empty' => 0,
							'echo' => 0,
							'name' => $field['id'],
							'selected' => ''
						)), 1);
						echo '<br />';
					?>

					<?php 
						///////////////////////////////////////////
						// Multifield
						///////////////////////////////////////////
						elseif( 'multifield' == $field['type'] ): ?>

						<?php if( isset($field['options']['select']) ): ?>
						<select id="<?php echo $field['options']['select']['id']; ?>" class="tfb_lb_option">
							<?php foreach( $field['options']['select']['options'] as $opt ): ?>
							<option value="<?php echo $opt; ?>"><?php echo $opt; ?></option>
							<?php endforeach; ?>
						</select>
						<?php endif; ?>
						
						<?php if( isset($field['options']['text']) ): ?>
						<input id="<?php echo $field['options']['text']['id']; ?>" class="xsmall tfb_lb_option" type="text" /> 
							<?php if( isset($field['options']['text']['help']) ): ?>
							<small><?php echo $field['options']['text']['help']; ?></small>
							<?php endif; ?>
						<?php endif; ?>

						<?php if( isset($field['options']['colorpicker']) ): ?>
						<?php $color_class = isset($field['options']['colorpicker']['class']) ? $field['options']['colorpicker']['class'] : 'xsmall'; ?>
							<span class="builderColorSelect"><span></span></span> 
							<input id="<?php echo $field['options']['colorpicker']['id']; ?>" class="<?php echo $color_class; ?> tfb_lb_option builderColorSelectInput" type="text" /> 
						<?php endif; ?>

						<?php 
						///////////////////////////////////////////
						// Type Slider option
						///////////////////////////////////////////
						elseif( 'slider' == $field['type'] ):
						?>

						<?php foreach( $field['options'] as $fieldsec): ?>

						<?php if( $fieldsec['type'] == 'select' ): ?>
							<select id="<?php echo $fieldsec['id'] ?>" name="<?php echo $fieldsec['id'] ?>" class="tfb_lb_option" >
								<?php if( isset($fieldsec['empty']) ): ?>
									<option value="<?php echo $fieldsec['empty']['val']; ?>"><?php echo $fieldsec['empty']['label']; ?></option>
								<?php endif; ?>
								
								<?php
								foreach ($fieldsec['options'] as $key => $value) {
									$selected = ( isset($fieldsec['default']) && $fieldsec['default'] == $value ) ? ' selected="selected"' : '';
									echo '<option value="' . $key . '" '.$selected.'>' . $value . '</option>';
								}
								?>
							</select>

						<?php elseif( $fieldsec['type'] == 'text' ): ?>
							<input id="<?php echo $fieldsec['id'] ?>" name="<?php echo $fieldsec['id'] ?>" placeholder="<?php if(isset($fieldsec['value'])) echo $fieldsec['value']; ?>" class="<?php echo $fieldsec['class']; ?> tfb_lb_option" type="text" />
							<?php echo (isset($fieldsec['unit'])) ? '<small>'.$fieldsec['unit'].'</small>' : ''; ?>
						<?php endif; ?>
						<?php echo (isset($fieldsec['help'])) ? $fieldsec['help'] : ''; ?><br />
						<?php endforeach; ?>
					<?php endif; ?>

					<?php
					// hook actions
					do_action( 'themify_builder_lightbox_fields', $field, $module_name );
					?>
					
					<?php if( isset($field['break']) && $field['break'] == true ): ?>
						<br />
					<?php endif; ?>
					
					<?php if(isset($field['help'])): ?>
					<small><?php echo $field['help']; ?></small>
					<?php endif; ?>
				</div>
				<!-- /themify_builder_input -->
				<?php } ?>
			
			<?php if( $field['type'] != 'builder' && (!isset($field['hide']) || $field['hide'] == false) ): ?>
			</div>
			<!-- /themify_builder_field -->
			<?php endif; ?>
		
		<?php if( isset( $field['separated'] ) && $field['separated'] == 'bottom' ): ?>
			<hr />
		<?php endif; endforeach;
	}
}

if ( ! function_exists( 'themify_builder_styling_field' ) ) {
	/**
	 * Module Styling Fields
	 * @param array $styling 
	 * @return string
	 */
	function themify_builder_styling_field( $styling ){
		switch ($styling['type']) {
			
			case 'text':
				$html = '<input id="'.$styling['id'].'" name="'.$styling['id'].'" type="text" value="" class="'.$styling['class'].' tfb_lb_option">';
				$html .= isset( $styling['description'] ) ? $styling['description'] : '';
				echo $html;
			break;

			case 'separator':
				echo $html = isset($styling['meta']['html']) && '' != $styling['meta']['html']? $styling['meta']['html'] : '<hr class="meta_fields_separator" />';
			break;

			case 'image': ?>
				<input id="<?php echo $styling['id']; ?>" name="<?php echo $styling['id'] ?>" placeholder="<?php if(isset($styling['value'])) echo $styling['value']; ?>" class="<?php echo $styling['class']; ?> themify-builder-uploader-input tfb_lb_option" type="text" /><br />
								
				<div class="small">

					<?php if ( is_multisite() && !is_upload_space_available() ): ?>
						<?php echo sprintf( __( 'Sorry, you have filled your %s MB storage quota so uploading has been disabled.', 'themify' ), get_space_allowed() ); ?>
					<?php else: ?>
					<div class="themify-builder-plupload-upload-uic hide-if-no-js tf-upload-btn" id="<?php echo $styling['id']; ?>themify-builder-plupload-upload-ui">
							<input id="<?php echo $styling['id']; ?>themify-builder-plupload-browse-button" type="button" value="<?php esc_attr_e(__('Upload', 'themify') ); ?>" class="builder_button" />
							<span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce($styling['id'] . 'themify-builder-plupload'); ?>"></span>
					</div> <?php _e('or', 'themify') ?> <a href="#" class="themify-builder-media-uploader tf-upload-btn" data-uploader-title="<?php _e('Upload an Image', 'themify') ?>" data-uploader-button-text="<?php _e('Insert file URL', 'themify') ?>"><?php _e('Browse Library', 'themify') ?></a>

					<?php endif; ?>

				</div>
				
				<p class="thumb_preview">
					<span class="img-placeholder"></span>
					<a href="#" class="themify_builder_icon small delete themify-builder-delete-thumb"></a>
				</p>


				<?php
			break;

			case 'select': ?>
				
				<select id="<?php echo $styling['id'] ?>" name="<?php echo $styling['id']; ?>" class="tfb_lb_option <?php echo $styling['class']; ?>">
					<?php if( isset( $styling['default'] ) ): ?>
					<option value="<?php echo $styling['default']; ?>"><?php echo $styling['default']; ?></option>
					<?php endif;

					foreach( $styling['meta'] as $option ): ?>
					<option value="<?php echo $option['value']; ?>"><?php echo $option['name']; ?></option>
					<?php endforeach; ?>

				</select>

				<?php if ( isset( $styling['description'] ) ) {
					echo $styling['description'];
				} ?>

			<?php
			break;

			case 'font_select':
			$fonts = array_merge( themify_get_web_safe_font_list(), themify_get_google_web_fonts_list() );
			 ?>
				
				<select id="<?php echo $styling['id'] ?>" name="<?php echo $styling['id']; ?>" class="tfb_lb_option <?php echo $styling['class']; ?>">
					<?php if( isset( $styling['default'] ) ): ?>
					<option value="<?php echo $styling['default']; ?>"><?php echo $styling['default']; ?></option>
					<?php endif;

					foreach( $fonts as $option ): ?>
					<option value="<?php echo $option['value']; ?>"><?php echo $option['name']; ?></option>
					<?php endforeach; ?>

				</select>

				<?php if ( isset( $styling['description'] ) ) {
					echo $styling['description'];
				} ?>

			<?php
			break;

			case 'color': ?>
				<span class="builderColorSelect"><span></span></span> 
				<input id="<?php echo $styling['id'] ?>" name="<?php echo $styling['id'] ?>" value="" class="<?php echo $styling['class']; ?> builderColorSelectInput tfb_lb_option" type="text" />
			<?php
			break;

			case 'radio':
				foreach( $styling['meta'] as $option ) {
					$checked = isset( $option['selected'] ) && $option['selected'] == true ? 'checked="checked"' : ''; ?>
					<input type="radio" id="<?php echo $styling['id'] . '_' . $option['value']; ?>" name="<?php echo $styling['id']; ?>" value="<?php echo $option['value']; ?>" class="tfb_lb_option" <?php echo $checked; ?>> <label for="<?php echo $styling['id'] . '_' . $option['value']; ?>"><?php echo $option['name']; ?></label> 
				<?php
				}

			break;
		}
	}
}

// check for rights
if ( !is_user_logged_in() || !current_user_can('edit_posts') )
	wp_die(__('You are not allowed to be here', 'themify'));
	
	if ( $this->load_form == 'module' ): ?>
	
	<form id="tfb_module_settings">

	<div class="lightbox_inner">
		
		<ul class="themify_builder_options_tab clearfix">
			<li><a href="#themify_builder_options_setting"><?php echo ucfirst( $module->name ); ?></a></li>
			<?php if( isset( $module->styling ) ): ?>
			<li><a href="#themify_builder_options_styling"><?php _e('Styling', 'themify') ?></a></li>
			<?php endif; ?>
		</ul>

		<div id="themify_builder_options_setting" class="themify_builder_options_tab_content">
			<?php if( isset( $module->options ) ) {
				themify_builder_module_settings_field( $module->options, $module->slug );
			} ?>
		</div>
	
		<?php if ( isset( $module->styling ) ): ?>
		<div id="themify_builder_options_styling" class="themify_builder_options_tab_content">
			<?php
			foreach( $module->styling as $styling ): ?>
			<?php
				echo $styling['type'] != 'separator' ? '<div class="themify_builder_field">' : '';
				if ( isset( $styling['label'] ) ) {
					echo '<div class="themify_builder_label">'.$styling['label'].'</div>';
				}
				echo $styling['type'] != 'separator' ? '<div class="themify_builder_input">' : '';
				if ( $styling['type'] != 'multi' ) {
					themify_builder_styling_field( $styling );
				} else {
					foreach( $styling['fields'] as $field ) {
						themify_builder_styling_field( $field );
					}
				}
				echo $styling['type'] != 'separator' ? '</div>' : ''; // themify_builder_input
				echo $styling['type'] != 'separator' ? '</div>' : ''; // themify_builder_field
			?>

			<?php endforeach; ?>

			<p>
				<a href="#" class="reset-module-styling" data-reset="module">
					<span class="themify_builder_icon delete"></span>
					<?php _e('Reset Styling', 'themify') ?>
				</a>
			</p>
		</div>
		<!-- /themify_builder_options_tab_content -->
		<?php endif; ?>
					
	</div>
	<!-- /themify_builder_lightbox_inner -->

	<p class="themify_builder_save">
		<input class="builder_button" type="submit" name="submit" value="<?php _e('Save', 'themify') ?>" />
	</p>

	</form>

<?php elseif ( $this->load_form == 'row' ): ?>

<?php
$row_settings = array(
	// Rows Width
	array(
		'id' => 'row_width',
		'label' => __( 'Row Width', 'themify' ),
		'type' => 'radio',
		'meta' => array(
			array( 'value' => '', 'name' => __( 'Default', 'themify' ), 'selected' => true ),
			array( 'value' => 'fullwidth', 'name' => __( 'Fullwidth', 'themify' ) )
		)
	),
	// Animation
	array(
		'type' => 'separator',
		'meta' => array('html'=>'<hr />')
	),
	array(
		'id' => 'separator_animation',
		'title' => '',
		'description' => '',
		'type' => 'separator',
		'meta' => array('html'=>'<h4>'.__('Animation', 'themify').'</h4>'),
	),
	array(
		'id' => 'animation_effect',
		'type' => 'select',
		'label' => __( 'Effect', 'themify' ),
		'meta'	=> array(
			array('value' => '',   'name' => '', 'selected' => true),
			array('value' => 'fly-in',   'name' => __('Fly In', 'themify')),
			array('value' => 'fade-in', 'name' => __('Fade In', 'themify')),
			array('value' => 'slide-up',  'name' => __('Slide Up', 'themify'))
		)
	),
	// Background
	array(
		'type' => 'separator',
		'meta' => array('html'=>'<hr />')
	),
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
		'label'		=> __('Background Mode', 'themify'),
		'type' 		=> 'select',
		'default'	=> '',
		'meta'		=> array(
			array('value' => 'repeat', 'name' => __('Repeat All', 'themify')),
			array('value' => 'repeat-x', 'name' => __('Repeat Horizontally', 'themify')),
			array('value' => 'repeat-y', 'name' => __('Repeat Vertically', 'themify')),
			array('value' => 'repeat-none', 'name' => __('Do not repeat', 'themify')),
			array('value' => 'fullcover', 'name' => __('Fullcover', 'themify')),
			array('value' => 'builder-parallax-scrolling', 'name' => __('Parallax Scrolling', 'themify'))
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
		'id' => 'custom_css_row',
		'type' => 'text',
		'label' => __('Additional CSS Class', 'themify'),
		'class' => 'large exclude-from-reset-field',
		'description' => sprintf( '<br/><small>%s</small>', __( 'Add additional CSS class(es) for custom styling', 'themify') )
	)
);
?>

<form id="tfb_row_settings">
	<div class="lightbox_inner">
		<?php foreach( $row_settings as $styling ):

				echo $styling['type'] != 'separator' ? '<div class="themify_builder_field">' : '';
				if ( isset( $styling['label'] ) ) {
					echo '<div class="themify_builder_label">'.$styling['label'].'</div>';
				}
				echo $styling['type'] != 'separator' ? '<div class="themify_builder_input">' : '';
				if ( $styling['type'] != 'multi' ) {
					themify_builder_styling_field( $styling );
				} else {
					foreach( $styling['fields'] as $field ) {
						themify_builder_styling_field( $field );
					}
				}
				echo $styling['type'] != 'separator' ? '</div>' : ''; // themify_builder_input
				echo $styling['type'] != 'separator' ? '</div>' : ''; // themify_builder_field
			
			endforeach; ?>
	</div>
	<!-- /lightbox_inner -->

	<p>
		<a href="#" class="reset-module-styling" data-reset="row">
			<span class="themify_builder_icon delete"></span>
			<?php _e('Reset Styling', 'themify') ?>
		</a>
	</p>

	<p class="themify_builder_save">
		<input class="builder_button" type="submit" name="submit" value="<?php _e('Save', 'themify') ?>" />
	</p>	
</form>

<?php endif; ?>