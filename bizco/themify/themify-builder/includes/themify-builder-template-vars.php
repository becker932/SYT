<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Template Variable
 */

$this->template_vars['rows'] = array(
	'content' => '<div class="themify_builder_row clearfix">
		
			<div class="themify_builder_row_top">
				<div class="row_menu">
					<div class="menu_icon">
					</div>
					<ul class="themify_builder_dropdown">
						<li><a href="#" class="themify_builder_duplicate_row">'.__('Duplicate', 'themify').'</a></li>
						<li><a href="#" class="themify_builder_delete_row">'.__('Delete', 'themify').'</a></li>
					</ul>
				</div>
				<!-- /row_menu -->
				<div class="toggle_row"></div><!-- /toggle_row -->
			</div>
			<!-- /row_top -->

			<div class="themify_builder_row_content">
				<div class="themify_builder_col col4-1 first col-one">
					<div class="themify_module_holder">
						<div class="empty_holder_text">'.__('drop module here', 'themify').'</div><!-- /empty module text -->
					</div>
					<!-- /module_holder -->
					<div class="col_dragger ui-resizable-handle ui-resizable-e" title="'.__('Drag left or right to change columns', 'themify').'"></div><!-- /col_dragger -->
				</div>
				<!-- /builder_col -->

				<div class="themify_builder_col col4-1 col-two">
					<div class="themify_module_holder">
						<div class="empty_holder_text">'.__('drop module here', 'themify').'</div><!-- /empty module text -->
					</div>
					<!-- /module_holder -->
					<div class="col_dragger ui-resizable-handle ui-resizable-e" title="'.__('Drag left or right to change columns', 'themify').'"></div><!-- /col_dragger -->
				</div>
				<!-- /builder_col -->

				<div class="themify_builder_col col4-1 col-three">
					<div class="themify_module_holder">
						<div class="empty_holder_text">'.__('drop module here', 'themify').'</div><!-- /empty module text -->
					</div>
					<!-- /module_holder -->
					<div class="col_dragger ui-resizable-handle ui-resizable-e" title="'.__('Drag left or right to change columns', 'themify').'"></div><!-- /col_dragger -->
				</div>
				<!-- /builder_col -->

				<div class="themify_builder_col col4-1 last col-four">
					<div class="themify_module_holder">
						<div class="empty_holder_text">'.__('drop module here', 'themify').'</div><!-- /empty module text -->
					</div>
					<!-- /module_holder -->
					<div class="col_dragger ui-resizable-handle ui-resizable-e" title="'.__('Drag left to add columns', 'themify').'"></div><!-- /col_dragger -->
				</div>
				<!-- /builder_col -->
			</div> <!-- /themify_builder_row_content -->
		</div>
		<!-- /builder_row -->'
);

$this->template_vars['column'] = array(
	'content' => '<div class="themify_builder_col">
					<div class="themify_module_holder">
						<div class="empty_holder_text">'.__('drop module here', 'themify').'</div><!-- /empty module text -->
					</div>
					<!-- /module_holder -->
					<div class="col_dragger"></div><!-- /col_dragger -->
				</div>
				<!-- /builder_col -->'
);

foreach( Themify_Builder_Model::$modules as $module ){
	$this->template_vars['module'][ $module->slug ] = array(
		'content' => '<div class="themify_builder_module '.$module->slug.' active_module" data-mod-name="'.$module->slug.'">
						<div class="module_menu">
							<div class="menu_icon">
							</div>
							<ul class="themify_builder_dropdown" style="display:none;">
								<li><a href="#" class="themify_module_options" data-module-name="'.$module->slug.'">'.__('Edit', 'themify').'</a></li>
								<li><a href="#" class="themify_module_duplicate">'.__('Duplicate', 'themify').'</a></li>
								<li><a href="#" class="themify_module_delete">'.__('Delete', 'themify').'</a></li>
							</ul>
						</div>
						<div class="module_label">
							<strong class="module_name">'. $module->name .'</strong>
							<em class="module_excerpt"></em>
						</div>
						<div class="themify_module_settings"></div>
					</div>
					<!-- /active_module -->'
	);
}

?>