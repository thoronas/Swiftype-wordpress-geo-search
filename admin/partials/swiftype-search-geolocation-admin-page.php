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

<div class="wrap">
<!-- 	<h2 class="swiftype-geolocation-title"><?php _e('Swiftype Geolocation','swiftype-search-geolocation'); ?></h2>
 -->	<form action="options.php" method="post">
		<?php settings_fields( 'swiftype_geo_location_settings' ); ?>

		<?php do_settings_sections( 'swiftype_search_geolocation' ); ?>

		<?php submit_button( __( 'Save Changes', 'swiftype_search_geolocation' ) ); ?>
	</form>
</div>