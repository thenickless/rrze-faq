<?php

namespace RRZE\Glossar\Server;

function fau_get_glossar( $id=0, $cat='', $color = '') { 

    if (isset($id) && intval($id)>0) {
        $title = get_the_title($id);
        $letter = remove_accents(get_the_title($id));
        $letter = mb_substr($letter, 0, 1);
        $letter = mb_strtoupper($letter, 'UTF-8');
        $content = apply_filters( 'the_content',  get_post_field('post_content',$id) );
        $content = str_replace( ']]>', ']]&gt;', $content );
        if ( isset($content) && (mb_strlen($content) > 1)) {
            $desc = $content;
        } else {
            $desc = get_post_meta( $id, 'description', true );
        }

        $result = '<article class="accordionbox fau-glossar" id="letter-'.$letter.'">'."\n";

        if (isset($color) && strlen(fau_san($color))>0) {
            $addclass= fau_san($color);
             $result .= '<header class="'.$addclass.'"><h2>'.$title.'</h2></header>'."\n";
        } else {		
            $result .= '<header><h2>'.$title.'</h2></header>'."\n";
        }
        $result .= '<div class="body">'."\n";
        $result .= $desc."\n";
        $result .= '</div>'."\n";
        $result .= '</article>'."\n";
        return $result;

    } else {
        $category = array();
        if ($cat) {
            $category = get_term_by('slug', $cat, 'glossary_category');
        }	
        if ($category) {
            $catid = $category->term_id;
            $posts = get_posts(array('post_type' => 'glossary', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC', 'tax_query' => array(
                array(
                        'taxonomy' => 'glossary_category',
                        'field' => 'id', // can be slug or id - a CPT-onomy term's ID is the same as its post ID
                        'terms' => $catid
                        )
                ), 'suppress_filters' => false));
        } else {
            $posts = get_posts(array('post_type' => 'glossary', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC', 'suppress_filters' => false));
        }
        $return = '<div class="fau-glossar">';

        $current = "A";
        $letters = array();


        $accordion = '<div class="accordion">'."\n";

        $i = 0;
        foreach($posts as $post) {
                $letter = remove_accents(get_the_title($post->ID));
                $letter = mb_substr($letter, 0, 1);
                $letter = mb_strtoupper($letter, 'UTF-8');

                if( $i == 0 || $letter != $current) {
                        $accordion .= '<h2 id="letter-'.$letter.'">'.$letter.'</h2>'."\n";
                        $current = $letter;
                        $letters[] = $letter;
                }

                $accordion .= '<div class="accordion-group white">'."\n";
                $accordion .= '  <div class="accordion-heading">'."\n";
                $accordion .= '     <a name="'.$post->post_name.'" class="accordion-toggle" data-toggle="collapse" data-parent="accordion-" href="#collapse_'.$post->ID.'000'.$i.'">'.get_the_title($post->ID).'</a>'."\n";
                $accordion .= '  </div>'."\n";
                $accordion .= '  <div id="collapse_'.$post->ID.'000'.$i.'" class="accordion-body">'."\n";
                $accordion .= '    <div class="accordion-inner">'."\n";

                $content = apply_filters( 'the_content',  get_post_field('post_content',$post->ID) );
                $content = str_replace( ']]>', ']]&gt;', $content );
                if ( isset($content) && (mb_strlen($content) > 1)) {
                    $desc = $content;
                } else {
                    $desc = get_post_meta( $post->ID, 'description', true );
                }
                $accordion .= $desc;

                $accordion .= '    </div>'."\n";
                $accordion .= '  </div>'."\n";
                $accordion .= '</div>'."\n";

                $i++;
        }

        $accordion .= '</div>'."\n";

        $return .= '<ul class="letters" aria-hidden="true">'."\n";

        $alphabet = range('A', 'Z');
        foreach($alphabet as $a)  {
                if(in_array($a, $letters)) {
                        $return .= '<li class="filled"><a href="#letter-'.$a.'">'.$a.'</a></li>';
                }  else {
                        $return .= '<li>'.$a.'</li>';
                }
        }

        $return .= '</ul>'."\n";
        $return .= $accordion;
        $return .= '</div>'."\n";
        return $return;
    }
}

function fau_glossary_rte_add_buttons( $plugin_array ) {
    $plugin_array['glossaryrteshortcodes'] = get_template_directory_uri().'/js/tinymce-glossary.js';
    return $plugin_array;
}

add_filter( 'mce_external_plugins','RRZE\Glossar\Server\fau_glossary_rte_add_buttons');