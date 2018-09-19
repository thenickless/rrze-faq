<?php

namespace RRZE\Glossar\Server;

function fau_glossary_post_type() {	

    $labels = array(
            'name'                => _x( 'FAQs', 'FAQ, Synonym or Glossar entries', 'rrze-faq' ),
            'singular_name'       => _x( 'FAQ', 'Single FAQ, Synonym or Glossar ', 'rrze-faq' ),
            'menu_name'           => __( 'FAQ', 'rrze-faq' ),
        /*    'parent_item_colon'   => __( 'Parent FAQs', 'rrze-faq' ),
            'all_items'           => __( 'All FAQs', 'rrze-faq' ),
            'view_item'           => __( 'Show FAQ', 'rrze-faq' ),
            'add_new_item'        => __( 'Add FAQ', 'rrze-faq' ),
            'add_new'             => __( 'New FAQ', 'rrze-faq' ),
            'edit_item'           => __( 'Edit FAQ', 'rrze-faq' ),
            'update_item'         => __( 'Update FAQ', 'rrze-faq' ),
            'search_items'        => __( 'Search FAQ', 'rrze-faq' ),
            'not_found'           => __( 'No FAQs found', 'rrze-faq' ),
            'not_found_in_trash'  => __( 'No FAQs found in trash', 'rrze-faq' ), */
    );
    $rewrite = array(
            'slug'                => 'glossary',
            'with_front'          => true,
            'pages'               => true,
            'feeds'               => true,
    );
    $args = array(
            'label'               => __( 'FAQ', 'rrze-faq' ),
            'description'         => __( 'FAQ informations', 'rrze-faq' ),
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