<?php
/**
 * Initialize Doc Suggester
 *
 * @package DOC_SUGGESTER
 * @since 1.0.0
 */

if ( ! class_exists( 'Doc_Suggester_Loader' ) ) :

	/**
	 * Doc Suggester Loader
	 *
	 * @since 1.0.0
	 */
	class Doc_Suggester_Loader {

		/**
		 * Instance
		 *
		 * @access private
		 * @var object Class Instance.
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 1.0.0
		 * @return object initialized object of class.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {

            require_once DOC_SUGGESTER_DIR . 'classes/common-functions.php';
            
            if ( ! is_bsf_docs_activate() ) {
				add_action( 'admin_notices', array( $this, 'install_activate_plugin_notice' ), 1 );
				return;
			}

			/* Add Scripts */
			add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_meta_scripts', 20 );

			require_once DOC_SUGGESTER_DIR . 'classes/class-meta-box.php';
		}

		/**
		 * Render Admin Scripts.
         * 
         * @since 1.0.0
		 */
		public static function admin_meta_scripts() {

			global $pagenow;
			global $post;
	
			wp_enqueue_style( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
			wp_enqueue_script( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery') );

			// please create also an empty JS file in your theme directory and include it too
			wp_enqueue_script( 'bsfdocs', DOC_SUGGESTER_URL . 'admin/js/bsfdocs.js', array( 'jquery', 'select2' ) ); 
		}

		/**
		 * Add Admin Notice.
         * 
         * @since 1.0.0
		 */
		public function install_activate_plugin_notice() {
			printf( __( '<div class="notice notice-error is-dismissible"> <p> <strong> BSF Docs </strong> needs to be active for you to use currently installed <strong>  </strong> plugin. </p> </div>', 'doc-suggester' ), DOC_SUGGESTER_PLUGIN_NAME );
		}
	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Doc_Suggester_Loader::get_instance();

endif;
