<?php themify_sidebar_before(); //hook ?>
<div id="sidebar">
	<?php themify_sidebar_start(); //hook ?>
	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar-main') ); ?>
    <?php themify_sidebar_end(); //hook ?>
</div>
<!-- /sidebar -->
<?php themify_sidebar_after(); //hook ?>