<?php

namespace RRZE\FAQ\Server;


function rest_api_filter_add_filters() {
	foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
		add_filter( 'rest_' . $post_type->name . '_query', 'RRZE\FAQ\Server\rest_api_filter_add_filter_param', 10, 2 );
        }
        add_filter( 'rest_faq_category_query', 'RRZE\FAQ\Server\rest_api_filter_add_filter_param', 10, 2 );
        add_filter( 'rest_faq_tag_query', 'RRZE\FAQ\Server\rest_api_filter_add_filter_param', 10, 2 );
}

function rest_api_filter_add_filter_param( $args, $request ) {
    // Bail out if no filter parameter is set.
    if ( empty( $request['filter'] ) || ! is_array( $request['filter'] ) ) {
            return $args;
    }
    $filter = $request['filter'];
//     if ( isset( $filter['posts_per_page'] ) && ( (int) $filter['posts_per_page'] >= 1 && (int) $filter['posts_per_page'] <= 150 ) ) {
//             $args['posts_per_page'] = $filter['posts_per_page'];
//     }
    global $wp;
    $vars = apply_filters( 'query_vars', $wp->public_query_vars );
    foreach ( $vars as $var ) {
            if ( isset( $filter[ $var ] ) ) {
                    $args[ $var ] = $filter[ $var ];
            }
    }
    return $args;
}

add_action( 'rest_api_init', 'RRZE\FAQ\Server\rest_api_filter_add_filters' );

