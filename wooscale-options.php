<?php
/**
 * Plugin Name: WooScale Options
 * Description: An extension to WooCommerce for adding custom options and accessories to products.
 * Version: 1.0.0
 * Author: Sivulabra Oy
 * Author URI: https://sivulabra.fi
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Define plugin version.
 */
define( 'WOOSCALE_OPTIONS_VERSION', '1.0.0' );

/**
 * Define plugin path.
 */
define( 'WOOSCALE_OPTIONS_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Define plugin URL.
 */
define( 'WOOSCALE_OPTIONS_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );

// Bootstrap the plugin
if ( ! class_exists( 'WooScaleOptions' ) ) {
	require_once __DIR__ . '/src/WooScaleOptions.php';
}

if ( ! function_exists( 'wooscale_options_init' ) ) {

	/**
	 * Init the main class.
	 */
	function init_wooscale_options() {
		return new \WooScaleOptions();
	}
}
add_action( 'plugins_loaded', 'init_wooscale_options', 100 );