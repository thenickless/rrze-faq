<?php

namespace RRZE\Glossar\Server;

function fau_glossary( $atts, $content = null ) {
    extract(shortcode_atts(array(
            "category" => '',
            "id"    => '',
            "color" => '',
            "domain" => '',
            "rest"  => 0
            ), $atts));
   
    return fau_get_glossar($id, $category, $color, $domain, $rest);   
}

add_shortcode('glossary', 'RRZE\Glossar\Server\fau_glossary' );
add_shortcode('fau_glossar', 'RRZE\Glossar\Server\fau_glossary' );
add_shortcode('faq', 'RRZE\Glossar\Server\fau_glossary' );

function fau_get_glossar($id, $cat='', $color = '', $domain, $rest) { 
    
    if(isset($cat) && empty($id) && !empty($domain) && $rest = 1) {
        
       $domains = get_option('registerDomain');
        
       if(in_array($domain, $domains )) {
            $t = getFaqDataByCategory($domain, $cat);
            return $t;
        } else {
            return 'Domain not registered';
        }
        
    } elseif(isset($id) && intval($id)>0 && !empty($domain) && $rest = 1) {
        
        $domains = get_option('registerDomain');
        
        if(in_array($domain, $domains )) {
            $f = getFaqByID($domain, $id, $color);
            return $f;
        } else {
            return 'Domain not registered';
        }
        
    } elseif (isset($id) && intval($id)>0 ) {
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

function getFaqByID($domain, $id, $color) {
    $args = array(
        'sslverify'   => false,
    );
    
    $content = wp_remote_get("https://{$domain}/wp-json/wp/v2/glossary/{$id}", $args );
    $status_code = wp_remote_retrieve_response_code( $content );
    if ( 200 === $status_code ) {
        $response = $content['body'];
    }

    return formatRequestedDataByID($response, $color);
 
}

function formatRequestedDataByID($item, $color) {
    
    $list = json_decode($item, true);
    $title = get_the_title($list['id']);
    $letter = remove_accents($list['id']);
    $letter = mb_substr($letter, 0, 1);
    $letter = mb_strtoupper($letter, 'UTF-8');
    $content = $list['content']['rendered'];
    $content = str_replace( ']]>', ']]&gt;', $content );
    if ( isset($content) && (mb_strlen($content) > 1)) {
        $desc = $content;
    } else {
        $desc = get_post_meta( $id, 'description', true );
    }

    $result = '<article class="accordionbox fau-glossar" id="letter-'.$letter.'">'."\n";

    if (isset($color) && strlen(fau_san($color))>0) {
        $addclass= fau_san($color);
         $result .= '<header class="'.$addclass.'"><h2>'. $list['title']['rendered'] .'</h2></header>'."\n";
    } else {		
        $result .= '<header><h2>' . $list['title']['rendered'] .'</h2></header>'."\n";
    }
    $result .= '<div class="body">'."\n";
    $result .= $desc."\n";
    $result .= '</div>'."\n";
    $result .= '</article>'."\n";
    return $result;
}

function getFaqDataByCategory($domain, $category) {
    $args = array(
        'sslverify'   => false,
    );
    
    $content = wp_remote_get("https://{$domain}/wp-json/wp/v2/glossary?filter[glossary_category]={$category}&per_page=200", $args );
    $status_code = wp_remote_retrieve_response_code( $content );
    if ( 200 === $status_code ) {
        $response[] = $content['body'];
    }
   
    $b = json_decode($content['body'], true);
   
    return formatRequestedDataByCategory($b);
}

function formatRequestedDataByCategory($data) {
    
    for($i = 0; $i < sizeof($data); $i++) {
        $id = uniqid();
        $item[$i]['unique'] = $id;
        $item[$i]['id']         = $data[$i]['id'];
        $item[$i]['title']      = $data[$i]['title']['rendered'];
        $item[$i]['content']    = $data[$i]['content']['rendered'];
        $url = parse_url($data[$i]['guid']['rendered']);
        $item[$i]['domain']     = $url['host'];
    }
    
    return showFaqAccordion($item);
        
}

function showFaqAccordion($items) {
    
    $collator = new \Collator('de_DE');

    usort($items, function (array $a, array $b) use ($collator) {
        $result = $collator->compare($a['title'], $b['title']);
        return $result;
    });
    
    $return = '<div class="fau-glossar">';

    $current = "A";
    $letters = array();

    $accordion = '<div class="accordion">'."\n";
    
    $i = 0;
    
    foreach($items as $item) {
       
        $letter = remove_accents($item['title']);
        $letter = mb_substr($letter, 0, 1);
        $letter = mb_strtoupper($letter, 'UTF-8');

        if( $i == 0 || $letter != $current) {
                $accordion .= '<h2 id="letter-'.$letter.'">'.$letter.'</h2>'."\n";
                $current = $letter;
                $letters[] = $letter;
        }

        $accordion .= '<div class="accordion-group white">'."\n";
        $accordion .= '  <div class="accordion-heading">'."\n";
        $accordion .= '     <a name="'.$item['title'].'" class="accordion-toggle" data-toggle="collapse" data-parent="accordion-" href="#collapse_'. $item['unique'] .'">'. $item['title'] .'</a>'."\n";
        $accordion .= '  </div>'."\n";
        $accordion .= '  <div id="collapse_'. $item['unique'] .'" class="accordion-body">'."\n";
        $accordion .= '    <div class="accordion-inner">'."\n";

        $content = $items[$i]['content'];//apply_filters( 'the_content',  get_post_field('post_content',$post->ID) );
        $content = str_replace( ']]>', ']]&gt;', $item['content'] );
        $accordion .= $content;

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