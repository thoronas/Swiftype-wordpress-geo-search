<?php

/**
 * Provide a dashboard view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin/partials
 */
?>

<?php $swiftype_geo_stored_meta = get_post_meta( $post->ID ); ?>

<div id="swiftype-geo-location">
	<label for "swiftype_location_address">
		<input id="swiftype-location-address" name="swiftype-location-address" type="text" value="<?php if( isset( $swiftype_geo_stored_meta['swiftype-location-address'] ) ) echo esc_attr($swiftype_geo_stored_meta['swiftype-location-address'][0]) ?>" placeholder="input address here..." />
	</label>
	<button class="swiftype-geocode button">Geo-locate Address</button>
	<input type="hidden" id="swiftype-location-lat" name="swiftype-location-lat" value="<?php if( isset( $swiftype_geo_stored_meta['swiftype-location-lat'] ) ) echo esc_attr($swiftype_geo_stored_meta['swiftype-location-lat'][0]) ?>" />
	<input type="hidden" id="swiftype-location-long" name="swiftype-location-long" value="<?php if( isset( $swiftype_geo_stored_meta['swiftype-location-long'] ) ) echo esc_attr($swiftype_geo_stored_meta['swiftype-location-long'][0]) ?>" />
	
	<div id="swiftype-map" style="width: 100%; height: 300px;"></div>
</div>