<?php

namespace RRZE\FAQ;

defined( 'ABSPATH' ) || exit;

/**
 * Layout settings for "faq"
 */
class Layout {

    public function __construct() {
        add_filter( 'pre_get_posts', [$this, 'makeFaqSortable'] );
        add_action( 'restrict_manage_posts', [$this, 'addTaxPostTable'] );
        // show content in box if not editable ( = source is not "website" )
        add_action( 'add_meta_boxes', [$this, 'addContentBox'] );
        add_action( 'add_meta_boxes', [$this, 'addShortcodeBox'] );
        add_action( 'edit_form_after_title', [$this, 'toggleEditor'] );
        // add_filter( 'use_block_editor_for_post', [$this, 'gutenberg_post_meta'], 10, 2 );
        // Table "All FAQ"
        add_filter( 'manage_edit-faq_columns', [$this, 'addFaqColumns'] );
        add_action( 'manage_faq_posts_custom_column', [$this, 'getFaqColumnsValues'], 10, 2 );
        add_filter( 'manage_edit-faq_sortable_columns', [$this, 'addFaqColumns'] );
        // Table "Category"
        add_filter( 'manage_edit-faq_category_columns', [$this, 'addTaxColumns'] );
        add_filter( 'manage_faq_category_custom_column', [$this, 'getTaxColumnsValues'], 10, 3 );
        add_filter( 'manage_edit-faq_category_sortable_columns', [$this, 'addTaxColumns'] );
        // Table "Tags"
        add_filter( 'manage_edit-faq_tag_columns', [$this, 'addTaxColumns'] );
        add_filter( 'manage_faq_tag_custom_column', [$this, 'getTaxColumnsValues'], 10, 3 );
        add_filter( 'manage_edit-faq_tag_sortable_columns', [$this, 'addTaxColumns'] );
    }

    public function makeFaqSortable( $wp_query ) {
        if ( is_admin() ) {    
            $post_type = $wp_query->query['post_type'];    
            if ( $post_type == 'faq') {
                if( ! isset($wp_query->query['orderby'])) {
                    $wp_query->set('orderby', 'title');
                    $wp_query->set('order', 'ASC');
                }
            }
        }
    }

    public function addTaxPostTable() {
        global $typenow;    
        if( $typenow == "faq" ){
            $filters = get_object_taxonomies( $typenow );    
            foreach ( $filters as $tax_slug ) {
                $tax_obj = get_taxonomy( $tax_slug );
                wp_dropdown_categories( array(
                    'show_option_all' => sprintf(__('Show all %s', 'rrze-faq'), $tax_obj->label),
                    'taxonomy' => $tax_slug,
                    'name' => $tax_obj->name,
                    'orderby' => 'name',
                    'selected' => isset($_GET[$tax_slug]) ? $_GET[$tax_slug] : '',
                    'hierarchical' => $tax_obj->hierarchical,
                    'show_count' => true,
                    'hide_if_empty' => true
                ));
            }
        }
    }
    
    public function fillContentBox( $post ) {
        // $cats = implode( ', ', wp_get_post_terms( $post->ID,  'faq_category', array( 'fields' => 'names' ) ) );
        // $tags = implode( ', ', wp_get_post_terms( $post->ID,  'faq_tag', array( 'fields' => 'names' ) ) );
        echo '<h1>' . $post->post_title . '</h1><br>' . apply_filters( 'the_content', $post->post_content );
        //  . '<hr>' . ( $cats ? '<h3>' . __('Category', 'rrze-faq' ) . '</h3><p>' . $cats . '</p>' : '' ) . ( $tags ? '<h3>' . __('Tags', 'rrze-faq' ) . '</h3><p>' . $tags .'</p>' : '' );
    }

