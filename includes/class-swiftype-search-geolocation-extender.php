<?php 
/**
 * Extends the Swiftype plugin class to allow for geolocation search
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 */
if ( class_exists( 'SwiftypePlugin' ) && !class_exists( 'SwiftypeGeolocationPlugin' ) ) {
    class SwiftypeGeolocationPlugin extends SwiftypePlugin {
        
        private $client = NULL;
        private $document_type_slug = 'posts';

        private $api_key = NULL;
        private $engine_slug = NULL;
        private $engine_name = NULL;
        private $engine_key = NULL;

        public function __construct() {
            $this->api_key = get_option( 'swiftype_api_key' );
            $this->engine_slug = get_option( 'swiftype_engine_slug' );
            $this->engine_key = get_option( 'swiftype_engine_key' );

            $this->client = new SwiftypeClient();
            $this->client->set_api_key( $this->api_key );  
        }

        /* 
         * Custom function to get posts from Swiftype based on geolocation
         * 
         */
        public function get_geolocated_posts_from_swiftype( $query, $lat, $long, $zoom, $max) {

            if($this->engine_slug && strlen( $this->engine_slug ) > 0){
                
                $query_string = $query;
                $params = array();
                $params['fetch_fields[posts]'] = apply_filters('swiftype_geolocation_return_fields', array( 'geolocation','title','external_id','url' ) );

                $params['filters[posts][geolocation]'] = array(
                    "type" => "distance",
                    "max" => $max,
                    "lat" => $lat,
                    "lon" => $long 
                );
                try {
                    $this->results = $this->client->search( $this->engine_slug, $this->document_type_slug, $query_string, $params );
                } catch( SwiftypeError $e ) {
                    $this->results = NULL;
                    $this->search_successful = false;
                }

                if( ! isset( $this->results ) ) {
                    $this->search_successful = false;
                    return;
                }
                $records = $this->results['records']['posts'];

                return $records; 
            } 
        }
    }
}