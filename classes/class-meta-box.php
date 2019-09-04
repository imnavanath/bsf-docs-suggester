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
            add_action( 'admin_init', array( $this, 'bsf_docs_suggester_add_meta_boxes' ), 1 );
            add_action( 'save_post', array( $this, 'bsf_docs_suggester_meta_box_save' ) );
        }

        /**
		 * Meta Box Adder
		 *
		 * @since 1.0.0
		 */
        function bsf_docs_suggester_add_meta_boxes() {

            $custom_post_type = bsf_docs_post_type();

            add_meta_box( 'repeatable-fields', 'BSF Related Docs Suggester', array( $this, 'bsf_docs_meta_box_display' ), $custom_post_type, 'normal', 'default');
        }

        /**
		 * Meta Box Adder
		 *
		 * @since 1.0.0
		 */
        public function bsf_docs_get_docs_options() {

            global $wpdb;

            $custom_post_type = bsf_docs_post_type();

            $results = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type = %s and post_status = 'publish'", $custom_post_type ), ARRAY_A );

            $options = array (
                '--- Please select doc from the list ---' => 'null',
            );

            foreach( $results as $index => $post ) {
                $options[ $post['post_title'] ] = $post['ID'];
            }
            
            return $options;
        }

        /**
		 * Meta Box Performer
		 *
		 * @since 1.0.0
		 */
        public function bsf_docs_meta_box_display() {

            global $post;

            $repeatable_fields = get_post_meta($post->ID, 'bsf_related_docs_option', true);
            $options = $this->bsf_docs_get_docs_options();

            wp_nonce_field( 'bsf_docs_repeatable_meta_box_nonce', 'bsf_docs_repeatable_meta_box_nonce' ); ?>
            <script type="text/javascript">
                jQuery(document).ready(function( $ ){
                    $('#bsf-docs-list').select2({
                        placeholder: 'Select a Doc'
                    });
                    $( '#add-row' ).on('click', function() {
                        var row = $( '.empty-row.screen-reader-text' ).clone(true);
                        row.removeClass( 'empty-row screen-reader-text' );
                        row.insertBefore( '#bsf-docs-repeatable-fieldset tbody>tr:last' );
                        return false;
                    });
                    $( '.remove-row' ).on('click', function() {
                        $(this).parents('tr').remove();
                        return false;
                    });
                });
            </script>

            <table id="bsf-docs-repeatable-fieldset" width="100%">
                <thead>
                    <tr>
                        <th width="40%">Name</th>
                        <th width="12%">Select</th>
                        <th width="8%"></th>
                    </tr>
                </thead>
                <tbody>
                <?php

                if ( $repeatable_fields ) :

                foreach ( $repeatable_fields as $field ) {
                    ?>
                        <tr>
                            <td><input type="text" class="widefat" name="name[]" value="<?php if( $field['name'] != '' ) echo esc_attr( $field['name'] ); ?>" /></td>

                            <td>
                                <select id="bsf-docs-list" name="select[]">
                                    <?php foreach ( $options as $label => $value ) : ?>
                                        <option value="<?php echo $value; ?>"<?php selected( $field['select'], $value ); ?>><?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>

                            <td><a class="button remove-row" href="#">Remove</a></td>
                        </tr>
                    <?php
                }
                else :
                // show a blank one
                ?>
                <tr>
                    <td><input type="text" class="widefat" name="name[]" /></td>

                    <td>
                        <select name="select[]">
                        <?php foreach ( $options as $label => $value ) : ?>
                        <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                        <?php endforeach; ?>
                        </select>
                    </td>

                    <td><a class="button remove-row" href="#">Remove</a></td>
                </tr>
                <?php endif; ?>
                
                <!-- empty hidden one for jQuery -->
                <tr class="empty-row screen-reader-text">
                    <td><input type="text" class="widefat" name="name[]" /></td>

                    <td>
                        <select name="select[]">
                        <?php foreach ( $options as $label => $value ) : ?>
                        <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                        <?php endforeach; ?>
                        </select>
                    </td>

                    <td><a class="button remove-row" href="#">Remove</a></td>
                </tr>
                </tbody>
            </table>
            
            <p><a id="add-row" class="button" href="#">Add another</a></p>
            <?php
        }

        /**
		 * Save Meta Box Performer
		 *
		 * @since 1.0.0
		 */
        function bsf_docs_suggester_meta_box_save( $post_id ) {

            if ( ! isset( $_POST['bsf_docs_repeatable_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['bsf_docs_repeatable_meta_box_nonce'], 'bsf_docs_repeatable_meta_box_nonce' ) )
                return;

            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
                return;

            if ( ! current_user_can( 'edit_post', $post_id ) )
                return;

            $old = get_post_meta( $post_id, 'bsf_related_docs_option', true );
            $new = array();
            $options = $this->bsf_docs_get_docs_options();

            $names = $_POST['name'];
            $selects = $_POST['select'];

            $count = count( $names );

            for ( $i = 0; $i < $count; $i++ ) {
                if ( $names[$i] != '' ) :
                    $new[$i]['name'] = stripslashes( strip_tags( $names[$i] ) );

                if ( in_array( $selects[$i], $options ) )
                    $new[$i]['select'] = $selects[$i];
                else
                    $new[$i]['select'] = '';
                endif;
            }

            if ( ! empty( $new ) && $new != $old )
                update_post_meta( $post_id, 'bsf_related_docs_option', $new );
            elseif ( empty($new) && $old )
                delete_post_meta( $post_id, 'bsf_related_docs_option', $old );
        }
    }

    /**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Doc_Suggester_Meta_Box::get_instance();

endif;