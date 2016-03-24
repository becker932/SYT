<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
///////////////////////////////////////
// Switch Template Layout Types
///////////////////////////////////////
$template_name = isset( $mod_settings['layout_display_slider'] ) && ! empty( $mod_settings['layout_display_slider'] ) ? $mod_settings['layout_display_slider'] : 'blog';

$this->retrieve_template( 'template-'.$mod_name.'-'.$template_name.'.php', array(
			'module_ID' => $module_ID,
			'mod_name' => $mod_name,
			'settings' => ( isset( $mod_settings ) ? $mod_settings : array() )
		), '', '', true );

?>