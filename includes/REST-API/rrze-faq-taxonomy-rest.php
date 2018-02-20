<?php

namespace RRZE\Glossar\Server;

function rrze_faq_taxonomy_rest_support() {
    global $wp_taxonomies;

    //be sure to set this to the name of your taxonomy!
    $taxonomy_name = 'glossary_category';

    if ( isset( $wp_taxonomies[ $taxonomy_name ] ) ) {
            $wp_taxonomies[ $taxonomy_name ]->show_in_rest = true;
            $wp_taxonomies[ $taxonomy_name ]->rest_base = $taxonomy_name;
            $wp_taxonomies[ $taxonomy_name ]->rest_controller_class = 'WP_REST_Terms_Controller';
    }
}

add_action( 'init', 'RRZE\Glossar\Server\rrze_faq_taxonomy_rest_support', 25 );

add_action( 'rest_api_init', 'RRZE\Glossar\Server\create_api_posts_meta_field' );
 
function create_api_posts_meta_field() {
 
    // register_rest_field ( 'name-of-post-type', 'name-of-field-to-return', array-of-callbacks-and-schema() )
    register_rest_field( 'glossary_category', 'name', array(
           'get_callback'    => 'RRZE\Glossar\Server\get_post_meta_for_api',
           'schema'          => null,
        )
    );
}
 
function get_post_meta_for_api( $object ) {
    //get the id of the post object array
    $post_id = $object['id'];
 
    //return the post meta
    return get_post_meta( $post_id );
}