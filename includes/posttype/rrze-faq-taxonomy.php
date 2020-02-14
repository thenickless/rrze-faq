<?php

namespace RRZE\Glossar\Server;

global $tax;
$tax = [
    [ 
        'name' => 'glossary_category',
        'label' => __('Glossary', 'rrze-faq'),
        'slug' => 'glossaries',
        'rest_base' => 'glossary_category',
        'labels' => array(
            'singular_name' => __('Glossary', 'rrze-faq'),
            'add_new' => __('Add new glossary', 'rrze-faq'),
            'add_new_item' => __('Add new glossary', 'rrze-faq'),
            'new_item' => __('New glossary', 'rrze-faq'),
            'view_item' => __('Show glossary', 'rrze-faq'),
            'view_items' => __('Show glossaries', 'rrze-faq'),
            'search_items' => __('Search glossaries', 'rrze-faq'),
            'not_found' => __('No glossary found', 'rrze-faq'),
            'all_items' => __('All glossaries', 'rrze-faq'),
            'separate_items_with_commas' => __('Separate glossaries with commas', 'rrze-faq'),
            'choose_from_most_used' => __('Choose from the most used glossaries', 'rrze-faq'),
            'edit_item' => __('Edit glossary', 'rrze-faq'),
            'update_item' => __('Update glossary', 'rrze-faq')
        )
    ],
    [ 
        'name' => 'glossary_tag',
        'label' => __('Tags', 'rrze-faq'),
        'slug' => 'tags',
        'rest_base' => 'glossary_tag',
        'labels' => array(
            'singular_name' => __('Tag', 'rrze-faq'),
            'add_new' => __('Add new tag', 'rrze-faq'),
            'add_new_item' => __('Add new tag', 'rrze-faq'),
            'new_item' => __('New tag', 'rrze-faq'),
            'view_item' => __('Show tag', 'rrze-faq'),
            'view_items' => __('Show tags', 'rrze-faq'),
            'search_items' => __('Search tags', 'rrze-faq'),
            'not_found' => __('No tag found', 'rrze-faq'),
            'all_items' => __('All tags', 'rrze-faq'),
            'separate_items_with_commas' => __('Separate tags with commas', 'rrze-faq'),
            'choose_from_most_used' => __('Choose from the most used tags', 'rrze-faq'),
            'edit_item' => __('Edit tag', 'rrze-faq'),
            'update_item' => __('Update tag', 'rrze-faq')
        )
    ],
];


function fau_glossary_taxonomy() {
    global $tax;

    foreach ($tax as $t){
        register_taxonomy(
            $t['name'],  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
            'glossary',   		 //post type name
            array(
                'hierarchical'	=> false,
                'label' 		=> $t['label'], //Display name
                'labels'        => $t['labels'],
                'show_admin_column' => true,
                'query_var' 	=> true,
                'rewrite'		=> array(
                       'slug'	    => $t['slug'], // This controls the base slug that will display before each term
                       'with_front'	    => false // Don't display the category base before
                ),
                'show_in_rest'       => true,
                'rest_base'          => $t['rest_base'],
                'rest_controller_class' => 'WP_REST_Terms_Controller'
            )
        );
    }
}

add_action( 'init', 'RRZE\Glossar\Server\fau_glossary_taxonomy');