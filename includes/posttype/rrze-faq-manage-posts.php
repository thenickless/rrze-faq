<?php

namespace RRZE\Glossar\Server;

function fau_glossary_restrict_manage_posts() {
    global $typenow;

    if( $typenow == "glossary" ){
        $filters = get_object_taxonomies($typenow);

        foreach ($filters as $tax_slug) {
            $tax_obj = get_taxonomy($tax_slug);
            wp_dropdown_categories(array(
                'show_option_all' => sprintf(__('Alle %s anzeigen', 'fau'), $tax_obj->label),
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

add_action( 'restrict_manage_posts', 'RRZE\Glossar\Server\fau_glossary_restrict_manage_posts' );