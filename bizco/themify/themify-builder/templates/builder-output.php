<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div id="themify_builder_content-<?php echo $builder_id; ?>" data-postid="<?php echo $builder_id; ?>" class="themify_builder_content themify_builder_content-<?php echo $builder_id; ?> themify_builder themify_builder_front">
	<?php foreach ( $builder_output as $rows => $row ): ?>
	<!-- module_row -->
	<?php
	$row['row_order'] = isset( $row['row_order'] ) ? $row['row_order'] : '1';
	$row_classes = array( 'themify_builder_row', 'module_row', 'module_row_' . $row['row_order'], 'clearfix' );
	$class_fields = array( 'custom_css_row', 'background_repeat', 'animation_effect', 'row_width' );
	foreach( $class_fields as $field ) {
		if ( isset( $row['styling'][ $field ] ) && ! empty( $row['styling'][ $field ] ) ) array_push( $row_classes, $row['styling'][ $field ] );
	}
	?>
	<div class="<?php echo implode(' ', $row_classes ); ?>">

		<div class="row_inner">
		
			<?php do_action('themify_builder_row_start', $builder_id, $row ); ?>

			<?php if ( $this->frontedit_active ): ?>
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
			<?php endif; // builder edit active ?>

			<?php if ( isset( $row['cols'] ) && count( $row['cols'] ) > 0 ):
				
				$count = count( $row['cols'] );

				switch ( $count ) {
					
					case 4:
						$order_classes = array( 'first', 'second', 'third', 'last' );
					break;

					case 3:
						$order_classes = array( 'first', 'middle', 'last' );
					break;

					case 2:
						$order_classes = array( 'first', 'last' );
					break;

					default:
						$order_classes = array( 'first' );
					break;
				}

				foreach ( $row['cols'] as $cols => $col ):
					$columns_class = array();
					$grid_class = explode(' ', $col['grid_class'] );
					$dynamic_class[0] = $this->frontedit_active ? 'themify_builder_col' : $order_classes[ $cols ];
					$dynamic_class[1] = $this->frontedit_active ? '' : 'tb-column';
					$columns_class = array_merge( $columns_class, $grid_class );
					foreach( $dynamic_class as $class ) {
						array_push( $columns_class, $class );
					}
					$columns_class = array_unique( $columns_class );
					// remove class "last" if the column is fullwidth
					if ( 1 == $count ) {
						if ( ( $key = array_search( 'last', $columns_class ) ) !== false) {
							unset( $columns_class[ $key ] );
						}
					}
					$print_column_classes = implode( ' ', $columns_class );
					?>

			<div class="<?php echo $print_column_classes; ?>">
				<?php if($this->frontedit_active): ?>
				<div class="themify_module_holder">
					<div class="empty_holder_text"><?php _e('drop module here', 'themify') ?></div><!-- /empty module text -->
				<?php endif; ?>
					
					<?php
						if ( isset( $col['modules'] ) && count( $col['modules'] ) > 0 ) { 
							foreach ( $col['modules'] as $modules => $mod ) { 
								$w_wrap = ( $this->frontedit_active ) ? true : false;
								$w_class = ( $this->frontedit_active ) ? 'r'.$rows.'c'.$cols.'m'.$modules : '';
								$identifier = array( $rows, $cols, $modules ); // define module id
								$this->get_template_module( $mod, $builder_id, true, $w_wrap, $w_class, $identifier );
							}
						}
					?>
				
				<?php if ( $this->frontedit_active ): ?>
				</div>
				<!-- /module_holder -->
				<div class="col_dragger ui-resizable-handle ui-resizable-e" title="<?php _e('Drag left/right to change columns','themify') ?>"></div><!-- /col_dragger -->
				<?php endif; ?>
			</div>
			<!-- /col -->
			<?php endforeach; endif; // end col loop ?>

			<?php if ( $this->frontedit_active ): ?>
			</div> <!-- /themify_builder_row_content -->
			
			<?php $row_data_styling = isset( $row['styling'] ) ? json_encode( $row['styling'] ) : json_encode( array() ); ?>
			<div class="row-data-styling" data-styling="<?php echo esc_attr( $row_data_styling ); ?>"></div>
			<?php endif; ?>
			
			<?php do_action('themify_builder_row_end', $builder_id, $row ); ?>
		
		</div>
		<!-- /row_inner -->
	</div>
	<!-- /module_row -->

	<?php endforeach; // end row loop ?>

	<?php
		if ( $this->frontedit_active ) {
			themify_builder_col_detection();
		}
	?>

</div>
<!-- /themify_builder_content -->