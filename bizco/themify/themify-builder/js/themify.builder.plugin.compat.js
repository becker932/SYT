(function($){
	// WordPress SEO by Yoast
	var Builder_WPSEO = {
		wpseo_meta_desc_length: 0,
		init: function(){
			var self = this,
				timeout;

			if( 'undefined' === typeof wpseo_meta_desc_length && 'undefined' === typeof wpseoMetaboxL10n ) return;

			if ( 'undefined' === typeof wpseo_meta_desc_length && 'undefined' !== typeof wpseoMetaboxL10n.wpseo_meta_desc_length ) {
				this.wpseo_meta_desc_length = wpseoMetaboxL10n.wpseo_meta_desc_length;
			} else {
				this.wpseo_meta_desc_length = 'undefined' !== typeof wpseo_meta_desc_length ? wpseo_meta_desc_length : 0;
			}
			
			// check if wpseo activated
			if( 'undefined' !== typeof updateDesc && 'undefined' !== typeof boldKeywords && ( 'undefined' !== typeof wpseo_meta_desc_length || 'undefined' !== typeof wpseoMetaboxL10n.wpseo_meta_desc_length ) && 'undefined' !== typeof yst_clean) {
				// Perform action
				this.updateDesc();
				$('#yoast_wpseo_metadesc').keyup(function () {
					clearTimeout(timeout);
					timeout = setTimeout(function(){
						self.updateDesc();
					}, 500);
				});
				$(document).on('change', '#yoast_wpseo_metadesc', this.updateDesc)
				.on('change', '#yoast_wpseo_focuskw', this.updateDesc);
			}
		
		},

		updateDesc: function(){
			var self = Builder_WPSEO,
				get_desc = $('#wpseosnippet .desc span.content').html(),
				desc = $('<div />').html(get_desc).text().replace('...', ''),
				metadesc = $.trim(yst_clean($("#yoast_wpseo_metadesc").val()));
			
			if( metadesc == '') {
				$.ajax({
					type: "POST",
					url: themifyBuilder.ajaxurl,
					data:
					{
						action : 'wpseo_get_html_builder',
						nonce : themifyBuilder.tfb_load_nonce,
						post_id : $('input#post_ID').val()
					},
					beforeSend: function(xhr){
						$("#wpseosnippet .desc span.content").html('Updating meta desc ...');
					},
					success: function( data ){
						var result = JSON.parse(data),
							new_desc = result.text_str,
							merge_desc = desc + new_desc,
							real_desc = merge_desc.substr(0, self.wpseo_meta_desc_length);
						
						if (real_desc.length > self.wpseo_meta_desc_length)
							var space = real_desc.lastIndexOf(" ", ( self.wpseo_meta_desc_length - 3 ));
						else
							var space = self.wpseo_meta_desc_length;
						real_desc = real_desc.substring(0, space).concat(' <strong>...</strong>');
						real_desc = boldKeywords(real_desc, false);

						$("#wpseosnippet .desc span.content").html(real_desc);
						testFocusKw();
					}
				});
			}

		}
	};

	$(window).load(function(){
		Builder_WPSEO.init();
	});
})(jQuery);