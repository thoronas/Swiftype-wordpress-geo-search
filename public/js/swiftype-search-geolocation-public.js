function display_map_markers(data){
    var canvas = jQuery("#swiftype-map").get(0);
    var geocoder, map; 

    if(canvas) {

        var zoom = parseInt(8);

        var options = {
            zoom: zoom , 
            center: new google.maps.LatLng(swiftype_geo_data.geo_lat, swiftype_geo_data.geo_long) 
        };
        map = new google.maps.Map(canvas, options);

    }

    jQuery.each( swiftype_geo_data.swiftype_markers, function( key, val ) {
        marker_lat = val.geolocation.lat;
        marker_long = val.geolocation.lon;
        var LocLatlng = new google.maps.LatLng(marker_lat,marker_long);

        var marker = new google.maps.Marker({
            position: LocLatlng,
            map: map,
        });


    });
}
jQuery(document).ready(function($){
    display_map_markers(swiftype_geo_data);    
});

