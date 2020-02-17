<?php

namespace RRZE\FAQ;

defined('ABSPATH') || exit;
use function RRZE\FAQ\Config\getShortcodeSettings;

$settings;

/**
 * Shortcode
 */
class Shortcode {

    /**
     * Der vollständige Pfad- und Dateiname der Plugin-Datei.
     * @var string
     */
    protected $pluginFile;

    /**
     * Settings-Objekt
     * @var object
     */
    private $settings = '';

    /**
     * Variablen Werte zuweisen.
     * @param string $pluginFile Pfad- und Dateiname der Plugin-Datei
     */
    // public function __construct($pluginFile, $settings)
    public function __construct() {
        // $this->pluginFile = $pluginFile;
        $this->settings = getShortcodeSettings();
        add_action( 'init',  [$this, 'gutenberg_init'] );
        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_shortcode( 'glossary', [ $this, 'shortcodeOutput' ], 10, 2 );
        add_shortcode( 'fau_glossar', [ $this, 'shortcodeOutput' ], 10, 2 );
        add_shortcode( 'faq', [ $this, 'shortcodeOutput' ], 10, 2 );
    }

    /**
     * Er wird ausgeführt, sobald die Klasse instanziiert wird.
     * @return void
     */
    public function onLoaded() {
        // add_shortcode('basis_shortcode', [$this, 'shortcodeOutput'], 10, 2);
        // add_shortcode( 'glossary', [ $this, 'shortcodeOutput' ], 10, 2 );
    }

    /**
     * Enqueue der Skripte.
     */
    public function enqueueScripts() {
        wp_register_style('rrze-faq', plugins_url('../assets/css/rrze-faq.css', __FILE__ ));
        wp_register_script('rrze-faq', plugins_url('../assets/js/rrze-faq.js', __FILE__ ));
    }


