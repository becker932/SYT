jQuery(document).ready(function() {

	// ***** Check passwords match on save user meta *****	

	jQuery( "#wps_usermeta_change" ).submit(function( event ) {

	  	if (jQuery('#wpspro_password').length) {
			if (jQuery('#wpspro_password').val() != jQuery('#wpspro_password2').val()) {
				jQuery('#wpspro_password').css('border', '1px solid red').css('background-color', '#faa').css('color', '#000');				
				jQuery('#wpspro_password2').css('border', '1px solid red').css('background-color', '#faa').css('color', '#000');				
				event.preventDefault();
			}
		}
	});

	// wps_user_button

	jQuery(".wps_user_button").click(function (event) {

		var url = jQuery(this).attr('rel');		
		event.preventDefault();

		window.location = url;

	});
    
    // wps_close_account
    
    jQuery('#wps_close_account').click(function (event) {
       
        var answer = confirm(jQuery(this).data('sure'));
        if (answer) {
            jQuery.post(
                wps_usermeta.ajaxurl,
                {
                    action : 'wps_deactivate_account',
                    user_id: jQuery(this).data('user'),
                },
                function(response) {
                    alert(jQuery('#wps_close_account').data('logout'));
                    var url = jQuery('#wps_close_account').data('url');
                    if (url) {
                        window.location = url;
                    } else {
                        location.reload();
                    }
                }   
            );
        }
    });


})
