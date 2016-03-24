<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php echo themify_get_html_schema(); ?> <?php language_attributes(); ?>>
<head>
<?php
/** Themify Default Variables
 @var object */
	global $themify;
	?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title itemprop="name"><?php wp_title( '' ); ?></title>

<?php
/**
 *  Stylesheets and Javascript files are enqueued in theme-functions.php
 */
?>

<!-- wp_header -->
<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

<?php themify_body_start(); //hook ?>
<div id="pagewrap" class="hfeed site">

	<div id="headerwrap">
	
    	<?php themify_header_before(); //hook ?>
		<div id="header" class="pagewidth">
        	<?php themify_header_start(); //hook ?>

			<?php echo themify_logo_image('site_logo'); ?>
			
			<?php if ( $site_desc = get_bloginfo('description') ) : ?><div id="site-description"><?php echo $site_desc; ?></div><?php endif; ?>

            <div id="main-nav-wrap">
                <div id="menu-icon" class="mobile-button"></div>
				<?php	if (function_exists('wp_nav_menu')) {
                    wp_nav_menu(array('theme_location' => 'main-nav' , 'fallback_cb' => 'themify_default_main_nav' , 'container'  => '' , 'menu_id' => 'main-nav'));
                } else {
                    themify_default_main_nav();
                } ?>
                <!--/main-nav -->
			</div>
            <!-- /#main-nav-wrap -->
			
			<div class="social-widget clearfix">
				<?php dynamic_sidebar('social-widget'); ?>
				<?php if(!themify_check('setting-exclude_rss')): ?>
					<div class="rss"><a href="<?php if(themify_get('setting-custom_feed_url') != ""){ echo themify_get('setting-custom_feed_url'); } else { bloginfo('rss2_url'); } ?>">RSS</a></div>
				<?php endif ?>
			</div>
			<!--/header widget --> 
			
            <?php themify_header_end(); //hook ?>
		</div>
		<!--/header -->
        <?php themify_header_after(); //hook ?>
		
	</div>
	<!--/headerwrap -->
    <?php themify_layout_before(); //hook ?>
