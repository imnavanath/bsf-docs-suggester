<?php
/**
 * Initialize Doc Suggester Meta box
 *
 * @package DOC_SUGGESTER
 * @since 1.0.0
 */

if ( ! class_exists( 'Doc_Suggester_Meta_Box' ) ) :

	/**
	 * Doc Suggester Loader
	 *
	 * @since 1.0.0
	 */
	class Doc_Suggester_Meta_Box {

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
            add_action( 'wp_ajax_bsf_docs_search', array( $this, 'bsf_docs_get_posts_ajax_callback' ) );
            add_action( 'admin_init', array( $this, 'bsf_docs_suggester_add_meta_boxes' ), 1 );
            add_action( 'save_post', array( $this, 'bsf_docs_save_metaboxdata' ), 10, 2 );
        }

        /**
		 * Meta Box Adder
		 *
		 * @since 1.0.0
		 */
        public function bsf_docs_suggester_add_meta_boxes() {

            $custom_post_type = bsf_docs_post_type();

            add_meta_box( 'bsf', 'BSF Related Docs', array( $this, 'rudr_display_select2_metabox' ), $custom_post_type, 'normal', 'default' );
        }

        /**
        * Display the fields inside it
        *
        * @since 1.0.0
        */
        public function rudr_display_select2_metabox( $post_object ) {
            // do not forget about WP Nonces for security purposes
            // I decided to write all the metabox html into a variable and then echo it at the end
            $html = '';

            // always array because we have added [] to our <select> name attribute
            $appended_posts = get_post_meta( $post_object->ID, 'bsf_related_docs',true );

            /*
            * Select Posts with AJAX search
            */
            $html .= '<p><label for="bsf_related_docs"><p> Add Docs - </p></label><select id="bsf_related_docs" name="bsf_related_docs[]" multiple="multiple" style="width:99%;max-width:25em;">';

            if( $appended_posts ) {
                foreach( $appended_posts as $post_id ) {
                    $title = get_the_title( $post_id );
                    // if the post title is too long, truncate it and add "..." at the end
                    $title = ( mb_strlen( $title ) > 50 ) ? mb_substr( $title, 0, 49 ) . '...' : $title;
                    $html .=  '<option value="' . $post_id . '" selected="selected">' . $title . '</option>';
                }
            }

            $html .= '</select></p>';

            echo $html;
        }

        /**
        * Save metabox
        *
        * @since 1.0.0
        */
        public function bsf_docs_save_metaboxdata( $post_id, $post ) {
 
            if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;

            $custom_post_type = bsf_docs_post_type();

            // if post type is different from our selected one, do nothing.
            if ( $post->post_type == $custom_post_type ) {

                if( isset( $_POST['bsf_related_docs'] ) )
                    update_post_meta( $post_id, 'bsf_related_docs', $_POST['bsf_related_docs'] );
                else
                    delete_post_meta( $post_id, 'bsf_related_docs' );
            }

            return $post_id;
        }

        /**
        * AJAX Action for rendering searching posts
        *
        * @since 1.0.0
        */
        function bsf_docs_get_posts_ajax_callback() {
 
            // we will pass post IDs and titles to this array.
            $return = array();

            // you can use WP_Query, query_posts() or get_posts() here - it doesn't matter.
            $search_results = new WP_Query( array(
                's'=> $_GET['q'], // the search query.
                'post_type' => array( 'post', 'docs' ),
                'post_status' => array( 'draft', 'publish', 'pending' ),
                'ignore_sticky_posts' => 1,
                'posts_per_page' => 50 // how much to show at once.
            ));

            if( $search_results->have_posts() ) :
                while( $search_results->have_posts() ) : $search_results->the_post();	
                    // shorten the title a little.
                    $title = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;
                    $return[] = array( $search_results->post->ID, $title ); // array( Post ID, Post Title ).
                endwhile;
            endif;

            echo json_encode( $return );
            die;
        }
    }

    /**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Doc_Suggester_Meta_Box::get_instance();

endif;