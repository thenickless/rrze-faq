<?php

namespace RRZE\FAQ;

defined( 'ABSPATH' ) || exit;

/**
 * REST API for "faq"
 */
class RESTAPI {


    public function __construct() {
        add_action( 'rest_api_init', [$this, 'createPostMeta'] );
        add_action( 'rest_api_init', [$this, 'createTaxDetails'] );
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
            ));
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




    public function getFaqCategories( $post ) {
        $cats = wp_get_post_terms( $post['id'], 'faq_category' );
        foreach ( $cats as $cat ){
            $cat->parent = get_term_parents_list( $cat->term_id, 'faq_category', array( 'format' => 'name', 'link' => FALSE, 'separator' => ',', 'inclusive' => TRUE ) );
        }
        return $cats;
    }

    public function getFaqTags( $post ) {
        return wp_get_post_terms( $post['id'], 'faq_tag' );
    }


    public function createTaxDetails() {
        register_rest_field( 'faq',
            'faq_category',
            array(
                'get_callback'    => [$this, 'getFaqCategories'],
                'update_callback'   => null,
                'schema'            => null,
             )
        );
        register_rest_field( 'faq',
            'faq_tag',
            array(
                'get_callback'    => [$this, 'getFaqTags'],
                'update_callback'   => null,
                'schema'            => null,
             )
        );
    }
        

}
