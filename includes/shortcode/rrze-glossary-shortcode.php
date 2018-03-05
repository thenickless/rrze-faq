<?php

namespace RRZE\Glossar\Server;

function fau_glossary( $atts, $content = null ) {
    extract(shortcode_atts(array(
            "category" => 'category',
            "id"    => 'id',
            "color" => 'color',
            "domain" => '',
            "rest"  => 0
            ), $atts));
   
    return fau_get_glossar($id, $category, $color, $domain, $rest);   
}

add_shortcode('glossary', 'RRZE\Glossar\Server\fau_glossary' );
add_shortcode('fau_glossar', 'RRZE\Glossar\Server\fau_glossary' );
add_shortcode('faq', 'RRZE\Glossar\Server\fau_glossary' );

function fau_get_glossar( $id=0, $cat='', $color = '', $domain, $rest) { 
    
    delete_option('testData');
    
    if(isset($cat) && empty($id) && !empty($domain) && $rest = 1) {
       $domains = get_option('registerDomain');
        
       if(in_array($domain, $domains )) {
            $t = getFaqDataByCategory($domain, $cat);
            echo $t;
        } else {
            return 'Domain not registered';
        }
        
    } elseif(isset($id) && intval($id)>0 && !empty($domain) && $rest = 1) {
        echo $id;
        $domains = get_option('registerDomain');
        
        if(in_array($domain, $domains )) {
            $f = getFaqByID($domain, $id, $color);
            echo $f;
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
    
    //if ( get_option( 'testData' ) === false ) {
        $content = wp_remote_get("https://{$domain}/wp-json/wp/v2/glossary/{$id}", $args );
        $status_code = wp_remote_retrieve_response_code( $content );
        if ( 200 === $status_code ) {
            $response = $content['body'];
                //add_option('testData', $response);
        }
    //}
    
    //$response = get_option('testData');

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
    
    //if ( get_option( 'testData' ) === false ) {
        $content = wp_remote_get("https://{$domain}/wp-json/wp/v2/glossary?filter[glossary_category]={$category}&per_page=200", $args );
        $status_code = wp_remote_retrieve_response_code( $content );
        if ( 200 === $status_code ) {
            $response[] = $content['body'];
                //add_option('testData', $response);
        }
    //}
    
    //$response = get_option('testData');
        
         echo '<pre>';
    //print_r($response);
    echo '</pre>';

    return formatRequestedData($response);
}

function formatRequestedData($data) {
    
    $clean = array_filter($data);

    foreach($clean as $c => $v) {
        $list[$c] = json_decode($clean[$c], true);
    }

    $i = 1;
    foreach($list as $k => $v) {
        foreach($v as $b => $c) {
            $id = uniqid();
            $item[$i]['unique'] = $id;
            $item[$i]['id']         = $c['id'];
            $item[$i]['title']      = $c['title']['rendered'];
            $item[$i]['content']    = $c['content']['rendered'];
            $url = parse_url($c['guid']['rendered']);
            $item[$i]['domain']     = $url['host'];
            $i++;
        }
    }
    
    return showFaqAccordion($item);
}

function showFaqAccordion($items) {
    
    $return = '<div class="fau-glossar">';

    $current = "A";
    $letters = array();

    $accordion = '<div class="accordion">'."\n";
    $sort_title = array_multisort(
        array_column($items, 'title'), 
        SORT_ASC,
        $items    
        );

    for($i = 1; $i < count($items); $i++) {
    
        $letter = $items[$i]['title'];
        $letter = mb_substr($letter, 0, 1);
        $letter = mb_strtoupper($letter, 'UTF-8');

        if( $i == 0 || $letter != $current) {
                $accordion .= '<h2 id="letter-'.$letter.'">'.$letter.'</h2>'."\n";
                $current = $letter;
                $letters[] = $letter;
        }

        $accordion .= '<div class="accordion-group white">'."\n";
        $accordion .= '  <div class="accordion-heading">'."\n";
        $accordion .= '     <a name="'.$items[$i]['title'].'" class="accordion-toggle" data-toggle="collapse" data-parent="accordion-" href="#collapse_'. $items[$i]['unique'] .'">'. $items[$i]['title'] .'</a>'."\n";
        $accordion .= '  </div>'."\n";
        $accordion .= '  <div id="collapse_'. $items[$i]['unique'] .'" class="accordion-body">'."\n";
        $accordion .= '    <div class="accordion-inner">'."\n";

        $content = $items[$i]['content'];//apply_filters( 'the_content',  get_post_field('post_content',$post->ID) );
        $content = str_replace( ']]>', ']]&gt;', $items[$i]['content'] );
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