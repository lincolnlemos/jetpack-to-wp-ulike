<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       lincolnlemos.com
 * @since      1.0.0
 *
 * @package    Jetpack_To_Wp_Ulike
 * @subpackage Jetpack_To_Wp_Ulike/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Jetpack_To_Wp_Ulike
 * @subpackage Jetpack_To_Wp_Ulike/includes
 * @author     Lincoln Lemos <hi@lincolnlemos.com>
 */
class Jetpack_To_Wp_Ulike_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'jetpack-to-wp-ulike',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
