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