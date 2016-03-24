jQuery(document).ready(function() {

	/* Admin - add forum */

	jQuery("#wps_admin_forum_add_button").click(function (event) {
		if (jQuery('#wps_admin_forum_add_details').css('display') != 'none') {

			if ( jQuery('#wps_admin_forum_add_name').val().length != 0) {
				jQuery('#wps_admin_forum_add_name').css('background-color', '#fff').css('color', '#000');
				if (jQuery('#wps_admin_forum_add_description').val().length == 0) {
					event.preventDefault();
					jQuery('#wps_admin_forum_add_description').css('background-color', '#faa').css('color', '#000');
				}
			} else {
				event.preventDefault();
				jQuery('#wps_admin_forum_add_name').css('background-color', '#faa').css('color', '#000');				
			}
		}
	});

	jQuery("#wps_admin_forum_add").click(function (event) {
		event.preventDefault();
		if (jQuery('#wps_admin_forum_add_details').css('display') == 'none') {
			jQuery('#wps_admin_forum_add_details').slideDown('fast');
		}
	});

	/* Add Post */

	if (jQuery("#wps_forum_post_button").length) {

		jQuery('#wps_forum_post_button').prop("disabled", false);
		jQuery('#wps_forum_post_title').val('');
		jQuery('#wps_forum_post_textarea').val('');
		jQuery('.wps_forum_extension_text').val(''); // Possible forum extensions
		jQuery('.wps_forum_extension_textarea').val(''); // Possible forum extensions
		
		jQuery("#wps_forum_post_button").click(function (event) {

			event.preventDefault();

			if(jQuery('#wps_forum_post_form').css('display') == 'none') {

				jQuery('#wps_forum_post_form').show();
				document.getElementById('wps_forum_post_title').focus();

			} else {

				if (jQuery('#wps_forum_post_title').val().length) {

					if (jQuery('#wps_forum_post_textarea').val().length) {

						// Check for mandatory fields
						var all_filled = true;
						jQuery('.wps_mandatory_field').each(function(i, obj) {
						    if (jQuery(this).val() == '') {
								jQuery(this).css('border', '1px solid red').css('background-color', '#faa').css('color', '#000');
								all_filled = false;
							}
						});

						if (all_filled) {
							jQuery(this).attr("disabled", true);

							jQuery("body").addClass("wps_wait_loading");
					
					        var iframe = jQuery('<iframe name="wps_forum_postiframe" id="wps_forum_postiframe" style="display:none;" />');
					        jQuery("body").append(iframe);

					        var form = jQuery('#wps_forum_post_theuploadform');
					        form.attr("action", jQuery('#wps_forum_plugins_url').val()+"/lib_forum.php");
					        form.attr("method", "post");
					        form.attr("enctype", "multipart/form-data");
					        form.attr("encoding", "multipart/form-data");
					        form.attr("target", "wps_forum_postiframe");
					        form.attr("file", jQuery('#wps_forum_image_upload').val());
					        form.submit();

					        jQuery("#wps_forum_postiframe").load(function () {
					            iframeContents = jQuery("#wps_forum_postiframe")[0].contentWindow.document.body.innerHTML;
								document.location = document.location + iframeContents;
					        });
					    }

					} else {

						jQuery('#wps_forum_post_textarea').css('border', '1px solid red').css('background-color', '#faa').css('color', '#000');

					}

				} else {

					jQuery('#wps_forum_post_title').css('border', '1px solid red').css('background-color', '#faa').css('color', '#000');

				}

			}

		});

	}

	/* Add Comment (Reply) */
	
	if (jQuery("#wps_forum_comment_button").length) {

		jQuery('#wps_forum_comment_button').prop("disabled", false);
		jQuery('#wps_forum_comment').val('');
		
		jQuery("#wps_forum_comment_button").click(function (event) {

			event.preventDefault();

			if(jQuery('#wps_forum_comment_form').css('display') == 'none') {

				jQuery('#wps_forum_comment_form').show();
				document.getElementById('wps_forum_comment').focus();

			} else {

				if (jQuery('#wps_forum_comment').val().length || wps_forum_ajax.is_admin) {

					jQuery(this).attr("disabled", true);

					jQuery("body").addClass("wps_wait_loading");
			
			        var iframe = jQuery('<iframe name="wps_forum_commentiframe" id="wps_forum_commentiframe" style="display: none;" />');
			        jQuery("body").append(iframe);

			        var form = jQuery('#wps_forum_comment_theuploadform');
			        form.attr("action", jQuery('#wps_forum_plugins_url').val()+"/lib_forum.php");
			        form.attr("method", "post");
			        form.attr("enctype", "multipart/form-data");
			        form.attr("encoding", "multipart/form-data");
			        form.attr("target", "wps_forum_commentiframe");
			        form.submit();

			        jQuery("#wps_forum_commentiframe").load(function () {
			            iframeContents = jQuery("#wps_forum_commentiframe")[0].contentWindow.document.body.innerHTML;
						if (iframeContents == 'reload') {
                            location.reload();
                        } else {
                            window.location = iframeContents;
                        }
			        });

				} else {

					jQuery('#wps_forum_comment').addClass('wps_field_error');

				}

			}

		});

	}

	// Add sub comment (comment on reply)

	if (jQuery(".wps_forum_post_comment_form_submit").length) {

		jQuery('.wps_forum_post_comment_form_submit').prop("disabled", false);
		jQuery('.wps_forum_post_comment_form').val('');
		
		jQuery(".wps_forum_post_comment_form_submit").click(function (event) {

			event.preventDefault();
			jQuery("body").addClass("wps_wait_loading");

			var id = jQuery(this).attr('rel');

			if(jQuery('#sub_comment_div_'+id).css('display') == 'none') {

				jQuery('#sub_comment_div_'+id).slideDown('fast');
				document.getElementById('sub_comment_'+id).focus();

			} else {

				if (jQuery('#sub_comment_'+id).val().length) {

					var comment = jQuery('#sub_comment_'+id).val();
					jQuery('#sub_comment_'+id).val('');

					jQuery.post(
					    wps_forum_ajax.ajaxurl,
					    {
					        action : 'wps_forum_add_subcomment',
					        post_id : jQuery(this).data('post-id'),
					        comment_id : id,
					        comment : comment,
					        size : jQuery(this).data('size'),
					        wps_forum_moderate : 1,
					    },
					    function(response) {

							jQuery('#sub_comment_div_'+id).prepend(response);
							jQuery("body").removeClass("wps_wait_loading");

					    }   
					);

				} else {

					jQuery('#sub_comment_'+id).css('border', '1px solid red').css('background-color', '#faa').css('color', '#000');

				}

			}

		});

	}

	// Reopen post
	
	if (jQuery("#wps_forum_comment_reopen_button").length) {

		jQuery('#wps_forum_comment_reopen_button').prop("disabled", false);
		
		jQuery("#wps_forum_comment_reopen_button").click(function (event) {

			event.preventDefault();
			jQuery(this).attr("disabled", true);
			jQuery("body").addClass("wps_wait_loading");

			var post_id = jQuery('#reopen_post_id').val();

			jQuery.post(
			    wps_forum_ajax.ajaxurl,
			    {
			        action : 'wps_forum_comment_reopen',
			        post_id : post_id
			    },
			    function(response) {
			    	location.reload();
			    }   
			);

		});

	}

	/* Edit Post */

	if (jQuery("#wps_post_forum_slug").length) {
		jQuery("#wps_post_forum_slug").select2();
	}

	/* Forum Settings */
	
	jQuery(".wps_forum_settings").mouseover(function (event) {
		jQuery('.wps_forum_settings_options').hide();
		jQuery('.wps_comment_settings_options').hide();
		jQuery(this).next('.wps_forum_settings_options').show();
	});	

	jQuery(".wps_forum_comment_settings").mouseover(function (event) {
		jQuery('.wps_forum_settings_options').hide();
		jQuery('.wps_comment_settings_options').hide();
		jQuery(this).next('.wps_forum_comment_settings_options').show();
	});	

	jQuery(document).mouseup(function (e) {
		jQuery('.wps_forum_settings_options').hide();
		jQuery('.wps_comment_settings_options').hide();
	});

	/* Closed switch */

	jQuery("#closed_switch").click(function (event) {
		var state = 'off';
		if (jQuery(this).is(":checked")) {
			jQuery('.wps_forum_post_closed').slideDown('fast');
			state = 'on';
		} else {
			jQuery('.wps_forum_post_closed').slideUp('fast');
		}
		jQuery.post(
		    wps_forum_ajax.ajaxurl,
		    {
		        action : 'wps_forum_closed_switch',
		        state : state
		    },
		    function(response) {
		    }   
		);
	});	
	

});
