<?php
/**
 * The template for single doc page - Related Doc Results
 *
 * @author Brainstormforce
 * @package Documentation/SinglePage
 */

$post_id = get_the_ID();

$related_docs_meta = get_post_meta( $post_id, 'bsf_related_docs' );

if( ! empty( $related_docs_meta ) ) { ?>
    <div class="related-docs-container">
        <div class="related-docs-wrapper">
        <h3 class="related-docs-section-title"> Related Articles - </h3>
            <ul>
                <?php
                    foreach ( $related_docs_meta as $key => $value ) {
                        foreach ( $value as $doc_id ) {
                            $doc_link = get_the_permalink( $doc_id );
                            $doc_title = get_the_title( $doc_id );

                            $message = '';
                            $message .= '<li class="related-doc-item" id="' .$doc_id. '"';
                            $message .= '<span class="related-doc-title"> <a href=' .$doc_link. ' class="related-doc-link">';
                            $message .= $doc_title;
                            $message .= '</a> </span> </li>';

                            echo $message;
                        }
                    }
                ?>
            </ul>
        </div>
    </div>
    <?php
}
    echo '<div class="text-below-single-docs">Not the solution you are looking for? Check other <a href="' .get_site_url(). '/docs">articles</a>, or open a <a href="'. get_the_permalink(25183) .'">support ticket</a>.</div>';
