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
        // add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_shortcode( 'faq', [ $this, 'shortcodeOutput' ], 10, 2 );
        add_shortcode( 'fau_glossar', [ $this, 'shortcodeOutput' ], 10, 2 ); // alternative shortcode
        add_shortcode( 'glossary', [ $this, 'shortcodeOutput' ], 10, 2 ); // alternative shortcode
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
        // wp_register_style('rrze-faq', plugins_url('../assets/css/rrze-faq.css', __FILE__ ));
        wp_register_script('rrze-faq', plugins_url('../assets/js/rrze-faq.js', __FILE__ ));
    }

    private function get_letter( &$txt ) {
        return mb_strtoupper( mb_substr( remove_accents( $txt ), 0, 1 ), 'UTF-8');
    }

    private function create_a_z( &$aSearch ){
        $ret = '<ul class="letters" aria-hidden="true">';
        foreach ( range( 'A', 'Z' ) as $a ) {
            if ( array_key_exists( $a, $aSearch ) ) {
                $ret .= '<li class="filled"><a href="#letter-'.$a.'">'.$a.'</a></li>';
            }  else {
                $ret .= '<li>'.$a.'</li>';
            }
        }
        return $ret . '</ul>';
    }

    private function get_tax_query( &$aTax ){
        $ret = '';
        $aTmp = array();
        foreach( $aTax as $field => $aVal ){
            $aID = array();
            foreach( $aVal as $val ){
                $term = get_term_by( 'slug', $val, 'glossary_' . $field );
                if ( $term ){
                    $aID[] = $term->term_id;
                }
            }
            if ( $aID ){
                $aTmp[] = array(
                    'taxonomy' => 'glossary_' . $field,
                    'field' => 'id', // can be slug or id - a CPT-onomy term's ID is the same as its post ID
                    'terms' => $aID,
                    'operator' => 'IN'
                );
            }
        }
        if ( $aTmp ){
            $ret = array( $aTmp );
            if ( count ( $aTmp ) > 1 ){
                $ret['relation'] = 'AND';
            }
        }
        return $ret;
    }

    private function search_array_by_key( &$needle, &$aHaystack ){
        foreach( $aHaystack as $k => $v ){
            if ( $k === $needle ){
                return $v;
            }
        }
        return FALSE;
    }

    /**
     * Generieren Sie die Shortcode-Ausgabe
     * @param  array   $atts Shortcode-Attribute
     * @param  string  $content Beiliegender Inhalt
     * @return string Gib den Inhalt zurück
     */
    public function shortcodeOutput( $atts ) {

        // merge given attributes with default ones
        $atts_default = array();
        foreach( $this->settings as $k => $v ){
            if ( $k != 'block' ){
                $atts_default[$k] = $v['default'];
            }
        }
        $atts = shortcode_atts( $atts_default, $atts );
        extract( $atts );

        $content = '';

        if ( array_key_exists( $glossary, $this->settings['glossary']['values'] ) == FALSE ){
            return __( 'Attribute glossary is not correct. Please use either glossary="category" or glossary="tag".', 'rrze-faq' );
        }

        if(isset($category) && empty($id) && !empty($domain)) {
            // DOMAIN
            $domains = get_option('registerDomain');
            if(in_array($domain, $domains )) {
                if ( strpos( $domain, 'http' ) === 0 ) {
                    $domainurl = $domain;
                } else {
                    $domainurl = 'https://' . $domain;
                }
            
                $content = wp_remote_get( $domainurl . '/wp-json/wp/v2/glossary?filter[glossary_category]=' . $category . '&per_page=200', array( 'sslverify'   => false ) );
                $status_code = wp_remote_retrieve_response_code( $content );
                if ( $status_code === 200 ) {
                    $content = $content['body'];
                } else {
                    return __( 'request returns ' . $status_code, 'rrze-faq' );
                }
               
                $data = json_decode( $content, true );
               
                for($i = 0; $i < sizeof($data); $i++) {
                    $items[$i]['title']      = $data[$i]['title']['rendered'];
                    $items[$i]['content']    = $data[$i]['content']['rendered'];
                }
                
                $collator = new \Collator('de_DE');
            
                usort( $items, function ( array $a, array $b ) use ( $collator ) {
                    $result = $collator->compare( $a['title'], $b['title'] );
                    return $result;
                });
                
                $aLetters = array();
                $accordion = '[collapsibles]';
                foreach ( $items as $item ) {
                    $letter = $this->get_letter( $item['title'] );
                    $aLetters[$letter] = TRUE; 
                    $accordion .= '[collapse title="' . $item['title'] . '"]' . str_replace( ']]>', ']]&gt;', $item['content'] ) . '[/collapse]';
                }
        
                $accordion .= '[/collapsibles]';
                $content = '<div class="fau-glossar">' . $this->create_a_z( $aLetters ) . '</div>';
                $content .= do_shortcode( $accordion );
            } else {
                return __( 'Domain is not registered', 'rrze-faq' );
            }
        } elseif( isset( $id ) && intval( $id ) > 0 && !empty( $domain ) ) {
            // DOMAIN
            $domains = get_option('registerDomain');
            if( in_array( $domain, $domains ) ) {
                if ( strpos( $domain, 'http' ) === 0 ) {
                    $domainurl = $domain;
                } else {
                    $domainurl = 'https://' . $domain;
                }
            
                $content = wp_remote_get( $domainurl . '/wp-json/wp/v2/glossary/' . $id, array( 'sslverify'   => false ) );
                $status_code = wp_remote_retrieve_response_code( $content );
                if ( 200 === $status_code ) {
                    $item = $content['body'];
                } else {
                    return __( 'request returns ' . $status_code, 'rrze-faq' );
                }
                
                $list = json_decode( $item, true );
                $title = get_the_title( $list['id'] );
                // $letter = $this->get_letter( $title );
        
                $content = str_replace( $list['content']['rendered'] );
                if ( !isset( $content ) || ( mb_strlen($content) < 1 ) ) {
                    $content = get_post_meta( $id, 'description', true );
                }
            
                $accordion = '[collapsibles][collapse title="' . $title . '"]' . $content . '[/collapse][/collapsibles]';
                $content = do_shortcode( $accordion );
            } else {
                return __( 'Domain is not registered', 'rrze-faq' );
            }
        } elseif ( isset( $id ) && intval( $id ) > 0 ) {
            // SINGLE FAQ
            $title = get_the_title( $id );
            $content = str_replace( ']]>', ']]&gt;', apply_filters( 'the_content',  get_post_field('post_content',$id) ) );
            if ( !isset( $content ) || ( mb_strlen( $content ) < 1)) {
                $content = get_post_meta( $id, 'description', true );
            }
            $accordion = '[collapsibles][collapse title="' . $title . '"]' . $content . '[/collapse][/collapsibles]';
            $content = do_shortcode( $accordion );
        } else {
            // attribute category or tag is given or none of them
            $aLetters = array();
            $aCategory = array();
            $field = '';
            $aTax = array();
            $tax_query = '';
            $postQuery = array('post_type' => 'glossary', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC', 'suppress_filters' => false);
            if ( $category ){
                $aTax['category'] = explode(',', trim( $category ) );
            }
            if ( $tag ){
                $aTax['tag'] = explode(',', trim( $tag ) );
            }
            $tax_query = $this->get_tax_query( $aTax );
            if ( $tax_query ){
                $postQuery['tax_query'] = $tax_query;
            }
            $posts = get_posts( $postQuery );

            if ( $glossary == 'tag' ){
                // get all used tags
                $aUsedTags = array();
                $aPostIDs = array();
                foreach( $posts as $post ) {
                    // get all tags for each post
                    $aTermIds = array();
                    $term = wp_get_post_terms( $post->ID, 'glossary_tag' );
                    if ( $term ){
                        foreach( $term as $t ){
                            $aTermIds[] = $t->term_id;
                            $letter = $this->get_letter( $t->name );
                            $aLetters[$letter] = TRUE; 
                            $aUsedTags[$t->name] = array( 'letter' => $letter, 'ID' => $t->term_id );
                            $aPostIDs[$t->term_id][] = $post->ID;
                        }
                    }                    
                }
                if ( $aLetters ){
                    $content = '<div class="fau-glossar">' . $this->create_a_z( $aLetters ) . '</div>';
                }

                asort( $aUsedTags );
                $accordion = '[collapsibles]';
                foreach ( $aUsedTags as $k => $aVal ){
                    $accordion .= '[collapse title="' . $k . '"]';
                    // find the postIDs to this tag
                    $aIDs = $this->search_array_by_key( $aVal['ID'], $aPostIDs );
                    foreach ( $aIDs as $ID ){
                        $tmp = str_replace( ']]>', ']]&gt;', apply_filters( 'the_content',  get_post_field('post_content', $ID) ) );
                        if ( !isset( $tmp ) || (mb_strlen( $tmp ) < 1)) {
                            $tmp = get_post_meta( $ID, 'description', true );
                        }
                        $accordion .= '[accordion][accordion-item title="' . get_the_title( $ID ) . '"]' . $tmp . '[/accordion-item][/accordion]';
                    }
                    $accordion .= '[/collapse]';
                }
                $accordion .= '[/collapsibles]';
                $content .= do_shortcode( $accordion );
            } else {
                $accordion = '[collapsibles]';
                foreach( $posts as $post ) {
                    $title = get_the_title( $post->ID );
                    $letter = $this->get_letter( $title );
                    $aLetters[$letter] = TRUE; 
                    $content = str_replace( ']]>', ']]&gt;', apply_filters( 'the_content',  get_post_field( 'post_content', $post->ID ) ) );
                    if ( !isset( $content ) || ( mb_strlen($content) < 1 ) ) {
                        $content = get_post_meta( $post->ID, 'description', true );
                    }
                    $accordion .= '[collapse title="' . $title . '"]' . $content . '[/collapse]';
                }
                $accordion .= '[/collapsibles]';
                $content = '<div class="fau-glossar">' . $this->create_a_z( $aLetters ) . '</div>';
                $content .= do_shortcode( $accordion );
            }
       } 
       $this->enqueueScripts();
       return $content;
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

        register_block_type( $this->settings['block']['blocktype'], array(
            'editor_script' => $editor_script,
            'render_callback' => [$this, 'shortcodeOutput'],
            'attributes' => $this->settings
            ) 
        );

        wp_localize_script( $editor_script, $this->settings['block']['blockname'] . 'Config', $this->settings );
    }
}
