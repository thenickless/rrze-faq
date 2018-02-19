<?php

namespace RRZE\Glossar\Server;

function rrze_fay_post_type_rest_support() {
    global $wp_post_types;

    //be sure to set this to the name of your post type!
    $post_type_name = 'glossary';
    if( isset( $wp_post_types[ $post_type_name ] ) ) {
            $wp_post_types[$post_type_name]->show_in_rest = true;
            $wp_post_types[$post_type_name]->rest_base = $post_type_name;
            $wp_post_types[$post_type_name]->rest_controller_class = 'WP_REST_Posts_Controller';
    }

}

add_action( 'init', 'RRZE\Glossar\Server\rrze_fay_post_type_rest_support', 25 );
