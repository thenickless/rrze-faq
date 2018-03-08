<?php

namespace RRZE\Glossar\Server;

function faq_columns( $columns ) {

	$columns = array(
            'title'         => __( 'Title', 'rrze-faq' ),
            'category'      => __( 'Category', 'rrze-faq' ),
            'date'          => __( 'Datum', 'rrze-faq' ),
	);

	return $columns;
}

add_filter( 'manage_edit-faq_columns', 'RRZE\Glossar\Server\faq_columns') ;

function show_faq_columns($column_name) {
    global $post;
    switch ($column_name) {
        case 'title':
            $title = get_post_meta($post->ID, 'title', true);
            echo $title;
            break;
         case 'glossary_category':
            $category = get_the_term_list($post->ID, 'glossary_category');
            echo $category;
            break;
    }
}

add_action('manage_posts_custom_column',  'RRZE\Glossar\Server\show_faq_columns');

function faq_sortable_columns() {
  return array(
    'glossary_category'   => 'glossary_category',
  );
}

add_filter( 'manage_edit-faq_sortable_columns', 'RRZE\Glossar\Server\fag_sortable_columns' );
