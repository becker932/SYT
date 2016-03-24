jQuery(document).ready(function() {

	jQuery('#wps_activity_items').show();
	jQuery('#wps_activity_post_div').show();
	jQuery('.wps_activity_settings').show();
	jQuery('#wps_activity_post_button').attr("disabled", false);

    // Admin - remove hidden flags
    
    jQuery("#wps_activity_unhide_all").click(function (event) {

        jQuery.post(
            wps_ajax.ajaxurl,
            {
                action : 'wps_activity_unhide_all',
                post_id : jQuery(this).attr('rel'),
            },
            function(response) {
                alert('OK');
            }   
        ); 

    });
    
	// Admin - new activity
	if (jQuery("#wps_target").length) {

		if (jQuery("#wps_target").val() == '') {
			jQuery("#wps_target").select2({
			    minimumInputLength: 1,
			    query: function (query) {
					jQuery.post(
					    wps_ajax.ajaxurl,
					    {
					        action : 'wps_get_users',
					        term : query.term
					    },
					    function(response) {
					    	var json = jQuery.parseJSON(response);
					    	var data = {results: []}, i, j, s;
							for(var i = 0; i < json.length; i++) {
						    	var obj = json[i];
						    	data.results.push({id: obj.value, text: obj.label});
							}
							query.callback(data);	    	
					    }   
					);
			    }
			});
		}	

	}

	// Activity Settings
	jQuery(".wps_activity_settings").mouseover(function (event) {
		jQuery('.wps_activity_settings_options').hide();
		jQuery('.wps_comment_settings_options').hide();
		jQuery(this).next('.wps_activity_settings_options').show();
	});

	// Comment Settings
	jQuery(".wps_comment_settings").mouseover(function (event) {
		jQuery('.wps_activity_settings_options').hide();
		jQuery('.wps_comment_settings_options').hide();
		jQuery(this).next('.wps_comment_settings_options').show();
	});

	jQuery(document).mouseup(function (e) {
		jQuery('.wps_activity_settings_options').hide();
		jQuery('.wps_comment_settings_options').hide();
	});


	// Add activity post
	if (jQuery('#wps_activity_post').length) {

		jQuery("#wps_activity_post_button").click(function (event) {

			event.preventDefault();

			if (jQuery('#wps_activity_post').val().length) {

				jQuery("body").addClass("wps_wait_loading");

				jQuery(this).attr("disabled", true);

		        var iframe = jQuery('<iframe name="postiframe" id="postiframe" style="display: none;" />');
		        jQuery("body").append(iframe);

		        var form = jQuery('#theuploadform');
		        form.attr("action", wps_activity_ajax.plugins_url+"/lib_activity.php");
		        form.attr("method", "post");
		        form.attr("enctype", "multipart/form-data");
		        form.attr("encoding", "multipart/form-data");
		        form.attr("target", "postiframe");
		        form.attr("file", jQuery('#wps_activity_image_upload').val());
		        form.submit();

		        jQuery("#postiframe").load(function () {
		            iframeContents = jQuery("#postiframe")[0].contentWindow.document.body.innerHTML;
		            if (QueryString.post != undefined) {
		            	if (QueryString.user_id != undefined) {
				            window.location = window.location.pathname+'?user_id='+QueryString.user_id;
				        } else {
				            window.location = window.location.pathname;
				        }
			        } else {
						location.reload();
			        }
		        });

		    } else {
		    	jQuery('#wps_activity_post').css('border', '1px solid red');
		    	jQuery('#wps_activity_post').css('background-color', '#faa');
		    	jQuery('#wps_activity_post').css('color', '#000');
		    }

	        return false;

	    });

	}

	// Add activity comment
	jQuery(".wps_activity_post_comment_button").click(function (event) {
        
		var id = jQuery(this).attr('rel');		
		var comment = jQuery('#post_comment_'+id).val();

		if (comment.length) {

			jQuery("body").addClass("wps_wait_loading");

			jQuery('#post_comment_'+id).val('');

			jQuery.post(
			    wps_activity_ajax.ajaxurl,
			    {
			        action : 'wps_activity_comment_add',
			        post_id : id,
			        comment_content: comment,
			        size : jQuery(this).data('size'),
			        link : jQuery(this).data('link')
			    },
			    function(response) {
			    	jQuery('#wps_activity_'+id+'_content').append(response);
			    	jQuery("body").removeClass("wps_wait_loading");
			    }   
			);

		}

	});

	// Make post sticky
	jQuery(".wps_activity_settings_sticky").click(function (event) {

		var id = jQuery(this).attr('rel');
		jQuery(this).hide();
		var height = jQuery('#wps_activity_'+id).height();
		jQuery('#wps_activity_'+id).animate({ height: 1 }, 500, function() {
			jQuery("#wps_activity_items").prepend(jQuery('#wps_activity_'+id));
			jQuery('#wps_activity_'+id).animate({ height: height }, 500);
			
			jQuery.post(
			    wps_activity_ajax.ajaxurl,
			    {
			        action : 'wps_activity_settings_sticky',
			        post_id : id
			    },
			    function(response) {
			    }   
			);

		});

	});

    // Hide post
	jQuery(".wps_activity_settings_hide").click(function (event) {

		var id = jQuery(this).attr('rel');
		jQuery(this).hide();
		var height = jQuery('#wps_activity_'+id).height();
        
        jQuery('#wps_activity_'+id).slideUp();
        //jQuery("#wps_activity_items").prepend(jQuery('#wps_activity_'+id));
        //jQuery('#wps_activity_'+id).animate({ height: height }, 500);

        jQuery.post(
            wps_activity_ajax.ajaxurl,
            {
                action : 'wps_activity_settings_hide',
                post_id : id
            },
            function(response) {
            }   
        );

	});    

	// Make post unsticky
	jQuery(".wps_activity_settings_unsticky").click(function (event) {

		var id = jQuery(this).attr('rel');
		jQuery(this).hide();

		jQuery('#wps_activity_'+id).wps_shake(3, 5, 100);

		jQuery.post(
		    wps_activity_ajax.ajaxurl,
		    {
		        action : 'wps_activity_settings_unsticky',
		        post_id : id
		    },
		    function(response) {
		    }   
		);

	});

	// Delete post from settings
	jQuery(".wps_activity_settings_delete").click(function (event) {

		var id = jQuery(this).attr('rel');
		jQuery('#wps_activity_'+id).fadeOut('slow');

		jQuery.post(
		    wps_activity_ajax.ajaxurl,
		    {
		        action : 'wps_activity_settings_delete',
		        id : id
		    },
		    function(response) {
		    }   
		);

	});

	// Delete comment from settings
	jQuery(".wps_comment_settings_delete").click(function (event) {
		var id = jQuery(this).attr('rel');
		jQuery('#wps_comment_'+id).fadeOut('slow');

		jQuery.post(
		    wps_activity_ajax.ajaxurl,
		    {
		        action : 'wps_comment_settings_delete',
		        id : id
		    },
		    function(response) {
		    }   
		);

	});	

	// Clicked on more... to expand post
	jQuery(".activity_item_more").click(function (event) {
		var id = jQuery(this).attr('rel');
		jQuery('#activity_item_'+id).hide();
		jQuery('#activity_item_full_'+id).show();
	});

	// Show hidden comments
	jQuery(".wps_activity_hidden_comments").click(function (event) {
		jQuery(this).hide();
		jQuery('.wps_activity_item_'+jQuery(this).attr('rel')).slideDown('fast');
	});

});


var QueryString = function () {
  // This function is anonymous, is executed immediately and 
  // the return value is assigned to QueryString!
  var query_string = {};
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
    	// If first entry with this name
    if (typeof query_string[pair[0]] === "undefined") {
      query_string[pair[0]] = pair[1];
    	// If second entry with this name
    } else if (typeof query_string[pair[0]] === "string") {
      var arr = [ query_string[pair[0]], pair[1] ];
      query_string[pair[0]] = arr;
    	// If third or later entry with this name
    } else {
      query_string[pair[0]].push(pair[1]);
    }
  } 
    return query_string;
} ();

jQuery.fn.wps_shake = function(intShakes, intDistance, intDuration) {
    this.each(function() {
        jQuery(this).css("position","relative"); 
        for (var x=1; x<=intShakes; x++) {
        	jQuery(this).animate({left:intDistance*-1}, (intDuration/intShakes)/4)
    			.animate({left:intDistance}, (intDuration/intShakes)/2)
    			.animate({left:0}, (intDuration/intShakes)/4);
    	}
  	});
	return this;
};
