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
		// enqueue scripts only when shortcode is called. 
		wp_enqueue_script( 'swiftype_geo_js', plugin_dir_url( __FILE__ ) . 'js/swiftype-search-geolocation-public.js', array( 'jquery' ), $this->version, FALSE );
	    wp_enqueue_script( 'Swiftype-googlemaps-JS', 'https://maps.googleapis.com/maps/api/js', null, '0.1', FALSE );
	    
	    extract(shortcode_atts(array(
	        'lat' 	=> '37.090240',
	        'long' 	=> '-95.712891', 
	        'zoom' 	=> '5', 
	        'max'	=> '50km'
	    ), $atts));

		$api_key     = get_option( 'swiftype_api_key' );
		$engine_slug = get_option( 'swiftype_engine_slug' );
		
		//instantiate Swiftype client to pass parameters to swiftype API
		$client      = new SwiftypeClient();
		$client->set_api_key( $api_key );

        $query_string = '';
        $document_type_slug = 'posts'; 
        $params = array();
        $params['fetch_fields[posts]'] = apply_filters('swiftype_geolocation_return_fields', array( 'geolocation','title','external_id','url' ) );
        
        $params['filters[posts][geolocation]'] = array(
            "type" => "distance",
            "max" => $max,
            "lat" => $lat,
            "lon" => $long 
        );
	
		$swiftype_result = $client->search( $engine_slug, $document_type_slug, $query_string, $params );
		$markers = $swiftype_result['records']['posts'];
	    
	    // prep data for localization
	    $data = array(
	    	"geo_lat" 			=> $lat,
	    	"geo_long" 			=> $long,
	    	"swiftype_markers"	=> $markers
    	); 
	    //output swiftype data to be used by geo scripts. 
    	wp_localize_script( 'swiftype_geo_js', 'swiftype_geo_data', $data );
	    
	    //google maps container. 
	    $output = '<div id="swiftype-map"></div>';
	    return $output; 
	}


	public function register_swiftype_geolocation_shortcodes(){
	    add_shortcode( 'swiftype-geo-location', array( $this, 'render_swiftype_geolocation_shortcode' ) );
	}


}
