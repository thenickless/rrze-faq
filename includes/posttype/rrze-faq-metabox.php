<?php

namespace RRZE\Glossar\Server;

function fau_glossar_metabox() {
    add_meta_box(
        'fau_glossar_metabox',
        __( 'Terms of use', 'rrze-faq' ),
        'RRZE\Glossar\Server\fau_glossar_metabox_content',
        'glossary',
        'normal',
        'high'
    );
}

function fau_glossar_metabox_content( $object, $box ) { 
    global $post;

    if ($post->ID >0) {
        $helpuse = __('<p>Integration in pages and posts via: </p>','fau');

        $helpuse .= '<ul><li>Einzelbeiträge:';
        $helpuse .= '<pre> [glossary id="'.$post->ID.'"] </pre>';
        $helpuse .= 'Inklusive der optionalen Parameter: color="<i>Fakultät</i>", wobei <i>Fakultät</i> folgende Werte haben kann: tf, nat, rw, med, phil.';
        $helpuse .= '<br>Bei der Einzeleinzeige eines Glossareintrags, ist dieser nicht in einem Accordion, sondern wird so wie er ist angezeigt.';
        $helpuse .= '</li>';
        $helpuse .= '<li>Accordion mit Kategory:';
        $helpuse .= '<pre> [glossary category="<i>Kategoryname</i>"] </pre>';
        $helpuse .= '</li>';	
        $helpuse .= '<li>Accordion mit allen Beiträgen:';
        $helpuse .= '<pre> [glossary] </pre>';
        $helpuse .= '</li></ul>';	

        echo $helpuse;
    }

    return;
}

add_action( 'add_meta_boxes', 'RRZE\Glossar\Server\fau_glossar_metabox' );