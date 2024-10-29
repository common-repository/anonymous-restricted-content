<?php

/**
 * @link              https://wordpress.org/plugins/anonymous-restricted-content/
 * @since             1.0.0
 * @package           ARC
 *
 * *
 * @wordpress-plugin
 * Plugin Name:       Anonymous Restricted Content
 * Plugin URI:        https://wordpress.org/plugins/anonymous-restricted-content/
 * Description:       Restrict access to selected content (posts/pages/categories) for NOT LOGGED IN users.
 * Version:           1.6.5
 * Author:            Taras Sych
 * Author URI:        https://wordpress.org/plugins/anonymous-restricted-content/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       anonymous-restricted-content
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'ARC_VERSION', '1.6.5' );
define( 'ARC_PACKAGE_NAME', 'ARC' );

/**
 * The code that runs during plugin activation.
 */
//function activate_plugin() {
//	require_once plugin_dir_path( __FILE__ ) . 'includes/class-arc-activator.php';
//	ARC_Activator::activate();
//}

/**
 * The code that runs during plugin deactivation.
 */
function arc_deactivate_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-arc-deactivator.php';
	ARC_Deactivator::deactivate();
}

//register_activation_hook( __FILE__, 'activate_plugin' );
register_deactivation_hook( __FILE__, 'arc_deactivate_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-arc.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function arc_run_plugin() {

	$plugin = new ARC();
	$plugin->run();

}
arc_run_plugin();
