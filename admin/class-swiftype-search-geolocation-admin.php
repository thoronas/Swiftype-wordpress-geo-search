<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class Swiftype_Search_Geolocation_Admin {

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
	 * @var      string    $name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $name, $version ) {

		$this->name = $name;
		$this->version = $version;

		add_action('add_meta_boxes', array( $this, 'swiftype_geolocation_meta_box_register' ) );

		add_action( 'save_post', array( $this, 'swiftype_geolocation_save_post_meta' ) );

		add_filter( 'swiftype_document_builder', array( $this, 'swiftype_geolocation_document_data' ), 10, 2 );

		// actions for adding menu pages. 
		add_action( 'admin_menu', array( $this, 'menu_extension' ), 100 );
		add_action( 'admin_init', array( $this, 'register_settings' ), 100 );

		add_action('media_buttons', array( $this, 'add_geo_location_button' ), 15);

		add_action('admin_footer', array( $this, 'output_modal_template' ) );
	}
	/**
	 * Added submenu page to the Swiftype Panel in the admin. 
	 * @return type
	 */
	public static function menu_extension(){

		// @see _add_post_type_submenus()
		// @see wp-admin/menu.php
		add_submenu_page(
			'swiftype', // parent slug
			__( 'geo-location', 'swiftype' ), // page title
			__( 'Geo-location', 'swiftype' ), // menu title
			'manage_options', // capability
			'swiftype_search_geolocation', // menu slug
			array( __CLASS__, 'page_geolocation' ) // function
		);
	}
	/* 
	 * Register Settings for Geolocation panel
	 */
	public function register_settings() {
		add_settings_section(
			"swiftype-search-geolocation-settings", // Id
			__("Swiftype Geo-location", "swiftype"), // Title
			'__return_false', // callback function - returning false accepted
			'swiftype_search_geolocation' // page added to. 
		);

		add_settings_field( 
			'swiftype-search-geolocation-post-types', 
			__('Swiftype Geo-Location Post Types', 'swiftype'), 
			array( $this, 'setting_geolocation_post_types'), 
			'swiftype_search_geolocation', 
			'swiftype-search-geolocation-settings', 
			array( 
				'description' => __('Choose which Post Types to enable Geo-Location on.', 'swiftype'), 
			) 
		);

		register_setting( 'swiftype_geo_location_settings', 'swiftype-search-geolocation-post-types' );

	}
	/* 
	 * Render settings field to select post types to use geolocation
	 */
	public function setting_geolocation_post_types( $args ){
		$post_types = get_post_types( array(
			'public' => true,
		), 'objects' );

		if ( $post_types ) : ?>

			<ul style="margin: 0;">

				<?php foreach ( $post_types as $name => $type ) : ?>

					<li>
						<label for="swiftype-geolocation-type-<?php echo $name; ?>">
							<input id="swiftype-geolocation-type-<?php echo $name; ?>" name="swiftype-search-geolocation-post-types[]" value="<?php echo $name; ?>" type="checkbox" <?php checked( self::is_post_type_active( $name ) ); ?> />
							<?php echo $type->labels->singular_name; ?>
						</label>
					</li>

				<?php endforeach; ?>

			</ul>

			<?php if ( isset( $args['description'] ) ) : ?>

				<p class="description"><?php echo $args['description']; ?></p>

			<?php endif;

		endif;
	}
	/* 
	 * Return the post types using geo location services
	 */ 
	public static function get_swiftype_geolocated_post_types(){
		$active = get_option( 'swiftype-search-geolocation-post-types' ); 
		return $active; 
	}

	public static function is_post_type_active( $post_type ){
		$activated_post_types = self::get_swiftype_geolocated_post_types();

		return in_array($post_type, $activated_post_types); 
	}

	/**
	 * Add media button
	 */
	public function add_geo_location_button(){
	    echo '<a href="#" id="add-swiftype-geolocation" class="button">Add Swiftype Map</a>';
	}

	/**
	 * Output javascript modal JS template 
	 * @return type
	 */
	public function output_modal_template(){
		include_once plugin_dir_path( __FILE__ ) . '/partials/modal-template.php'; 
	}

	/**
	 * Render geolocation page
	 */
	public static function page_geolocation() {
		include_once plugin_dir_path( __FILE__ ) . '/partials/swiftype-search-geolocation-admin-page.php';
	}
	/* 
	 * Register Post Meta Box for Swiftype Geolocation field
	 */ 
	public function swiftype_geolocation_meta_box_register( $post_type ){
	    $new_post_types = get_option( 'swiftype-search-geolocation-post-types' );
	    if(  in_array( $post_type, $new_post_types ) ){
		    $ID = "Swiftype-geo-location-meta-box"; 
		    $title = "Swiftype Map"; 
		    add_meta_box($ID, $title, array($this, 'render'), $post_type);   
	    } 
	}

	/* 
	 * Render function for custom meta box. Includes php file with html data. 
	 */
	public function render( $post ) {
		include plugin_dir_path( __FILE__ ).'/partials/swiftype-search-geolocation-map-meta-box.php';
	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->name, plugin_dir_url( __FILE__ ) . 'css/swiftype-search-geolocation-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->name, plugin_dir_url( __FILE__ ) . 'js/swiftype-search-geolocation-admin.js', array( 'jquery' ), $this->version, FALSE );
	    wp_enqueue_script( 'Swiftype-googlemaps-JS', 'https://maps.googleapis.com/maps/api/js', null, '0.1', FALSE );

	}

	/* 
	 * Insert geo-location data as post meta. To be used in swiftype document indexing. 
	 */ 

	public function swiftype_geolocation_save_post_meta(  $post_id ){
	    
	    // Checks save status
	    $is_autosave = wp_is_post_autosave( $post_id );
	    $is_revision = wp_is_post_revision( $post_id );
	 
	    // Exits script depending on save status
	    if ( $is_autosave || $is_revision ) {
	        return;
	    }
	     if( isset( $_POST[ 'swiftype-location-address' ] ) ) {
	        update_post_meta( $post_id, 'swiftype-location-address', sanitize_text_field( $_POST[ 'swiftype-location-address' ] ) );
	    }
	    if( isset( $_POST[ 'swiftype-location-lat' ] ) ) {
	        update_post_meta( $post_id, 'swiftype-location-lat', sanitize_text_field( $_POST[ 'swiftype-location-lat' ] ) );
	    }
	    if( isset( $_POST[ 'swiftype-location-long' ] ) ) {
	        update_post_meta( $post_id, 'swiftype-location-long', sanitize_text_field( $_POST[ 'swiftype-location-long' ] ) );
	    }

	}

	/* 
	 * Add geolocation meta data to document stored in swiftype. 
	 * This can then be queried by [insert function name here]
	 */

	public function swiftype_geolocation_document_data( $document, $post ) {
	    
	    $lat = get_post_meta($post->ID, 'swiftype-location-lat', true); 
	    $long = get_post_meta($post->ID, 'swiftype-location-long', true);
	    // add data to document if present
	    If($lat && $long){
	        $formatted_location = array( 'lat' => $lat, 'lon' => $long); 
	        $document['fields'][] = array( 
	            'name' => 'geolocation',
	            'type' => 'location',
	            'value' => $formatted_location
	        );

	    }

	    return $document;
	}

}
