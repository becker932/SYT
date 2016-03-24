var ThemifyMap = {

	request: 0,

	/**
	 * Function to initialize a Google Maps instance
	 * @param address Map address to display
	 * @param num CSS ID
	 * @param zoom 0 - 15
	 * @param type ROADMAP SATELLITE HYBRID TERRAIN
	 * @param scroll
	 */
	initialize: function( address, num, zoom, type, scroll ) {
		var delay = this.request++ * 500;
		setTimeout( function(){
			var geo = new google.maps.Geocoder(),
				latlng = new google.maps.LatLng(-34.397, 150.644),
				mapOptions = {
					'zoom': zoom,
					center: latlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					scrollwheel: scroll == 'yes'
				};
			switch( type.toUpperCase() ) {
				case 'ROADMAP':
					mapOptions.mapTypeId = google.maps.MapTypeId.ROADMAP;
					break;
				case 'SATELLITE':
					mapOptions.mapTypeId = google.maps.MapTypeId.SATELLITE;
					break;
				case 'HYBRID':
					mapOptions.mapTypeId = google.maps.MapTypeId.HYBRID;
					break;
				case 'TERRAIN':
					mapOptions.mapTypeId = google.maps.MapTypeId.TERRAIN;
					break;
			}
			var	map = new google.maps.Map( document.getElementById( 'themify_map_canvas_' + num ), mapOptions );
			geo.geocode( { 'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					map.setCenter(results[0].geometry.location);
					var marker = new google.maps.Marker({
						map: map,
						position: results[0].geometry.location	});
				}
			});
		}, delay );
	}
};