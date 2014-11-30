(function( $ ) {
	'use strict';
	
	var map, marker_lat, marker_long; 
	var swiftype_map_markers = []; 

	function init(data){
		var canvas = jQuery("#swiftype-map").get(0);
		var infowindow = new google.maps.InfoWindow();
		if(canvas) {

			var zoom = parseInt(swiftype_geo_data.map_zoom);

			var options = {
				zoom: zoom, 
				center: new google.maps.LatLng(swiftype_geo_data.geo_lat, swiftype_geo_data.geo_long)
			};
			map = new google.maps.Map(canvas, options);

		}
		if(swiftype_geo_data.search_form == "true"){
			var form_content = wp.template( "swiftype-geo-form" ); 
			$("#swiftype-map").before(form_content);
		}

		process_map_markers(swiftype_geo_data.swiftype_markers); 

	}

	function process_map_markers(markers){
		$.each( markers, function( key, val ) {
			var LocLatlng = new google.maps.LatLng( val.geolocation.lat , val.geolocation.lon );
			var infowindow = new google.maps.InfoWindow({ maxWidth: 350 });

			var marker = new google.maps.Marker({
				position: LocLatlng,
				map: map,
			});
			swiftype_map_markers.push(marker);

			google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function() {
					var content = "<a class='swiftype-map-marker' href='"+val.url+"'>"+val.title+"</a>";
					infowindow.setContent(content);
					infowindow.open(map, marker);
				}
			})(marker, key));
		});
	}

	/* 
	 *	Function gets latitude and longitude from Google maps api
	 */
	function geocode_address(address, callback){
		var $geocoder = new google.maps.Geocoder();
		$geocoder.geocode( { 'address' : address }, function(results, status) {
			if(status == google.maps.GeocoderStatus.OK) {
				var location = {
					geo_lat: 	results[0].geometry.location.lat(),
					geo_long: 	results[0].geometry.location.lng()
				}
				// send Latitude and Longitude to callback function
				callback.call(location);
			}else{
				alert('geocode was not successful for the following reason: ' + status);
			}
		} ); 
	}

	//clear old markers from marker array for looking at closer 
	function deleteOverlays() {
	  if (swiftype_map_markers) {
	    for (var i in swiftype_map_markers) {
			swiftype_map_markers[i].setMap(null);
	    }
	    swiftype_map_markers.length = 0;
	  }
	}

	function search_success( data ){
		var pos = new google.maps.LatLng( marker_lat, marker_long);
		map.setCenter(pos);

		deleteOverlays();
		process_map_markers( data );
	}

	function search_error( data ){
		console.log( data );
	}
	/*
	 *	Function called when user submits Geo-search form. 
	 */
	function search_swiftype( event ){
		event.preventDefault(); 
		var address = $('#swiftype-geo-address').val(); 
		var nonce = window.swiftype_geo_search_nonce;
		
		if(address){
			geocode_address(address, function(){
				marker_lat = this.geo_lat;
				marker_long = this.geo_long; 

				wp.ajax.send( "search_swiftype_geo", {
					success: 	search_success,
					error: 		search_error,
					data: {
						nonce: 		nonce,
						loc_lat: 	this.geo_lat,
						loc_long: 	this.geo_long,
						distance: 	swiftype_geo_data.distance,
						post_types: swiftype_geo_data.post_types
					}
				});
			});
		}else{
			alert("please enter an address."); 
		}
	}
	
	$(document).ready(function(){
		init(swiftype_geo_data);
		$('#swiftype-geo-search').on( "submit", search_swiftype ); 
	});

})( jQuery );