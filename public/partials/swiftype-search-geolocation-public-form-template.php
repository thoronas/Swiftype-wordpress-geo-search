<?php

/**
 * Search Form used by shortcode for searching for posts based on Latitude and Longitude
 *
 * This file is used as template Search Form used by shortcode for searching for posts based on Latitude and Longitude.
 *
 * @since      1.0.0
 *
 */
?>
<script type="text/html" id="tmpl-swiftype-geo-form">
	<form id="swiftype-geo-search" class="swiftype-geo-search-form" >
		<label for="swiftype-geo-address">
			<input type="text" name="swiftype-geo-address" id="swiftype-geo-address" value="" placeholder="<?php _e( 'please enter your address', 'swiftype-search-geolocation' ); ?>">
		</label>
		<input type="submit" id="swiftype-geo-submit" value="<?php _e( 'Search', 'swiftype-search-geolocation' ); ?>">
	</form>
</script>