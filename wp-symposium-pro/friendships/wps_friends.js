jQuery(document).ready(function() {

	// ***** Friendships (admin) *****	

	if (jQuery("#wps_member1").length) {

		if (jQuery("#wps_member1").val() == '') {
			jQuery("#wps_member1").select2({
			    minimumInputLength: 1,
			    query: function (query) {
					jQuery.post(
					    wps_friendships_ajax.ajaxurl,
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

		if (jQuery("#wps_member2").val() == '') {
			jQuery("#wps_member2").select2({
			    minimumInputLength: 1,
			    query: function (query) {
					jQuery.post(
					    wps_friendships_ajax.ajaxurl,
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

	// ***** Friendships (user interface) *****	

	// Make (add) friendship request
	jQuery(".wps_friends_add").click(function (event) {

		jQuery.post(
		    wps_friendships_ajax.ajaxurl,
		    {
		        action : 'wps_friends_add',
		        user_id: jQuery(this).attr('rel')
		    },
		    function(response) {
		    	location.reload();
		    }   
		);

	});

	// Accept friendship request
	jQuery(".wps_friends_accept").click(function (event) {

		jQuery.post(
		    wps_friendships_ajax.ajaxurl,
		    {
		        action : 'wps_friends_accept',
		        post_id: jQuery(this).attr('rel')
		    },
		    function(response) {
		    	location.reload();
		    }   
		);

	});

	// Reject friendship request
	jQuery(".wps_friends_reject").click(function (event) {

		jQuery.post(
		    wps_friendships_ajax.ajaxurl,
		    {
		        action : 'wps_friends_reject',
		        post_id: jQuery(this).attr('rel')
		    },
		    function(response) {
		    	location.reload();
		    }   
		);

	});

	// Cancel friendship
	jQuery(".wps_friends_cancel").click(function (event) {

		jQuery.post(
		    wps_friendships_ajax.ajaxurl,
		    {
		        action : 'wps_friends_reject',
		        post_id: jQuery(this).attr('rel')
		    },
		    function(response) {
		    	location.reload();
		    }   
		);

	});



})
