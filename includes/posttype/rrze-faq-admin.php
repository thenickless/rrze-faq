<?php

namespace RRZE\Glossar\Server;
 
function fau_glossary_post_types_admin_order( $wp_query ) {
    if (is_admin()) {

        $post_type = $wp_query->query['post_type'];

        if ( $post_type == 'glossary') {

                if( ! isset($wp_query->query['orderby']))
                {
                        $wp_query->set('orderby', 'title');
                        $wp_query->set('order', 'ASC');
                }

        }
    }
}

add_filter('pre_get_posts', 'RRZE\Glossar\Server\fau_glossary_post_types_admin_order');