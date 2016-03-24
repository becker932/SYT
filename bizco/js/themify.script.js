;// Themify Theme Scripts - http://themify.me/

jQuery(document).ready(function($){

	/////////////////////////////////////////////
	// Toggle menu on mobile 							
	/////////////////////////////////////////////
	$("#menu-icon").click(function(){
		$("#headerwrap #main-nav").fadeToggle();
		$("#headerwrap #searchform").hide();
		$(this).toggleClass("active");
	});

	// Lightbox / Fullscreen initialization ///////////
	if(typeof ThemifyGallery !== 'undefined'){ ThemifyGallery.init({'context': jQuery(themifyScript.lightboxContext)}); }

});
