<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class Swiftype_Search_Geolocation_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $name    The ID of this plugin.
	 */
	private $name;

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
	 * @var      string    $name       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $name, $version ) {

		$this->name = $name;
		$this->version = $version;

		add_action( 'init', array( $this, 'register_swiftype_geolocation_shortcodes' ) );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->name, plugin_dir_url( __FILE__ ) . 'css/swiftype-search-geolocation-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		
	}

	/**
	 * Call the Swiftype API and parse the data for google maps. 
	 * 
	 * Parse the shortcode data and ping the swiftype API for all posts within 
	 * a specified geolocation range.  
	 * 
	 * @param type $atts 
	 * @param type $content 
	 * @return type
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
		if( empty($lat) || empty($long) ){
			return; 
		}

		$this->render_swiftype_geolocation_shortcode_js(); 

		// post types need to be in an array for Swiftype. 
		$post_types = explode(',', $post_types);
		$distance		= $distance."km";	
		
		$markers = $this->geo_search_swiftype($lat, $long, $distance, $post_types); 

		// prep data for localization
		$data = array(
			"geo_lat" 			=> $lat,
			"geo_long" 			=> $long,
			"map_zoom"			=> $zoom,
			"search_form"		=> $search_form,
			"swiftype_markers"	=> $markers
		); 
		//output swiftype data to be used by geo scripts. 
		wp_localize_script( 'swiftype_geo_js', 'swiftype_geo_data', $data );
		
		//google maps container. 
		$output = '<div id="swiftype-map"></div>';
		return $output; 
	}

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
		return $swiftype_result['records']['posts'];

	}

	public function register_swiftype_geolocation_shortcodes(){
		add_shortcode( 'swiftype-geo-location', array( $this, 'render_swiftype_geolocation_shortcode' ) );
	}

	public function render_swiftype_geolocation_shortcode_js(){
		// enqueue scripts only when shortcode is called. 
		wp_enqueue_script( 'swiftype_geo_js', plugin_dir_url( __FILE__ ) . 'js/swiftype-search-geolocation-public.js', array( 'jquery', 'wp-util' ), $this->version, true );
		wp_enqueue_script( 'Swiftype-googlemaps-JS', 'https://maps.googleapis.com/maps/api/js', null, '0.1', FALSE );
		add_action( "wp_footer", array( $this, "render_shortcode_form" ) ); 
	}
	public function render_shortcode_form(){
		include( plugin_dir_path( __FILE__ ) . 'partials/swiftype-search-geolocation-public-form-template.php');
	} 
}
