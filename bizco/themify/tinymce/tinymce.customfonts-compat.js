(function () {
	if( typeof themifyCustomFonts === 'undefined' )
		return;

	tinymce.create('tinymce.plugins.themifyCustomFonts', {
		createControl: function (n, cm) {
			switch(n) {
			case 'themifyCustomFonts':
				var mlb = cm.createListBox('themifyCustomFonts', {
					title: themifyCustomFonts.label,
					onselect: function (v) {
						if( v == '' ) {
							return false;
						} else if( v == 'add-more' ) {
							window.location = themifyCustomFonts.themify_page_url;
						} else {
							tinyMCE.activeEditor.focus();
							var sel_txt = tinyMCE.activeEditor.selection.getContent() || themifyCustomFonts.default_text;
							tinyMCE.activeEditor.execCommand('mceInsertContent', false, '<span class="' + v + '"><!-- [themify_gfont id="' + themifyCustomFonts.fonts[v] + '"] -->' + sel_txt + '</span>');
						}
					}
				});

				jQuery.each(themifyCustomFonts.fonts, function(i, val){
					mlb.add( val, i );
				});
				mlb.add( themifyCustomFonts.add_more, 'add-more' );

				return mlb;
			}
			return null;
		}
	});

	tinymce.PluginManager.add( 'themifyCustomFonts', tinymce.plugins.themifyCustomFonts );

	tinyMCE.onAddEditor.add( function(mgr,ed) {
		ed.onInit.add(function( editor ){
			jQuery(editor.dom.doc.head).append( '<link rel="stylesheet" href="' + themifyCustomFonts.stylesheet + '" type="text/css" />' ).append(themifyCustomFonts.styles);
		});
		return mgr;
	} );

})();