<?php

namespace RRZE\FAQ\Server;

function rrze_faq_post_type() {	

    $labels = array(
            'name'                => _x( 'FAQ', 'FAQ, synonym or glossary entries', 'rrze-faq' ),
            'singular_name'       => _x( 'FAQ', 'Single FAQ, synonym or glossary ', 'rrze-faq' ),
            'menu_name'           => __( 'FAQ', 'rrze-faq' ),
            'add_new'             => __( 'Add FAQ', 'rrze-faq' ),
            'add_new_item'        => __( 'Add new FAQ', 'rrze-faq' ),
            'edit_item'           => __( 'Edit FAQ', 'rrze-faq' ),
            'all_items'           => __( 'All FAQ', 'rrze-faq' ),
            'search_items'        => __( 'Search FAQ', 'rrze-faq' ),
    );

    $rewrite = array(
            'slug'                => 'faq',
            'with_front'          => true,
            'pages'               => true,
            'feeds'               => true,
    );

    $args = array(
            'label'               => __( 'FAQ', 'rrze-faq' ),
            'description'         => __( 'FAQ informations', 'rrze-faq' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'editor' ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => false,
            'show_in_admin_bar'   => true,
            'menu_icon'		  => 'dashicons-editor-help',
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => true,
            'publicly_queryable'  => true,
            'query_var'           => 'faq',
            'rewrite'             => $rewrite,
            'show_in_rest'        => true,
            'rest_base'           => 'faq',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
    );

    register_post_type( 'faq', $args );

}

add_action( 'init', 'RRZE\FAQ\Server\rrze_faq_post_type', 0 );


// make API deliver source and lang for FAQ
function create_api_posts_meta_field() {
    $fields = array( 'faq', 'faq_category', 'faq_tag' );
    foreach( $fields as $field ){
        register_rest_field( $field, 'post-meta-fields', array(
                'get_callback'    => 'RRZE\FAQ\Server\get_post_meta_for_api',
                'schema'          => null,
             )
         );
    }        
}
 
function get_post_meta_for_api( $object ) {
    return get_post_meta( $object['id'] );
}

add_action( 'rest_api_init', 'RRZE\FAQ\Server\create_api_posts_meta_field' );


function set_term_source( $post_ID, $terms, $term_ids ){
    foreach( $term_ids as $term_id ){
        add_term_meta( $term_id, 'source', 'website', TRUE );
    }
}

function set_source( $post_ID ){
    add_post_meta( $post_ID, 'source', 'website', TRUE );
}

// set default source for faq, faq_tag and faq_category
add_action('publish_faq', 'RRZE\FAQ\Server\set_source', 10, 1 );
add_action('set_object_terms', 'RRZE\FAQ\Server\set_term_source', 10, 3 );
