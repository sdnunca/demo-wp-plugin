<?php
/**
 * Plugin Name:       Demo WP Plugin
 * Plugin URI:        https://github.com/
 * Description:       A demo WordPress plugin
 * Version:           0.1.0
 * Author:            S.N
 * Author URI:        https://sorin.live
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       demo-wp-plugin
 * Domain Path:       /languages/
 */

namespace DemoWPPlugin;

define( "DEMO_WP_PLUGIN_PLUGIN_DIR", __DIR__ );
require_once( DEMO_WP_PLUGIN_PLUGIN_DIR . '/vendor/autoload.php' );

class Plugin {
	public function run() {
		add_action( 'init', [ $this, 'i18n' ] );
		add_action( 'init', [ $this, 'register_block' ] );
	}

	public function register_block() {
		register_block_type( __DIR__ . '/build' );
	}

	/**
	 * Load translations.
	 */
	public function i18n() {
		load_plugin_textdomain( 'demo-wp-plugin', false, DEMO_WP_PLUGIN_PLUGIN_DIR . '/languages' );
	}
}

$demo_wp_plugin = new Plugin();
$demo_wp_plugin->run();

// Load the rest API
RestAPI::instance()->run();
