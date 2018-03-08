<?php

namespace RRZE\Glossar\Server;

function fau_glossary_post_type() {	

    $labels = array(
            'name'                => _x( 'Faqs', 'Post Type General Name', 'rrze-faq' ),
            'singular_name'       => _x( 'Faqs', 'Post Type Singular Name', 'rrze-faq' ),
            'menu_name'           => __( 'Faq', 'rrze-faq' ),
            'parent_item_colon'   => __( 'Parent Faqs', 'rrze-faq' ),
            'all_items'           => __( 'All Faqs', 'rrze-faq' ),
            'view_item'           => __( 'Show Faq', 'rrze-faq' ),
            'add_new_item'        => __( 'Add Faq', 'rrze-faq' ),
            'add_new'             => __( 'New Faq', 'rrze-faq' ),
            'edit_item'           => __( 'Edit Faq', 'rrze-faq' ),
            'update_item'         => __( 'Update Faq', 'rrze-faq' ),
            'search_items'        => __( 'Search Faq', 'rrze-faq' ),
            'not_found'           => __( 'No Faqs found', 'rrze-faq' ),
            'not_found_in_trash'  => __( 'No Faqs found in trash', 'rrze-faq' ),
    );
    $rewrite = array(
            'slug'                => 'glossary',
            'with_front'          => true,
            'pages'               => true,
            'feeds'               => true,
    );
    $args = array(
            'label'               => __( 'faq', 'rrze-faq' ),
            'description'         => __( 'faq informations', 'rrze-faq' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'editor' ),
            'taxonomies'          => array( 'glossary_category' ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => false,
            'show_in_admin_bar'   => true,
            'menu_icon'		=> 'dashicons-editor-help',
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => true,
            'publicly_queryable'  => true,
            'query_var'           => 'glossary',
            'rewrite'             => $rewrite,
            'show_in_rest'       => true,
            'rest_base'          => 'glossary',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
    );
    register_post_type( 'glossary', $args );

}

add_action( 'init', 'RRZE\Glossar\Server\fau_glossary_post_type', 0 );