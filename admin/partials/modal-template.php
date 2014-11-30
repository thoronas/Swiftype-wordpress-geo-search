<?php 
/**
 * Template for Swiftype Geo Search Shortcode Settings Modal
 */
?>
<script type="text/html" id="tmpl-swiftype-geo-modal">

<div class="swiftype-modal-wrapper">
	<div class="swiftype-modal">
		<h3>Set Center of Map</h3>
		<form id="swiftype-geo-shortcode">
			<label for="swiftype-shortcode-address">Address:
				<input id="swiftype-shortcode-address" class="sw-full-length" name="swiftype-shortcode-address" type="text" />
			</label>
			<!-- Hidden fields for storing google geo coder data. -->
			<input id="swiftype-shortcode-lat" disabled name="swiftype-shortcode-lat" type="hidden" />
			<input id="swiftype-shortcode-long" disabled name="swiftype-shortcode-long" type="hidden" />

			<label for="swiftype-shortcode-zoom" class="swiftype-half-length">Map Zoom Level: 
				<select id="swiftype-shortcode-zoom" name="swiftype-shortcode-zoom">
					<option value="0">0 (whole world) </option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7" selected="selected">7</option>
					<option value="8">8</option>
					<option value="9">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
					<option value="13">13</option>
					<option value="14">14</option>
					<option value="15">15</option>
					<option value="16">16</option>
					<option value="17">17</option>
					<option value="18">18</option>
					<option value="19">19 (individual buildings)</option>
				</select>
			</label>
			<label for="swiftype-shortcode-radius" class="swiftype-half-length">Search radius distance: 
				<select id="swiftype-shortcode-radius" name="swiftype-shortcode-radius">
					<option value="1">1km</option>
					<option value="5" selected="selected">5km</option>
					<option value="10">10km</option>
					<option value="20">20km</option>
					<option value="50">50km</option>
					<option value="100">100km</option>
				</select>				
			</label>
			<div class="swiftype-post-types">
				<h3 class="swiftype-divider">Include Post Types</h3>
				<?php 
					$active_cpts = get_option( 'swiftype-search-geolocation-post-types' ); 
					foreach($active_cpts as $cpt){
				?>
					<label class="swiftype-third-length" for="swiftype-shortcode-posttypes-<?php echo esc_attr( $cpt );?>">
						<?php echo esc_attr( $cpt );?>
						<input type="checkbox" checked value="<?php echo esc_attr( $cpt );?>" name="swiftype-shortcode-posttypes-<?php echo esc_attr( $cpt );?>">
					</label>

				<?php
					}
				?>
			</div>

			<label for="swiftype-shortcode-searchform"> 
				<h3 class="swiftype-divider">Include Search Form:</h3>
				<input type="checkbox" id="swiftype-shortcode-searchform" checked label="Include Searchform:" name="swiftype-shortcode-searchform">				
			</label>

			<button id="insert-swiftype-shortcode" class="button">Insert Shortcode</button>		
		</form>
	</div>
</div>
<div class="swiftype-overlay"></div>

</script>