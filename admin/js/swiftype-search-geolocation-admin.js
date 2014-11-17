(function( $ ) {
	'use strict';

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
				console.log('geocode was not successful for the following reason: ' + status);
			}
		} ); 
	}

	function render_map(geo_lat, geo_long) {
		var canvas = $( '#swiftype-geo-location #swiftype-map')[0];
		if(canvas){
			if(geo_lat.length > 0 && geo_long.length > 0 ){
				var the_loc = new google.maps.LatLng( geo_lat, geo_long);
				var options = {
				  zoom: 8, 
				  center: the_loc
				};
				var map = new google.maps.Map(canvas, options);
				var marker = new google.maps.Marker({
				  map: map,
				  zoom: 6,
				  position: the_loc
				});
			}
		}
	}

	/* 
	 *	Function uses latitude and longitude to create Google map
	 */
	function swiftype_codeAddress() {
		var address = $('#swiftype-location-address').val(); 

		geocode_address(address, function(){

			var loc_lat = this.geo_lat;
			var loc_long = this.geo_long;
			if(loc_lat && loc_long){
				$('#swiftype-location-lat').val(loc_lat);
				$('#swiftype-location-long').val(loc_long); 
			}
			render_map(loc_lat, loc_long);
		});
	}

	function render_current_map(){
		var geo_lat = $('#swiftype-location-lat').val();
		var geo_long = $('#swiftype-location-long').val();
		if()
		render_map(geo_lat, geo_long);
	}
	function display_geo_swiftype_modal() {
		var modalTemplate = wp.template( "swiftype-geo-modal" );
		$('body').append( modalTemplate );
		var $that = $('.swiftype-modal-wrapper'); 
		$that.fadeIn( 'fast' );
	}
	function destroy_geo_swiftype_modal() {		 
		$('.swiftype-modal-wrapper').fadeOut( 'fast', function(){
			$('.swiftype-modal-wrapper').remove();
			$('.swiftype-overlay').remove();
		}); 
	}

	function get_addres_lat_long(that){
		var address = that.val();
		var lat_input = document.getElementById( 'swiftype-shortcode-lat'); 
		var long_input = document.getElementById( 'swiftype-shortcode-long');
		if(address.length > "0"){
			geocode_address(address, function(){
				lat_input.value = this.geo_lat;
				long_input.value = this.geo_long; 
			});
		}
	}	

	function insert_shortcode_params() {
		var $address = document.getElementById( 'swiftype-shortcode-address' ).value;
		var $lat = document.getElementById( 'swiftype-shortcode-lat' ).value;
		var $long = document.getElementById( 'swiftype-shortcode-long' ).value; 
		var $zoom = document.getElementById( 'swiftype-shortcode-zoom' ).value; 
		var $distance = document.getElementById( 'swiftype-shortcode-radius' ).value; 
		var $search_form = $( '#swiftype-shortcode-searchform' ).is(':checked') ;  
		var $post_types = ""; 
		$('.swiftype-post-types :checked').each( function(){
			$post_types += $(this).val()+",";
		} );
		// trim the trailing comma from the post types string
		$post_types = $post_types.substring(0, $post_types.length - 1);

		// because we're calling google via ajax we need to use a callback function. 
		geocode_address($address, function(){
			wp.media.editor.insert('[swiftype-geo-location lat="'+this.geo_lat+'" long="'+this.geo_long+'" zoom="'+$zoom+'" distance="'+$distance+'" search-form="'+$search_form+'" post-types="'+$post_types+'"]');
		});
		
		destroy_geo_swiftype_modal(); 

	}

	$(document).ready(function(){
		render_current_map(); 

		$('.swiftype-geocode').on('click', function(e){
			e.preventDefault(); 
			swiftype_codeAddress(); 
		});
		$('body').on('focusout', '#swiftype-shortcode-address', function(){
			get_addres_lat_long($(this));
		});
		$('#add-swiftype-geolocation').on('click', function(e){
			e.preventDefault(); 
			display_geo_swiftype_modal(); 
		});
		$('body').on('click', '.swiftype-overlay', function(e){
			destroy_geo_swiftype_modal(); 
		});
		$('body').on('click', '#insert-swiftype-shortcode', function(e){
			e.preventDefault(); 
			insert_shortcode_params(); 
		}); 

	});

})( jQuery );
