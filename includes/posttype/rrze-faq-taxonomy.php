<?php

namespace RRZE\Glossar\Server;

function fau_glossary_taxonomy() {
    register_taxonomy(
        'glossary_category',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
        'glossary',   		 //post type name
        array(
            'hierarchical'	=> true,
            'label' 		=> __('Glossar-Kategorien', 'fau'),//Display name
            'show_admin_column' => true,
            'query_var' 	=> true,
            'rewrite'		=> array(
                   'slug'	    => 'glossaries', // This controls the base slug that will display before each term
                   'with_front'	    => false // Don't display the category base before
            ),
            'show_in_rest'       => true,
            'rest_base'          => 'glossary_category',
            'rest_controller_class' => 'WP_REST_Terms_Controller',
        )
    );
}

add_action( 'init', 'RRZE\Glossar\Server\fau_glossary_taxonomy');