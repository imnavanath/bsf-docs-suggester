<?php
/**
 * Plugin Name: BSF Doc Suggester
 * Author: Brainstorm Force, Navanath Bhosale
 * Author URI: https://brainstormforce.com
 * Version: 1.0.0
 * Description: This plugin provides priviledge to add suggested docs on the frontend.
 * Text Domain: doc-suggester
 *
 * @package DOC_SUGGESTER
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Set constants.
 */
define( 'DOC_SUGGESTER_FILE', __FILE__ );
define( 'DOC_SUGGESTER_VER', '1.0.0' );
define( 'DOC_SUGGESTER_PLUGIN_NAME', 'Docs Suggester' );
define( 'DOC_SUGGESTER_DIR', plugin_dir_path( __FILE__ ) );
define( 'DOC_SUGGESTER_URL', plugins_url( '/', __FILE__ ) );
define( 'DOC_SUGGESTER_ROOT', dirname( plugin_basename( __FILE__ ) ) );

if ( ! function_exists( 'render_doc_suggester_setup' ) ) :

	/**
	 * Doc Suggester Setup
	 *
	 * @since 1.0.0
	 */
	function render_doc_suggester_setup() {
		require_once DOC_SUGGESTER_DIR . 'classes/class-doc-suggester-loader.php';
	}

	add_action( 'plugins_loaded', 'render_doc_suggester_setup' );

endif;
