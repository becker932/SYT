<div class="lightbox_inner">
	<form id="themify_builder_import_form" method="POST">
		<?php foreach( $data as $field ): ?>
		<div class="themify_builder_field">
			<div class="themify_builder_label"><?php echo $field['label']; ?></div>
			<div class="themify_builder_input">
				<select name="<?php echo $field['post_type']; ?>">
					<option value=""></option>
					<?php foreach( $field['items'] as $option ): ?>
					<option value="<?php echo $option->ID; ?>"><?php echo $option->post_title; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<?php endforeach; ?>

		<p class="themify_builder_import_submit themify_builder_save">
			<input class="builder_button" type="submit" name="submit" value="<?php _e('Import', 'themify') ?>" />
		</p>

	</form>
</div> <!-- /lightbox_inner -->