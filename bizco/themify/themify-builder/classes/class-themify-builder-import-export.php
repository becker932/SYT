<?php
/**
 * Class Builder Import Export
 * @package themify-builder
 */
class Themify_Builder_Import_Export {
	/**
	 * Constructor
	 */
	function __construct() {
		add_action( 'init', array( $this, 'do_export_file' ) );
		add_action( 'wp_ajax_builder_import_file', array( &$this, 'builder_import_file_ajaxify' ), 10 );
	}

	/**
	 * Do Export file
	 */
	function do_export_file() {

		if ( is_user_logged_in() && isset( $_GET['themify_builder_export_file'] ) && $_GET['themify_builder_export_file'] == true && 
			check_admin_referer( 'themify_builder_export_nonce' ) ) {
			
			$postid = (int) $_GET['postid'];
			$postdata = get_post( $postid );
			$data_name = $postdata->post_name;

			$builder_data = get_post_meta( $postid, apply_filters( 'themify_builder_meta_key', '_themify_builder_settings' ), true );
			$builder_data = maybe_serialize( $builder_data );

			if ( class_exists('ZipArchive') ) {
				$datafile = 'builder_data_export.txt';
				$handler = @fopen( $datafile, 'w' );
				@fwrite( $handler, $builder_data );
				@fclose( $handler );
				$files_to_zip = array( $datafile );
				$file = $data_name . '_themify_builder_export_' . date( 'Y_m_d' ) . '.zip';
				$result = themify_create_zip( $files_to_zip, $file, true );
			}

			if ( isset( $result ) && $result ) {
				if ( ( isset( $file ) ) && ( file_exists( $file ) ) ) {
					ob_start();
					header('Pragma: public');
					header('Expires: 0');
					header("Content-type: application/force-download");
					header('Content-Disposition: attachment; filename="' . $file . '"');
					header("Content-Transfer-Encoding: Binary"); 
					header("Content-length: ".filesize( $file ) );
					header('Connection: close');
					ob_clean();
					flush(); 
					readfile( $file );
					unlink( $datafile );
					unlink( $file );
					exit();
				} else {
					return false;
				}
			} else {
				if ( ini_get('zlib.output_compression') ) {
					ini_set('zlib.output_compression', 'Off');
				}
				ob_start();
				header('Content-Type: application/force-download');
				header('Pragma: public');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Cache-Control: private',false);
				header('Content-Disposition: attachment; filename="'.$data_name.'_themify_builder_export_'.date("Y_m_d").'.txt"');
				header('Content-Transfer-Encoding: binary');
				ob_clean();
				flush();
				echo $builder_data;
				exit();
			}
		}
	}

	/**
	 * Builder Import Lightbox
	 * @return html
	 */
	function builder_import_file_ajaxify(){
		check_ajax_referer( 'tfb_load_nonce', 'nonce' );

		$output = '<div class="lightbox_inner themify-builder-import-file-inner">';
		$output .= sprintf( '<h3>%s</h3>', __( 'Select a file to import', 'themify') );

		if ( is_multisite() && !is_upload_space_available() ) {
			$output .= sprintf( __( '<p>Sorry, you have filled your %s MB storage quota so uploading has been disabled.</p>', 'themify' ), get_space_allowed() );
		} else {
			$output .= sprintf( '<p><div class="themify-builder-plupload-upload-uic hide-if-no-js tf-upload-btn" id="%sthemify-builder-plupload-upload-ui">
										<input id="%sthemify-builder-plupload-browse-button" type="button" value="%s" class="builder_button" />
										<span class="ajaxnonceplu" id="ajaxnonceplu%s"></span>
								</div></p>', 'themify_builder_import_file', 'themify_builder_import_file', __('Upload', 'themify'), wp_create_nonce('themify_builder_import_filethemify-builder-plupload') );
			
			$max_upload_size = (int) wp_max_upload_size() / ( 1024 * 1024 );
			$output .= sprintf( __( '<p>Maximum upload file size: %d MB.</p>', 'themify' ), $max_upload_size );
		}
		
		$output .= '</div>';
		echo $output;
		die();
	}
}