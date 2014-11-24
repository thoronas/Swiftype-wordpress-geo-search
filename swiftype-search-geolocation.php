<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             1.0.0
 * @package           Swiftype_Search_Geolocation
 *
 * @wordpress-plugin
 * Plugin Name:       Swiftype Search Geolocation Add-On
 * Plugin URI:        https://github.com/thoronas/Swiftype-wordpress-geo-search
 * Description:       Integrate geo-search into Swiftype functionality. 
 * Version:           1.0.0
 * Author:            Flynn O'Connor
 * Author URI:        http://fsquaredesign.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * 
 * This plugin extends the Swiftype WordPress plugin. It allows users 
 * to add latitude and longitude post meta which is added to the swiftype
 * document builder. A shortcode displays a map of posts and includes a
 * search form for searching for other posts. 
 * 
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-swiftype-search-geolocation-activator.php';

/**
 * The code that runs during plugin deactivation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-swiftype-search-geolocation-deactivator.php';

/** This action is documented in includes/class-plugin-name-activator.php */
register_activation_hook( __FILE__, array( 'Swiftype_Search_Geolocation_Activator', 'activate' ) );

/** This action is documented in includes/class-plugin-name-deactivator.php */
register_activation_hook( __FILE__, array( 'Swiftype_Search_Geolocation_Deactivator', 'deactivate' ) );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-swiftype-search-geolocation.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_swiftype_search_geolocation() {

	$plugin = new Swiftype_Search_Geolocation();
	$plugin->run();

}
run_swiftype_search_geolocation();
