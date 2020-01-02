<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              lincolnlemos.com
 * @since             1.0.0
 * @package           Jetpack_To_Wp_Ulike
 *
 * @wordpress-plugin
 * Plugin Name:       Jetpack To WP ULike
 * Plugin URI:        https://github.com/lincolnlemos/jetpack-to-wp-ulike
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.2
 * Author:            Lincoln Lemos
 * Author URI:        lincolnlemos.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       jetpack-to-wp-ulike
 * GitHub Plugin URI: https://github.com/lincolnlemos/jetpack-to-wp-ulike
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'JETPACK_TO_WP_ULIKE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-jetpack-to-wp-ulike-activator.php
 */
function activate_jetpack_to_wp_ulike() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jetpack-to-wp-ulike-activator.php';
	Jetpack_To_Wp_Ulike_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-jetpack-to-wp-ulike-deactivator.php
 */
function deactivate_jetpack_to_wp_ulike() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jetpack-to-wp-ulike-deactivator.php';
	Jetpack_To_Wp_Ulike_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_jetpack_to_wp_ulike' );
register_deactivation_hook( __FILE__, 'deactivate_jetpack_to_wp_ulike' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-jetpack-to-wp-ulike.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_jetpack_to_wp_ulike() {

	$plugin = new Jetpack_To_Wp_Ulike();
	$plugin->run();

}
run_jetpack_to_wp_ulike();
