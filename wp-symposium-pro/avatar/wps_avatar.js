jQuery(document).ready(function() {

	// Check for pre IE 10, if so use old upload file
    var undef,
        v = 3,
        new_browser = false,
        div = document.createElement('div'),
        all = div.getElementsByTagName('i');

    while (
        div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->',
        all[0]
    );

    var ie = v > 4 ? v : undef;
	if (ie == 'undefined' || ie == '' || ie == undef) new_browser = true;

	if (new_browser) {

		jQuery('.att_single').remove();

		if (jQuery('#avatarUploadForm').length) {
			jQuery('input[type=file]').wpsbootstrapFileInput();
		}

	} else {

		jQuery('.att_multiple').remove();
		jQuery('.att_single').show();

	}
	jQuery('#avatar_file_upload').show();

	var xinit = 150;
	var yinit = 150;
	var ratio = xinit / yinit;
	var ximg = jQuery('img#wps_upload').width();
	var yimg = jQuery('img#wps_upload').height();

	if ( yimg < yinit || ximg < xinit ) {
		if ( ximg / yimg > ratio ) {
			yinit = yimg;
			xinit = yinit * ratio;
		} else {
			xinit = ximg;
			yinit = xinit / ratio;
		}
	}

	jQuery('img#wps_upload').imgAreaSelect({
		handles: true,
		keys: true,
		aspectRatio: xinit + ':' + yinit,
		show: true,
		x1: 0,
		y1: 0,
		x2: xinit,
		y2: yinit,
		onInit: function () {
			jQuery('#width').val(xinit);
			jQuery('#height').val(yinit);
		},
		onSelectChange: function(img, c) {
			jQuery('#x1').val(c.x1);
			jQuery('#y1').val(c.y1);
			jQuery('#width').val(c.width);
			jQuery('#height').val(c.height);

			if (!c.width || !c.height)
    			return;

		    var scaleX = 150 / c.width;
		    var scaleY = 150 / c.height;

		    jQuery('#wps_preview img').css({
		        width: Math.round(scaleX * jQuery('#init_width').val()),
		        height: Math.round(scaleY * jQuery('#init_height').val()),
		        marginLeft: -Math.round(scaleX * c.x1),
		        marginTop: -Math.round(scaleY * c.y1)
		    });

		}
	});

});

function onEndCrop( coords ) {
	jQuery( '#x1' ).val(coords.x);
	jQuery( '#y1' ).val(coords.y);
	jQuery( '#width' ).val(coords.w);
	jQuery( '#height' ).val(coords.h);
}
