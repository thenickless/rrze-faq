<?php

namespace RRZE\FAQ;


defined('ABSPATH') || exit;

class API {


    protected function checkDomain( $url ){
        $content = wp_remote_get( $url . 'wp-json/wp/v2/faq?per_page=1' );
        $status_code = wp_remote_retrieve_response_code( $content );
        return ( $status_code != 200 ? FALSE : TRUE );
    }

    public function getDomains(){
        $domains = array();
        $options = get_option( 'rrze-faq' );
        if ( isset( $options['registeredDomains'] ) ){
            foreach( $options['registeredDomains'] as $shortname => $url ){
                $domains[$shortname] = $url;
            }	
        }
        asort( $domains );
        return $domains;
    }
    
    public function setDomain( $shortname, $url ){
        $ret = FALSE;
        $url = trailingslashit( preg_replace( "/^((http|https):\/\/)?/i", "https://", $url ) );
        $domains = $this->getDomains();
        $shortname = strtolower( preg_replace('/[^A-Za-z0-9\-]/', '', str_replace( ' ', '-', $shortname ) ) );
        if ( in_array( $url, $domains ) === FALSE ) {
            if ( $this->checkDomain( $url ) ){
                $domains[$shortname] = $url;
            }else{
                return FALSE;
            }
        }
        return $domains;
    }

    protected function isRegisteredDomain( $url ){
        return in_array( $url, $this->getDomains() );
    }


    protected function getUrl( $url ){
        $ret = FALSE;
        $domains = $this->getDomains();
        if ( $this->isRegisteredDomain( $url ) ){
            $ret = $url . 'wp-json/wp/v2/faq';
        }
        return $ret;
    }

    protected function getTaxonomies( $url, $field, $filter ){
        $url = $this->getUrl( $url );
        if ( !$url ){
            return FALSE;
        }else{
            $items = array();    
            $url .= '_' . $field;    
            $slug = ( $filter ? '&slug=' . $filter : '' );
            $page = 1;

            do {
                $request = wp_remote_get( $url . '?page=' . $page . $slug );
                $status_code = wp_remote_retrieve_response_code( $request );
                if ( $status_code == 200 ){
                    $entries = json_decode( wp_remote_retrieve_body( $request ), true );
                    if ( !empty( $entries ) ){
                        foreach( $entries as $entry ){
                            $items[$entry['id']] = array( 
                                'slug' => $entry['slug'],
                                'name' => $entry['name']
                            );
                            if ( isset( $entry['parent'] ) ){
                                $items[$entry['id']]['parentID'] = $entry['parent'];
                            }
                        }
                    }
                }
                $page++;   
            } while ( ( $status_code == 200 ) && ( !empty( $entries ) ) );
                    
            return $items;
        } 
    }

    public function getCategories( $url, $categories = '' ){
        return $this->getTaxonomies( $url, 'category', $categories );
    }

    public function getTags( $url, $tags = '' ){
        return $this->getTaxonomies( $url, 'tag', $tags );
    }

    protected function setCategories( $url, $categories ){
        ksort( $categories ); 
        foreach ( $categories as $ID => $aDetails ){
            $parent = ( isset( $aDetails['parentID'] ) ? array( 'parent' => $aDetails['parentID'] ) : 0 );
            $term = term_exists( $aDetails['name'], 'faq_category', $parent );
            if ( !$term ) {
                $term = wp_insert_term( $aDetails['name'], 'faq_category', $parent );
            }
            update_term_meta( $term['term_taxonomy_id'], 'source', $url );
        }
        return TRUE;
    }

    protected function setTags( $url, $tags ){
        foreach ( $tags as $ID => $aDetails ){
            $term = term_exists( $aDetails['name'], 'faq_tag' );
            if ( !$term ) {
                $term = wp_insert_term( $aDetails['name'], 'faq_tag' );
                update_term_meta( $term['term_taxonomy_id'], 'source', $url );
            }
        }
        return TRUE;
    }

    protected function deleteFAQ( $source ){
        // deletes all FAQ by source
        $iDel = 0;
        $allFAQ = get_posts( array( 'post_type' => 'faq', 'meta_key' => 'source', 'meta_value' => $source, 'numberposts' => -1 ) );
        foreach ( $allFAQ as $faq ) {
            wp_delete_post( $faq->ID, true );
            $iDel++;
        } 
        return $iDel;
    }

    protected function setFAQ( $url, $categories, $shortname ){

    }


    protected function setLastFAQ( $url, $faqID, $categoryID ){
        $options = get_option( 'rrze-faq' );
        $options['lastSync'][$url]['catID_'.$categoryID] = $faqID;
        update_option( 'rrze-faq', $options );
        return TRUE;
    }

}    


