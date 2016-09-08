<?php
/** Themify Default Variables
 @var object */
	global $themify; ?>

	<?php themify_layout_after(); //hook ?>
    <div id="footerwrap">

        <?php themify_footer_before(); //hook ?>
		<div id="footer" class="pagewidth" itemscope="itemscope" itemtype="https://schema.org/WPFooter">
        	<?php themify_footer_start(); //hook ?>

			<?php get_template_part( 'includes/footer-widgets'); ?>

			<div class="footer-text clearfix">
				<?php themify_the_footer_text(); ?>
				<?php themify_the_footer_text('right'); ?>
			</div>
			<!--/footer-text -->
            <?php themify_footer_end(); //hook ?>
		</div>
		<!--/footer -->
        <?php themify_footer_after(); //hook ?>
	</div>
	<!--/footerwrap -->

</div>
<!--/pagewrap -->

<?php
/**
 *  Stylesheets and Javascript files are enqueued in theme-functions.php
 */
?>

<?php themify_body_end(); // hook ?>
<!-- wp_footer -->
<?php wp_footer(); ?>

</body>
</html>
