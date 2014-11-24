<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Swiftype_Search_Geolocation
 * @subpackage Swiftype_Search_Geolocation/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Swiftype_Search_Geolocation
 * @subpackage Swiftype_Search_Geolocation/public
 * @author     Flynn O'Connor <flynnoconnor@gmail.com>
 */
class Swiftype_Search_Geolocation_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $swiftype_search_geolocation    The ID of this plugin.
	 */
	private $swiftype_search_geolocation;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $swiftype_search_geolocation       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $swiftype_search_geolocation, $version ) {

		$this->name = $swiftype_search_geolocation;
		$this->version = $version;

		add_action( 'init', array( $this, 'register_swiftype_geolocation_shortcodes' ) );
		

		add_action( "wp_ajax_search_swiftype_geo", array( $this, "ajax_search_swiftype_geo" ) );
		add_action( "wp_ajax_nopriv_search_swiftype_geo", array( $this, "ajax_search_swiftype_geo" ) );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->name, plugin_dir_url( __FILE__ ) . 'css/swiftype-search-geolocation-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * To-do: Need to get rid of this gracefully, deleting it breaks plugin.  
	 * 
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		
	}

	/**
	 * Method for verifying Latitude and Longitude are set properly.
	 * @param float $lat 
	 * @param float $long 
	 * @return bool
	 */
	public function verify_location($lat, $long){
		if( empty($lat) || empty($long) || !is_numeric($lat) || !is_numeric($long) ){
			return false; 
		}else{
			return true;
		}
	}
	/**
	 * Call the Swiftype API and parse the data for google maps. 
	 * 
	 * Parse the shortcode data and ping the swiftype API for all posts within 
	 * a specified geolocation range.  
	 * 
	 * @param array $atts 
	 * @return string
	 */
	public function render_swiftype_geolocation_shortcode( $atts ) {
		
		extract( shortcode_atts(array(
			'lat' 			=> '',
			'long' 			=> '', 
			'zoom' 			=> '5', 
			'distance'		=> '50',
			'search_form'	=> true,
			'post_types'	=> get_option( 'swiftype-search-geolocation-post-types' ), 

		), $atts) );

		// do a quick check to make sure lat & long is usable
		if( !$this->verify_location($lat, $long) ){
			return; 
		}

		// Call the JS needed for the shortcode
		$this->render_swiftype_geolocation_shortcode_js(); 

		// post types need to be in an array for Swiftype. 
		$processed_post_types = explode(',', $post_types);

		// To-do: make km a wp setting and allow switch between "km" and "mi"
		$distance		= $distance."km";	
		
		$markers = $this->geo_search_swiftype($lat, $long, $distance, $processed_post_types); 

		// prep data for localization
		$data = array(
			"geo_lat" 			=> $lat,
			"geo_long" 			=> $long,
			"map_zoom"			=> $zoom,
			"search_form"		=> $search_form,
			"swiftype_markers"	=> $markers,
			"distance"			=> $distance,
			"post_types"		=> $post_types
		); 
		//output swiftype data to be used by geo scripts. 
		wp_localize_script( 'swiftype_geo_js', 'swiftype_geo_data', $data );
		
		//google maps container. 
		$output = '<div id="swiftype-map"></div>';
		return $output; 
	}

	/**
	 * Queries Swiftype API for searchå results
	 * 
	 * Searches for indexed posts using swiftype geo filter to return posts
	 * within specified distance from given latitude and longitude. 
	 * 
	 * @param float $lat 
	 * @param float $long 
	 * @param int $distance 
	 * @param string $post_types 
	 * @return mixed
	 */
	public function geo_search_swiftype($lat, $long, $distance, $post_types){
		$api_key		= get_option( 'swiftype_api_key' );
		$engine_slug	= get_option( 'swiftype_engine_slug' );

		//instantiate Swiftype client to pass parameters to swiftype API
		$client = new SwiftypeClient();
		$client->set_api_key( $api_key );

		// pass an empty query string for now as it is expected. To-do: allow string search with geo search. 
		$query_string = '';
		$document_type_slug = 'posts'; 
		$params = array();
		$params['filters[posts][object_type]'] = $post_types; 

		// filter to change which fields are returned. To-Do: enable customization of infobox
		$params['fetch_fields[posts]'] = apply_filters('swiftype_geolocation_return_fields', array( 'geolocation','title','external_id','url' ) );
		
		$params['filters[posts][geolocation]'] = array(
			"type" => "distance",
			"max" => $distance,
			"lat" => $lat,
			"lon" => $long 
		);
	
		$swiftype_result = $client->search( $engine_slug, $document_type_slug, $query_string, $params );
		// print_r($swiftype_result); 
		return $swiftype_result['records']['posts'];

	}

	public function ajax_search_swiftype_geo(){
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : ""; 

		$lat = isset( $_POST['loc_lat'] ) ? $_POST['loc_lat'] : "";
		$long = isset( $_POST['loc_long'] ) ? $_POST['loc_long'] : "";
		$distance = isset( $_POST['distance'] ) ? $_POST['distance'] : "50km";
		$post_types = isset( $_POST['post_types'] ) ? $_POST['post_types'] : get_option( 'swiftype-search-geolocation-post-types' );
		$processed_post_types = explode(',', $post_types);

		if( wp_verify_nonce( $nonce, 'swiftype-geo-nonce' ) ){
			if( $this->verify_location($lat, $long) ){
				$markers = $this->geo_search_swiftype($lat, $long, $distance, $processed_post_types); 
				wp_send_json_success( $markers );
			}else{
				wp_send_json_error( "The location was not set properly." );
			}
		}else{
			wp_send_json_error( "There is monkey business going on." );
		}
	}

	public function register_swiftype_geolocation_shortcodes(){
		add_shortcode( 'swiftype-geo-location', array( $this, 'render_swiftype_geolocation_shortcode' ) );
	}
	/**
	 * Function enqueues javascript required for shortcode
	 * 
	 * This function is called only when the shortcode 
	 * is on the page to reduce unnecessary calls
	 * 
	 */
	public function render_swiftype_geolocation_shortcode_js(){
		wp_enqueue_script( 'swiftype_geo_js', plugin_dir_url( __FILE__ ) . 'js/swiftype-search-geolocation-public.js', array( 'jquery', 'wp-util' ), $this->version, true );
		wp_enqueue_script( 'Swiftype-googlemaps-JS', 'https://maps.googleapis.com/maps/api/js', null, '0.1', FALSE );

		// Print out a nonce so we can verify this request.
		wp_localize_script( "swiftype_geo_js", "swiftype_geo_search_nonce", wp_create_nonce( "swiftype-geo-nonce" ) );

		add_action( "wp_footer", array( $this, "render_shortcode_form" ) ); 
	}
	/**
	 * Render form template used in shortcode
	 * 
	 * Form needed for geo-search is in JS template that is rendered 
	 * in footer in $this->render_swiftype_geolocation_shortcode_js()
	 * 
	 */
	public function render_shortcode_form(){
		include( plugin_dir_path( __FILE__ ) . 'partials/swiftype-search-geolocation-public-form-template.php');
	} 
}
