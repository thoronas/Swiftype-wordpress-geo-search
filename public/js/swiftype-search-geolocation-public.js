(function( $ ) {
	'use strict';
	function display_map_markers(data){
		var canvas = jQuery("#swiftype-map").get(0);
		var geocoder, map, marker_lat, marker_long; 
		var infowindow = new google.maps.InfoWindow();
		if(canvas) {

			var zoom = parseInt(swiftype_geo_data.map_zoom);

			var options = {
				zoom: zoom, 
				center: new google.maps.LatLng(swiftype_geo_data.geo_lat, swiftype_geo_data.geo_long) 
			};
			map = new google.maps.Map(canvas, options);

		}

		$.each( swiftype_geo_data.swiftype_markers, function( key, val ) {
			marker_lat = val.geolocation.lat;
			marker_long = val.geolocation.lon;
			var LocLatlng = new google.maps.LatLng(marker_lat,marker_long);

			var marker = new google.maps.Marker({
				position: LocLatlng,
				map: map,
			});

			google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function() {
					var content = "<a class='swiftype-map-marker' href='"+val.url+"'>"+val.title+"</a>";
					infowindow.setContent(content);
					infowindow.open(map, marker);
				}
			})(marker, key));
		});
		if(swiftype_geo_data.search_form == "true"){
			var form_content = wp.template( "swiftype-geo-form" ); 
			$("#swiftype-map").before(form_content);
		}
	}
	$(document).ready(function(){
		display_map_markers(swiftype_geo_data);    
	});

})( jQuery );