<?php

namespace RRZE\FAQ;

defined('ABSPATH') || exit;

/**
 * REST API for "faq"
 */
class RESTAPI
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'createPostMeta']);
        add_action('rest_api_init', [$this, 'createTaxDetails']);
        add_action('rest_api_init', [$this, 'createChildren']);
        add_action('rest_api_init', [$this, 'addFilters']);
    }

    public function getPostSource($object)
    {
        return get_post_meta($object['id'], 'source', true);
    }

    public function getPostLang($object)
    {
        return get_post_meta($object['id'], 'lang', true);
    }

    public function getPostRemoteID($object)
    {
        return get_post_meta($object['id'], 'remoteID', true);
    }

    public function getPostRemoteChanged($object)
    {
        return get_post_meta($object['id'], 'remoteChanged', true);
    }

    // make API deliver source and lang for FAQ
    public function createPostMeta()
    {
        register_rest_field('faq', 'source', array(
            'get_callback' => [$this, 'getPostSource'],
            'schema' => null,
        ));
        register_rest_field('faq', 'lang', array(
            'get_callback' => [$this, 'getPostLang'],
            'schema' => null,
        ));
        register_rest_field('faq', 'remoteID', array(
            'get_callback' => [$this, 'getPostRemoteID'],
            'schema' => null,
        ));
        register_rest_field('faq', 'remoteChanged', array(
            'get_callback' => [$this, 'getPostRemoteChanged'],
            'schema' => null,
        ));
    }

    public function addFilterParam($args, $request)
    {
        if (empty($request['filter']) || !is_array($request['filter'])) {
            return $args;
        }
        global $wp;
        $filter = $request['filter'];

        $vars = apply_filters('query_vars', $wp->public_query_vars);
        foreach ($vars as $var) {
            if (isset($filter[$var])) {
                $args[$var] = $filter[$var];
            }
        }
        return $args;
    }

    public function addFilters()
    {
        add_filter('rest_faq_query', [$this, 'addFilterParam'], 10, 2);
        add_filter('rest_faq_category_query', [$this, 'addFilterParam'], 10, 2);
        add_filter('rest_faq_tag_query', [$this, 'addFilterParam'], 10, 2);
    }

    public function getChildrenCategories($term)
    {
        $children = get_terms(
            array(
                'taxonomy' => 'faq_category',
                'parent' => $term['id'],
            ));
        $aRet = array();
        foreach ($children as $child) {
            $aRet[] = $child->name;
        }
        return $aRet;
    }

    public function getFaqCategories($post)
    {
        $cats = wp_get_post_terms($post['id'], 'faq_category', array('fields' => 'names'));
        return $cats;
    }

    public function getFaqTags($post)
    {
        return wp_get_post_terms($post['id'], 'faq_tag', array('fields' => 'names'));
    }

    public function getTaxSource($object)
    {
        return get_term_meta($object['id'], 'source', true);
    }

    public function getTaxLang($object)
    {
        return get_term_meta($object['id'], 'lang', true);
    }

    public function createTaxDetails()
    {
        register_rest_field('faq',
            'faq_category',
            array(
                'get_callback' => [$this, 'getFaqCategories'],
                'update_callback' => null,
                'schema' => null,
            )
        );
        register_rest_field('faq',
            'faq_tag',
            array(
                'get_callback' => [$this, 'getFaqTags'],
                'update_callback' => null,
                'schema' => null,
            )
        );

        $fields = array('faq_category', 'faq_tag');
        foreach ($fields as $field) {
            register_rest_field($field, 'source', array(
                'get_callback' => [$this, 'getTaxSource'],
                'schema' => null,
            ));
            register_rest_field($field, 'lang', array(
                'get_callback' => [$this, 'getTaxLang'],
                'schema' => null,
            ));
        }
    }

    public function createChildren()
    {
        register_rest_field('faq_category',
            'children',
            array(
                'get_callback' => [$this, 'getChildrenCategories'],
                'update_callback' => null,
                'schema' => null,
            )
        );
    }
}
