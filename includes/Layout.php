<?php

namespace RRZE\FAQ;

defined( 'ABSPATH' ) || exit;

// use function RRZE\FAQ\API\getDomains;
use RRZE\FAQ\API;


/**
 * Layout settings for "faq"
 */
class Layout {

    public function __construct() {
        
        add_filter( 'pre_get_posts', [$this, 'makeFaqSortable'] );
        add_filter( 'enter_title_here', [$this, 'changeTitleText'] );
        // show content in box if not editable ( = source is not "website" )
        add_action( 'admin_menu', [$this, 'toggleEditor'] );
        // Table "All FAQ"
        add_filter( 'manage_faq_posts_columns', [$this, 'addFaqColumns'] );        
        add_action( 'manage_faq_posts_custom_column', [$this, 'getFaqColumnsValues'], 10, 2 );
        add_filter( 'manage_edit-faq_sortable_columns', [$this, 'addFaqSortableColumns'] );
        add_action( 'restrict_manage_posts', [$this, 'addFaqFilters'], 10, 1 );

        // remove_action( 'restrict_manage_posts', [$this, 'addTaxPostTable'] );

        // Table "Category"
        add_filter( 'manage_edit-faq_category_columns', [$this, 'addTaxColumns'] );
        add_filter( 'manage_faq_category_custom_column', [$this, 'getTaxColumnsValues'], 10, 3 );
        add_filter( 'manage_edit-faq_category_sortable_columns', [$this, 'addTaxColumns'] );
        // Table "Tags"
        add_filter( 'manage_edit-faq_tag_columns', [$this, 'addTaxColumns'] );
        add_filter( 'manage_faq_tag_custom_column', [$this, 'getTaxColumnsValues'], 10, 3 );
        add_filter( 'manage_edit-faq_tag_sortable_columns', [$this, 'addTaxColumns'] );
        // show categories and tags under content
        add_filter( 'the_content', [$this, 'showDetails'] );  
        
        add_action( 'save_post_faq', [$this, 'savePostMeta'] );        
    }


    public function makeFaqSortable( $wp_query ) {
        if ( is_admin() ) {    
            $post_type = $wp_query->query['post_type'];    
            if ( $post_type == 'faq') {
                if( ! isset($wp_query->query['orderby'])) {
                    $wp_query->set('orderby', 'title');
                    $wp_query->set('order', 'ASC');
                }

                $orderby = $wp_query->get('orderby');
                if ( $orderby == 'sortfield' ){
                    $wp_query->set('meta_key','sortfield');
                    $wp_query->set('orderby','meta_value');                    
                }
            }
        }
    }

    // public function saveSort( $post_id ){
    public function savePostMeta( $postID ){
        if ( ! current_user_can( 'edit_post', $postID ) || ! isset( $_POST['sortfield'] ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ){
            return $postID;
        }
        update_post_meta( $postID, 'source', 'website' );
        $lang = substr( get_locale(), 0, 2 );
        update_post_meta( $postID, 'lang', $lang );
        update_post_meta( $postID, 'remoteID', $postID );
        $remoteChanged = get_post_timestamp( $postID, 'modified' );
        update_post_meta( $postID, 'remoteChanged', $remoteChanged );
        update_post_meta( $postID, 'sortfield', sanitize_text_field( $_POST['sortfield'] ) );       
    }

    public function sortboxCallback( $meta_id ) {
        $sortfield = get_post_meta( $meta_id->ID, 'sortfield', TRUE );
        $output = '<input type="text" name="sortfield" id="sortfield" class="sortfield" value="'. esc_attr($sortfield) .'">';
        $output .= '<p class="description">' . __( 'Criterion for sorting the output of the shortcode', 'rrze-faq' ) . '</p>';
        echo $output;
    }


    public function fillContentBox( $post ) {
        $mycontent = apply_filters( 'the_content', $post->post_content );
        $mycontent = substr( $mycontent, 0, strpos( $mycontent, '<!-- rrze-faq -->' ));
        echo '<h1>' . $post->post_title . '</h1><br>' . $mycontent;
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
            $ret .= '<h3 class="hndle">' . __('Single entries','rrze-faq') . ':</h3><p>[faq id="' . $post->ID . '"]</p>';
            $ret .= ( $category ? '<h3 class="hndle">' . __( 'Accordion with category','rrze-faq') . ':</h3><p>[faq category="' . $category . '"]</p><p>' . __( 'If there is more than one category listed, use at least one of them.', 'rrze-faq' ) . '</p>' : '' );
            $ret .= ( $tag ? '<h3 class="hndle">' . __( 'Accordion with tag','rrze-faq' ) . ':</h3><p>[faq tag="' . $tag . '"]</p><p>'. __( 'If there is more than one tag listed, use at least one of them.', 'rrze-faq' ) . '</p>' : '' );
            $ret .= '<h3 class="hndle">' . __( 'Accordion with all entries','rrze-faq' ) . ':</h3><p>[faq]</p>';
        }    
        echo $ret;
    }

    public function changeTitleText( $title ){
        $screen = get_current_screen();
        if  ( $screen->post_type == 'faq' ) {
             $title = __( 'Enter question here', 'rrze-faq' );
        }         
        return $title;
    }

