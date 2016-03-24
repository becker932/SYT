<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Builder Main Meta Box HTML
 */
?>

<div class="themify_builder themify_builder_admin clearfix">

	<div class="themify_builder_module_panel clearfix">
		
		<?php foreach( Themify_Builder_Model::$modules as $module ): ?>
		<?php $class = "themify_builder_module module-{$module->slug}"; ?>

		<div class="<?php echo esc_attr($class); ?>" data-module-name="<?php echo esc_attr( $module->slug ); ?>">
			<strong class="module_name"><?php echo esc_html( $module->name ); ?></strong>
			<a href="#" class="add_module" data-module-name="<?php echo esc_attr( $module->slug ); ?>"><?php _e('Add', 'themify') ?></a>
		</div>
		<!-- /module -->
		<?php endforeach; ?>
	</div>
	<!-- /themify_builder_module_panel -->

	<div class="themify_builder_row_panel clearfix">

		<div id="themify_builder_row_wrapper" class="themify_builder_row_js_wrapper">

		<?php if( ! empty( $builder_data ) && is_array( $builder_data ) ): ?>
		<!-- from database -->
		<?php foreach( $builder_data as $rows => $row ): ?>
		<div class="themify_builder_row clearfix">
			
			<div class="themify_builder_row_top">
				<div class="row_menu">
					<div class="menu_icon">
					</div>
					<ul class="themify_builder_dropdown">
						<li><a href="#" class="themify_builder_option_row"><?php _e('Options', 'themify') ?></a></li>
						<li><a href="#" class="themify_builder_duplicate_row"><?php _e('Duplicate', 'themify') ?></a></li>
						<li><a href="#" class="themify_builder_delete_row"><?php _e('Delete', 'themify') ?></a></li>
					</ul>
				</div>
				<!-- /row_menu -->
				<div class="toggle_row"></div><!-- /toggle_row -->
			</div>
			<!-- /row_top -->

			<div class="themify_builder_row_content">
				
				<?php if ( isset( $row['cols'] ) ) : ?>
					<?php foreach ( $row['cols'] as $cols => $col ): ?>
					<div class="themify_builder_col <?php echo $col['grid_class']; ?>">
						<div class="themify_module_holder">
							<div class="empty_holder_text"><?php _e('drop module here', 'themify') ?></div><!-- /empty module text -->

							<?php if ( isset( $col['modules'] ) && count( $col['modules'] ) > 0 ): ?>
							<?php foreach ( $col['modules'] as $mod ): ?>
							<div class="themify_builder_module module-<?php echo $mod['mod_name']; ?> active_module" data-mod-name="<?php echo esc_attr( $mod['mod_name'] ); ?>">
								<div class="module_menu">
									<div class="menu_icon">
									</div>
									<ul class="themify_builder_dropdown">
										<li><a href="#" class="themify_module_options" data-module-name="<?php echo esc_attr( $mod['mod_name'] ); ?>"><?php _e('Edit', 'themify') ?></a></li>
										<li><a href="#" class="themify_module_duplicate"><?php _e('Duplicate', 'themify') ?></a></li>
										<li><a href="#" class="themify_module_delete"><?php _e('Delete', 'themify') ?></a></li>
									</ul>
								</div>
								<div class="module_label">
									<strong class="module_name"><?php echo Themify_Builder_Model::get_module_name( $mod['mod_name'] ); ?></strong>
									<em class="module_excerpt"><?php echo $this->get_module_excerpt($mod); ?></em>
								</div>
								<div class="themify_module_settings">
									<?php
									$mod_settings = $mod['mod_settings'];
									?>
									<?php echo esc_attr( json_encode( $mod_settings ) ); ?>
								</div>
							</div>
							<!-- /active_module -->
							<?php endforeach; // end modules loop ?>
							<?php endif; // end modules count check ?>

						</div>
						<!-- /module_holder -->
						<div class="col_dragger ui-resizable-handle ui-resizable-e" title="<?php _e('Drag left/right to change columns', 'themify') ?>"></div><!-- /col_dragger -->
					</div>
					<!-- /builder_col -->
					<?php endforeach; // end col loop ?>
				<?php endif; // isset row cols ?>

			</div> <!-- /themify_builder_row_content -->
			<?php $row_data_styling = isset( $row['styling'] ) && is_array( $row['styling'] ) ? json_encode( $row['styling'] ) : json_encode( array() ); ?>
			<div class="row-data-styling" data-styling="<?php echo esc_attr( $row_data_styling ); ?>"></div>
		</div>
		<!-- /builder_row -->
		<?php endforeach; // end rows loop ?>

		<!-- /from database -->
		<?php else: ?>

		<div class="themify_builder_row clearfix">
			
			<div class="themify_builder_row_top">
				<div class="row_menu">
					<div class="menu_icon">
					</div>
					<ul class="themify_builder_dropdown">
						<li><a href="#" class="themify_builder_option_row"><?php _e('Options', 'themify') ?></a></li>
						<li><a href="#" class="themify_builder_duplicate_row"><?php _e('Duplicate', 'themify') ?></a></li>
						<li><a href="#" class="themify_builder_delete_row"><?php _e('Delete', 'themify') ?></a></li>
					</ul>
				</div>
				<!-- /row_menu -->
				<div class="toggle_row"></div><!-- /toggle_row -->
			</div>
			<!-- /row_top -->

			<div class="themify_builder_row_content">
				<div class="themify_builder_col col4-1 first">
					<div class="themify_module_holder">
						<div class="empty_holder_text"><?php _e('drop module here', 'themify') ?></div><!-- /empty module text -->
					</div>
					<!-- /module_holder -->
					<div class="col_dragger ui-resizable-handle ui-resizable-e" title="<?php _e('Drag left/right to change columns', 'themify') ?>"></div><!-- /col_dragger -->
				</div>
				<!-- /builder_col -->

				<div class="themify_builder_col col4-1">
					<div class="themify_module_holder">
						<div class="empty_holder_text"><?php _e('drop module here', 'themify') ?></div><!-- /empty module text -->
					</div>
					<!-- /module_holder -->
					<div class="col_dragger ui-resizable-handle ui-resizable-e"></div><!-- /col_dragger -->
				</div>
				<!-- /builder_col -->

				<div class="themify_builder_col col4-1">
					<div class="themify_module_holder">
						<div class="empty_holder_text"><?php _e('drop module here', 'themify') ?></div><!-- /empty module text -->
					</div>
					<!-- /module_holder -->
					<div class="col_dragger ui-resizable-handle ui-resizable-e" title="<?php _e('Drag left/right to change columns', 'themify') ?>"></div><!-- /col_dragger -->
				</div>
				<!-- /builder_col -->

				<div class="themify_builder_col col4-1 last">
					<div class="themify_module_holder">
						<div class="empty_holder_text"><?php _e('drop module here', 'themify') ?></div><!-- /empty module text -->
					</div>
					<!-- /module_holder -->
					<div class="col_dragger ui-resizable-handle ui-resizable-e" title="<?php _e('Drag left to add columns', 'themify') ?>"></div><!-- /col_dragger -->
				</div>
				<!-- /builder_col -->
			</div> <!-- /themify_builder_row_content -->

			<div class="row-data-styling" data-styling=""></div>
		</div>
		<!-- /builder_row -->
		<?php endif; // end count data rows ?>

		<?php themify_builder_col_detection(); ?>

		</div> <!-- /#themify_builder_row_wrapper -->

		<p class="themify_builder_save">
			<?php if ( $pagenow !== 'post-new.php' ): ?>
			<a href="#" id="themify_builder_duplicate" class="themify-builder-duplicate-btn builder_button left"><?php _e('Duplicate this page', 'themify') ?></a>
			<a href="#" id="themify_builder_switch_frontend" class="themify_builder_switch_frontend"><?php _e('Switch to frontend', 'themify') ?></a>
			<?php endif; ?>
			<a href="#" id="themify_builder_main_save" class="builder_button"><?php _e('Save', 'themify') ?></a>
		</p>

	</div>
	<!-- /themify_builder_row_panel -->

	<div style="display: none;">
		<?php
			wp_editor( ' ', 'tfb_lb_hidden_editor' );
		?>
	</div>

</div>
<!-- /themify_builder -->
