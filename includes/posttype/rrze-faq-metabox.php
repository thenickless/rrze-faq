<?php

namespace RRZE\FAQ\Server;

function rrze_faq_metabox() {
    add_meta_box(
        'rrze_faq_metabox',
        __( 'How to', 'rrze-faq' ),
        'RRZE\FAQ\Server\rrze_faq_metabox_content',
        'faq',
        'normal',
        'high'
    );
}

function rrze_faq_metabox_content( $object, $box ) { 
    global $post;

    if ($post->ID >0) {
        $helpuse = __('<p>Integration in pages and posts via: </p>','rrze-faq');
        $helpuse .= '<h3 class="hndle">'.__('Single entries','rrze-faq').'</h3>';
        $helpuse .= '<pre> [faq id="'.$post->ID.'"] </pre>';
        $helpuse .= '<p>'.__('You can also add color codes for the departments: color="<em>faculty shortcut</em>", where <em>faculty shortcut</em> could be: <code>tf</code>, <code>nat</code>, <code>rw</code>, <code>med</code>, <code>phil</code>.','rrze-faq');
        $helpuse .= '<br>'.__('Notice: A single display of an entriy will not be displayed as accordion element.','rrze-faq');
        $helpuse .= '</p>';
        $helpuse .= '<h3 class="hndle">'.__('Accordion with category','rrze-faq').'</h3>';
        $helpuse .= '<pre> [faq category="<i>category</i>"] </pre>';
        $helpuse .= '<h3 class="hndle">'.__('Accordion with all entries','rrze-faq').'</h3>';
        $helpuse .= '<pre> [faq] </pre>';
        echo $helpuse;
    }

    return;
}

add_action( 'add_meta_boxes', 'RRZE\FAQ\Server\rrze_faq_metabox' );