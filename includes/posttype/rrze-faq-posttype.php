<?php

namespace RRZE\Glossar\Server;

function fau_glossary_post_type() {	

    $labels = array(
            'name'                => _x( 'Glossar-Einträge', 'Post Type General Name', 'fau' ),
            'singular_name'       => _x( 'Glossar-Eintrag', 'Post Type Singular Name', 'fau' ),
            'menu_name'           => __( 'Glossar', 'fau' ),
            'parent_item_colon'   => __( 'Übergeordneter Glossar-Eintrag', 'fau' ),
            'all_items'           => __( 'Alle Glossar-Einträge', 'fau' ),
            'view_item'           => __( 'Eintrag anzeigen', 'fau' ),
            'add_new_item'        => __( 'Glossar-Eintrag hinzufügen', 'fau' ),
            'add_new'             => __( 'Neuer Glossar-Eintrag', 'fau' ),
            'edit_item'           => __( 'Eintrag bearbeiten', 'fau' ),
            'update_item'         => __( 'Eintrag aktualisieren', 'fau' ),
            'search_items'        => __( 'Glossar-Eintrag suchen', 'fau' ),
            'not_found'           => __( 'Keine Glossar-Einträge gefunden', 'fau' ),
            'not_found_in_trash'  => __( 'Keine Glossar-Einträge im Papierkorb gefunden', 'fau' ),
    );
    $rewrite = array(
            'slug'                => 'glossary',
            'with_front'          => true,
            'pages'               => true,
            'feeds'               => true,
    );
    $args = array(
            'label'               => __( 'glossar', 'fau' ),
            'description'         => __( 'Glossar-Informationen', 'fau' ),
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