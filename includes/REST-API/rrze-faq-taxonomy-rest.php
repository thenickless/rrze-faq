<?php

namespace RRZE\Glossar\Server;

global $taxonomy_names;
$taxonomy_names = array( 'glossary_category', 'glossary_tag' );


function rrze_faq_taxonomy_rest_support() {
    global $wp_taxonomies;
    global $taxonomy_names;

    foreach ($taxonomy_names as $taxonomy_name){
        if ( isset( $wp_taxonomies[ $taxonomy_name ] ) ) {
            $wp_taxonomies[ $taxonomy_name ]->show_in_rest = true;
            $wp_taxonomies[ $taxonomy_name ]->rest_base = $taxonomy_name;
            $wp_taxonomies[ $taxonomy_name ]->rest_controller_class = 'WP_REST_Terms_Controller';
        }
    }
}

add_action( 'init', 'RRZE\Glossar\Server\rrze_faq_taxonomy_rest_support', 25 );

function create_api_posts_meta_field() {
    global $taxonomy_names;

    foreach ($taxonomy_names as $taxonomy_name){
        register_rest_field( $taxonomy_name, 'name', array(
            'get_callback'    => 'RRZE\Glossar\Server\get_post_meta_for_api',
            'schema'          => null,
            )
        );
    }
}
 
function get_post_meta_for_api( $object ) {
    //get the id of the post object array
    $post_id = $object['id'];
 
    //return the post meta
    return get_post_meta( $post_id );
}

add_action( 'rest_api_init', 'RRZE\Glossar\Server\create_api_posts_meta_field' );