    /**
     * Generieren Sie die Shortcode-Ausgabe
     * @param  array   $atts Shortcode-Attribute
     * @param  string  $content Beiliegender Inhalt
     * @return string Gib den Inhalt zurück
     */
    public function shortcodeOutput( $atts ) {
        $content = '';
        $atts = shortcode_atts( [
            'category' => '',
            "id" => NULL,
            "color" => '',
            "domain" => '',
            "rest" => FALSE
        ], $atts );

        extract( $atts );

        if(isset($category) && empty($id) && !empty($domain)) {
            echo "1";
            exit;
            $domains = get_option('registerDomain');
            if(in_array($domain, $domains )) {
                $t = $this->getFaqDataByCategory($domain, $category);
                return $t;
            } else {
                return 'Domain not registered';
            }
        } elseif(isset($id) && intval($id)>0 && !empty($domain)) {
            $domains = get_option('registerDomain');
            if(in_array($domain, $domains )) {
                $f = $this->getFaqByID($domain, $id, $color);
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
            $aCategory = array();
            if ($category) {
                $aCategory = get_term_by('slug', $cat, 'glossary_category');
            }	
            if ($aCategory) {
                $catid = $aCategory->term_id;
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
                $id = $post->ID.'000'.$i;
                $title = get_the_title($post->ID);
                $content = apply_filters( 'the_content',  get_post_field('post_content',$post->ID) );
                $content = str_replace( ']]>', ']]&gt;', $content );
                if ( isset($content) && (mb_strlen($content) > 1)) {
                    $desc = $content;
                } else {
                    $desc = get_post_meta( $post->ID, 'description', true );
                }
                $accordion .= $this->getAccordion($id,$title,'','','',$desc);
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
            $this->enqueueScripts();
            return $return;
       } 
    }
    

    public function getFaqByID($domain, $id, $color) {
        $args = array(
            'sslverify'   => false,
        );
        if (strpos($domain, 'http') === 0) {
        $domainurl = $domain;
        } else {
        $domainurl = 'https://'.$domain;
        }
    
        $getfrom = $domainurl.'/wp-json/wp/v2/glossary/'.$id;
        
        $content = wp_remote_get($getfrom, $args );
        $status_code = wp_remote_retrieve_response_code( $content );
        if ( 200 === $status_code ) {
            $response = $content['body'];
        }
    
        return $this->formatRequestedDataByID($response, $color);
     
    }
    
    public function formatRequestedDataByID($item, $color) {
        
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
    

    public function getFaqDataByCategory($domain, $category) {
        $args = array(
            'sslverify'   => false,
        );
        
        if (strpos($domain, 'http') === 0) {
        $domainurl = $domain;
        } else {
        $domainurl = 'https://'.$domain;
        }
    
        $getfrom = $domainurl.'/wp-json/wp/v2/glossary?filter[glossary_category]='.$category.'&per_page=200';
        
        $content = wp_remote_get($getfrom, $args );
        $status_code = wp_remote_retrieve_response_code( $content );
        if ( 200 === $status_code ) {
            $response[] = $content['body'];
        }
       
        $b = json_decode($content['body'], true);
       
        return $this->formatRequestedDataByCategory($b);
    }
    
    public function formatRequestedDataByCategory($data) {
        
        for($i = 0; $i < sizeof($data); $i++) {
            $id = uniqid();
            $item[$i]['unique'] = $id;
            $item[$i]['id']         = $data[$i]['id'];
            $item[$i]['title']      = $data[$i]['title']['rendered'];
            $item[$i]['content']    = $data[$i]['content']['rendered'];
            $url = parse_url($data[$i]['guid']['rendered']);
            $item[$i]['domain']     = $url['host'];
        }
        
        return $this->showFaqAccordion($item);
            
    }
    
    public function showFaqAccordion($items) {
        
        $collator = new \Collator('de_DE');
    
        usort($items, function (array $a, array $b) use ($collator) {
            $result = $collator->compare($a['title'], $b['title']);
            return $result;
        });
        
        $return = '<div class="fau-glossar rrze-faq">';
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
    
        $content = $items[$i]['content'];//apply_filters( 'the_content',  get_post_field('post_content',$post->ID) );
        $content = str_replace( ']]>', ']]&gt;', $item['content'] );
        $accordion .= $this->getAccordion($item['unique'],$item['title'],'','','',$content);
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
        $this->enqueueScripts();
        
        return $return;
    }
    
    
    
    function getAccordion($id = 0, $title = '', $color= '', $load = '', $name= '', $content = '') {
        $addclass = '';
        $title = esc_attr($title);
        $color = $color ? ' ' . esc_attr($color) : '';
        $load = $load ? ' ' . esc_attr($load) : '';
        $name = $name ? ' name="' . esc_attr($name) . '"' : '';

        if (empty($title) && ($empty($content))) {
            return;
        }
        if (!empty($load)) {
            $addclass .= " " . $load;
        }

        $id = intval($id) ? intval($id) : 0;
        if ($id < 1) {
            if (!isset($GLOBALS['current_collapse'])) {
                $GLOBALS['current_collapse'] = 0;
            } else {
                $GLOBALS['current_collapse'] ++;
            }
            $id = $GLOBALS['current_collapse'];
        }

        $output = '<div class="accordion-group' . $color . '">';
        $output .= '<h3 class="accordion-heading"><button class="accordion-toggle" data-toggle="collapse" href="#collapse_' . $id . '">' . $title . '</button></h3>';
        $output .= '<div id="collapse_' . $id . '" class="accordion-body' . $addclass . '"' . $name . '>';
        $output .= '<div class="accordion-inner clearfix">';

        $output .= do_shortcode(trim($content));

        $output .= '</div></div>';  // .accordion-inner & .accordion-body
        $output .= '</div>';        // . accordion-group
    
        return $output;
    }    


    public function gutenberg_init() {
        // Skip block registration if Gutenberg is not enabled/merged.
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        $js = '../assets/js/gutenberg.js';
        $editor_script = $this->settings['block']['blockname'] . '-blockJS';

        wp_register_script(
            $editor_script,
            plugins_url( $js, __FILE__ ),
            array(
                'wp-blocks',
                'wp-i18n',
                'wp-element',
                'wp-components',
                'wp-editor'
            ),
            filemtime( dirname( __FILE__ ) . '/' . $js )
        );

        wp_localize_script( $editor_script, 'blockname', $this->settings['block']['blockname'] );


        $css = '../assets/css/rrze-faq.css';
        $editor_stype = $this->settings['block']['blockname'] . '-blockCSS';

        wp_register_style(
            $editor_stype,
            plugins_url( $css, __FILE__ ),
            array( 'wp-edit-blocks' ),
            filemtime( dirname( __FILE__ ) . '/' . $css )
        );

        register_block_type( $this->settings['block']['blocktype'], array(
            'editor_style' => $editor_stype,
            'editor_script' => $editor_script,
            'render_callback' => [$this, 'shortcodeOutput'],
            'attributes' => $this->settings
            ) 
        );

        wp_localize_script( $editor_script, $this->settings['block']['blockname'] . 'Config', $this->settings );
    }
}
