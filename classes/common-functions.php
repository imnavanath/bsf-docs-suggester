<?php
/**
 * Doc Suggester supportive Common function.
 *
 * @package DOC_SUGGESTER
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Validate if BSF Docs plugin activate
 * 
 * @package DOC_SUGGESTER
 * @since 1.0.0
 */
if ( ! function_exists( 'is_bsf_docs_activate' ) ) {

	function is_bsf_docs_activate() {

		if ( defined( 'BSF_DOCS_BASE_FILE' ) ) {
			return true;
		}

		return false;
	}
}

/**
 * Get Docs Post Type
 * 
 * @package DOC_SUGGESTER
 * @since 1.0.0
 */
if ( ! function_exists( 'bsf_docs_post_type' ) ) {

	function bsf_docs_post_type() {

		if ( defined( 'BSF_DOCS_POST_TYPE' ) ) {
			return BSF_DOCS_POST_TYPE;
		}

		return 'docs';
	}
}
