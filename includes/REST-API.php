<?php

namespace RRZE\FAQ;

defined( 'ABSPATH' ) || exit;

/**
 * REST API for "faq"
 */
class RESTAPI {

    public function __construct() {
        add_action( 'rest_api_init', [$this, 'createPostMeta'] );
        add_action( 'rest_api_init', [$this, 'addFilters'] );
    }

    public function getPostMeta( $object ) {
        return get_post_meta( $object['id'] );
    }

    // make API deliver source and lang for FAQ
    public function createPostMeta() {
        $fields = array( 'faq', 'faq_category', 'faq_tag' );
        foreach( $fields as $field ){
            register_rest_field( $field, 'post-meta-fields', array(
                    'get_callback'    => [$this, 'getPostMeta'],
                    'schema'          => null,
                )
            );
        }        
    }

    
    public function addFilterParam( $args, $request ) {
        if ( empty( $request['filter'] ) || ! is_array( $request['filter'] ) ) {
                return $args;
        }
        global $wp;
        $filter = $request['filter'];
        $vars = apply_filters( 'query_vars', $wp->public_query_vars );
        foreach ( $vars as $var ) {
                if ( isset( $filter[ $var ] ) ) {
                        $args[ $var ] = $filter[ $var ];
                }
        }
        return $args;
    }
    
    public function addFilters() {
        add_filter( 'rest_faq_query', [$this, 'addFilterParam'], 10, 2 );
        add_filter( 'rest_faq_category_query', [$this, 'addFilterParam'], 10, 2 );
        add_filter( 'rest_faq_tag_query', [$this, 'addFilterParam'], 10, 2 );
    }

}
