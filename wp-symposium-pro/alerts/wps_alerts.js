jQuery(document).ready(function() {

	if (jQuery("#wps_alerts_activity").length) {
		jQuery("#wps_alerts_activity").select2({
			minimumInputLength: -1,
			dropdownCssClass: 'wps_alerts_activity',
		});
	};

	jQuery('#wps_alerts_activity').on("change", function(e) { 

		jQuery("body").addClass("wps_wait_loading");

		var alert_id = jQuery(this).val();
		var selected = jQuery(this).find('option:selected');
		var url = selected.data('url');

		if (url == 'make_all_read') {

			jQuery.post(
			    wps_alerts.ajaxurl,
			    {
			        action : 'wps_alerts_make_all_read',
			        alert_id : alert_id,
			        url : url
			    },
			    function(response) {
					jQuery(".wps_alerts_unread").removeClass("wps_alerts_unread");
					jQuery("#wps_alerts_activity option[value='count']").remove();
					jQuery("body").removeClass("wps_wait_loading");
			    }   
			);

		} else {

			jQuery.post(
			    wps_alerts.ajaxurl,
			    {
			        action : 'wps_alerts_activity_redirect',
			        alert_id : alert_id,
			        url : url
			    },
			    function(response) {
					window.location.assign(response);
			    }   
			);
		}

	});	

	// ***** Users for custom post *****	
	if (jQuery("#wps_alert_recipient").length) {

		if (jQuery("#wps_alert_recipient").val() == '') {
			jQuery("#wps_alert_recipient").select2({
			    minimumInputLength: 1,
			    query: function (query) {
					jQuery.post(
					    wps_alerts.ajaxurl,
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

	// Clear all sent alerts
	jQuery(".wps_alerts_list_item_link").click(function (event) {
        var url = jQuery(this).data('url');
        var alert_id = jQuery(this).data('id');
        
        jQuery.post(
            wps_alerts.ajaxurl,
            {
                action : 'wps_alerts_activity_redirect',
                alert_id : alert_id,
                url : url
            },
            function(response) {
                window.location.assign(response);
            }   
        );
        
    });	

    // Mark all as read (for list)
    jQuery("#wps_make_all_read").click(function (event) {

        jQuery(this).parent().remove();
        jQuery('#wps_alerts_flag_unread').remove();
        jQuery(".wps_alerts_unread").removeClass("wps_alerts_unread");
        
        jQuery.post(
            wps_alerts.ajaxurl,
            {
                action : 'wps_alerts_make_all_read',
            },
            function(response) {
            }   
        );

	});	
    
    // Delete alert from list
    jQuery(".wps_alerts_list_item_delete").click(function (event) {

        jQuery(this).parent().slideUp('fast');
        
        jQuery.post(
            wps_alerts.ajaxurl,
            {
                action : 'wps_alerts_list_item_delete',
                alert_id : jQuery(this).attr('rel'),
            },
            function(response) {
            }   
        );

	});	    

    // Show delete icon on hover
    jQuery(".wps_alerts_list_item").hover(function (event) {

        jQuery(this).children('.wps_alerts_list_item_delete').show();

	});	    
    
    // Hide delete icon when mouse leaves
    jQuery(".wps_alerts_list_item").mouseleave(function (event) {
        
        jQuery(".wps_alerts_list_item_delete").hide();

	});	   
    
    // Delete all
    jQuery("#wps_alerts_delete_all").click(function (event) {

        jQuery(".wps_alerts_list_item").slideUp('fast');
        jQuery(this).hide();
        jQuery("#wps_mark_all_as_read_div").hide();
        
        jQuery.post(
            wps_alerts.ajaxurl,
            {
                action : 'wps_alerts_delete_all',
            },
            function(response) {
            }   
        );

	});	    
    
})