    public function toggleEditor(){
        $post_id = ( isset( $_GET['post'] ) ? $_GET['post'] : ( isset ( $_POST['post_ID'] ) ? $_POST['post_ID'] : 0 ) ) ;

        if ( $post_id ){            
            if ( get_post_type( $post_id ) == 'faq' ) {
                $source = get_post_meta( $post_id, "source", TRUE );
                if ( $source ){
                    if ( $source != 'website' ){
                        $api = new API();
                        $domains = $api->getDomains();
                        $source = get_post_meta( $post_id, "source", TRUE );
                        $remoteID = get_post_meta( $post_id, "remoteID", TRUE );
                        $link = $domains[$source] . 'wp-admin/post.php?post=' . $remoteID . '&action=edit';
                        remove_post_type_support( 'faq', 'title' );
                        remove_post_type_support( 'faq', 'editor' );
                        remove_meta_box( 'faq_categorydiv', 'faq', 'side' );
                        remove_meta_box( 'tagsdiv-faq_tag', 'faq', 'side' );
                        // remove_meta_box( 'submitdiv', 'faq', 'side' ); 2020-25-05 : we need submitdiv because of sortbox            
                        add_meta_box(
                            'read_only_content_box', // id, used as the html id att
                            __( 'This FAQ cannot be edited because it is sychronized', 'rrze-faq') . '. <a href="' . $link . '" target="_blank">' . __('You can edit it at the source', 'rrze-faq') . '</a>',
                            [$this, 'fillContentBox'], // callback function, spits out the content
                            'faq', // post type or page. This adds to posts only
                            'normal', // context, where on the screen
                            'high' // priority, where should this go in the context
                        );
                    }
                }
                add_meta_box(
                    'shortcode_box', // id, used as the html id att
                    __( 'Integration in pages and posts', 'rrze-faq'), // meta box title
                    [$this, 'fillShortcodeBox'], // callback function, spits out the content
                    'faq', // post type or page. This adds to posts only
                    'normal'
                );            
            }
        }
        add_meta_box(
            'sortbox', // id, used as the html id att
            __( 'Sort', 'rrze-faq'), // meta box title
            [$this, 'sortboxCallback'], // callback function, spits out the content
            'faq', // post type or page. This adds to posts only
            'side'
            // 'high' // priority, where should this go in the context
        );    
    }

    public function addFaqColumns( $columns ) {
        $columns['sortfield'] = __( 'Sort criterion', 'rrze-faq' );
        $columns['source'] = __( 'Source', 'rrze-faq' );
        $columns['id'] = __( 'ID', 'rrze-faq' );
        return $columns;
    }

    public function addFaqSortableColumns( $columns ) {
        $columns['taxonomy-faq_category'] = __( 'Category', 'rrze-faq' );
        $columns['taxonomy-faq_tag'] = __( 'Tag', 'rrze-faq' );
        $columns['sortfield'] = 'sortfield';
        $columns['source'] = __( 'Source', 'rrze-faq' );
        $columns['id'] = __( 'ID', 'rrze-faq' );
        return $columns;
    }


    public function addFaqFilters( $post_type ){
        if( $post_type !== 'faq' ){
            return;
        }
        $taxonomies_slugs = array(
            'faq_category',
            'faq_tag'
        );
        foreach( $taxonomies_slugs as $slug ){
            $taxonomy = get_taxonomy( $slug );
            $selected = ( isset( $_REQUEST[ $slug ] ) ? $_REQUEST[ $slug ] : '' );
            wp_dropdown_categories( array(
                'show_option_all' =>  $taxonomy->labels->all_items,
                'taxonomy'        =>  $slug,
                'name'            =>  $slug,
                'orderby'         =>  'name',
                'value_field'     =>  'slug',
                'selected'        =>  $selected,
                'hierarchical'    =>  TRUE,
                'show_count'      => TRUE
            ) );
        }
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
            echo get_post_meta( $post_id, 'source', true );
        }
        if( $column_name == 'sortfield' ) {
            echo get_post_meta( $post_id, 'sortfield', true );
        }
    }

    public function getTaxColumnsValues( $content, $column_name, $term_id ) {
        if( $column_name == 'source' ) {
            $source = get_term_meta( $term_id, 'source', true );
            echo $source;
        }
    }

    public function getTermsAsString( &$postID, $field ){
        $ret = '';
        $terms = wp_get_post_terms( $postID, 'faq_' . $field );
        foreach ( $terms as $term ){
            $ret .= $term->name . ', ';
        }
        return substr( $ret, 0, -2 );
    }

    public function showDetails( $content ){
        global $post;
        if ( $post->post_type == 'faq' ){
            $cats = $this->getTermsAsString( $post->ID, 'category' );
            $tags = $this->getTermsAsString( $post->ID, 'tag' );            
            $details = '<!-- rrze-faq --><p id="rrze-faq" class="meta-footer">'
            . ( $cats ? '<span class="post-meta-categories"> '. __( 'Categories', 'rrze-faq' ) . ': ' . $cats . '</span>' : '' )
            . ( $tags ? '<span class="post-meta-tags"> '. __( 'Tags', 'rrze-faq' ) . ': ' . $tags . '</span>' : '' )
            . '</p>';
            $schema = '';
            $source = get_post_meta( $post->ID, "source", TRUE );
            if ( $source == 'website' ){
                $question = get_the_title( $post->ID );
                $answer = wp_strip_all_tags( $content, TRUE );
                $schema = '<div style="display:none" itemscope itemtype="https://schema.org/FAQPage">';
                $schema .= '<div style="display:none" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">';
                $schema .= '<div style="display:none" itemprop="name">' . $question . '</div>';
                $schema .= '<div style="display:none" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">';
                $schema .= '<div style="display:none" itemprop="text">' . $answer . '</div></div></div></div>';
            }
            $content .= $details . $schema;
        }
        return $content;
    }
}