    public function fillShortcodeBox( ) { 
        global $post;
        $ret = '';
        $category = '';
        $tag = '';
        $fields = array( 'category', 'tag');
        foreach ( $fields as $field ){
            $terms = wp_get_post_terms( $post->ID, 'faq_' . $field );
            foreach ( $terms as $term ){
                $$field .= $term->slug . ', ';
            }
            $$field = rtrim( $$field, ', ' );
        }

        if ( $post->ID > 0 ) {
            $ret .= '<h3 class="hndle">' . __('Single entries','rrze-faq') . '</h3>';
            $ret .= '<pre>[faq id="' . $post->ID . '"]</pre>';
            $ret .= ( $category ? '<h3 class="hndle">' . __( 'Accordion with category','rrze-faq') . '</h3><pre>[faq category="' . $category . '"]</pre><p>' . __( 'If there is more than one category listed, use at least one of them.', 'rrze-faq' ) . '</p>' : '' );
            $ret .= ( $tag ? '<h3 class="hndle">' . __( 'Accordion with tag','rrze-faq' ) . '</h3><pre>[faq tag="' . $tag . '"]</pre><p>'. __( 'If there is more than one tag listed, use at least one of them.', 'rrze-faq' ) . '</p>' : '' );
            $ret .= '<h3 class="hndle">' . __( 'Accordion with all entries','rrze-faq' ) . '</h3>';
            $ret .= '<pre>[faq]</pre>';
        }    
        echo $ret;
    }

    public function addShortcodeBox() {
        add_meta_box(
            'shortcode_box', // id, used as the html id att
            __( 'Integration in pages and posts', 'rrze-faq'), // meta box title
            [$this, 'fillShortcodeBox'], // callback function, spits out the content
            'faq', // post type or page. This adds to posts only
            'normal', // context, where on the screen
            'high' // priority, where should this go in the context
        );        
    }


    public function addContentBox() {
        add_meta_box(
            'read_only_content_box', // id, used as the html id att
            __( 'This FAQ cannot be edited because it is sychronized', 'rrze-faq'), // meta box title
            [$this, 'fillContentBox'], // callback function, spits out the content
            'faq', // post type or page. This adds to posts only
            'normal', // context, where on the screen
            'high' // priority, where should this go in the context
        );        
    }

    public function toggleEditor( $post ) {
        if ( $post->post_type == 'faq' ) {
            $source = get_post_meta( $post->ID, "source", true );
            if ( $source && $source != 'website' ){
                remove_post_type_support( 'faq', 'title' );
                remove_post_type_support( 'faq', 'editor' );
                remove_meta_box( 'tagsdiv-faq_category', 'faq', 'side' );
                remove_meta_box( 'tagsdiv-faq_tag', 'faq', 'side' );
            } else {
                remove_meta_box( 'read_only_content_box', 'faq', 'normal' );
            }
        }
    }

    /**
     * Adds sortable column "source" to tables "All FAQ", "Category" and "Tags"
     */
    public function addFaqColumns( $columns ) {
        $columns['taxonomy-faq_category'] = __( 'Category', 'rrze-faq' );
        $columns['taxonomy-faq_tag'] = __( 'Tag', 'rrze-faq' );
        $columns['source'] = __( 'Source', 'rrze-faq' );
        $columns['id'] = __( 'ID', 'rrze-faq' );
        return $columns;
    }
    public function addTaxColumns( $columns ) {
        $columns['source'] = __( 'Source', 'rrze-faq' );
        return $columns;
    }
    public function getFaqColumnsValues( $column_name, $post_id ) {
        if( $column_name == 'id' ) {
            echo $post_id;
        }
        if( $column_name == 'source' ) {
            $source = get_post_meta( $post_id, 'source', true );
            echo $source;
        }
    }
    public function getTaxColumnsValues( $content, $column_name, $term_id ) {
        if( $column_name == 'source' ) {
            $source = get_term_meta( $term_id, 'source', true );
            echo $source;
        }
    }

    /**
     * Trigger editable of CPT faq: 
     * synced FAQ should not be editable
     * self-written FAQ have to be editable
     */
    // public function gutenberg_post_meta( $can_edit, $post)  {
    //     // check settings from Plugin rrze-settings enable_block_editor instead of enable_classic_editor
    //     $ret = FALSE;
    //     $settings = (array) get_option( 'rrze_settings' );
    //     if ( isset( $settings )){
    //         $settings = (array) $settings['writing'];
    //         if ( isset( $settings['enable_block_editor'] ) && $settings['enable_block_editor'] ) {
    //             return TRUE;
    //         }
    //     }

    //     $source = get_post_meta( $post->ID, 'source', TRUE );
    //     if ( $source && $source == 'website' ) {
    //         $ret = TRUE;
    //     }
    //     return $ret;
    // }
}
