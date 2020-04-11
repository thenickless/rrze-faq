<?php

namespace RRZE\FAQ;


defined('ABSPATH') || exit;

define ('ENDPOINT', 'wp-json/wp/v2/faq' );

class API {

    private $aRemoteCategoryIDs = array();
    private $aRemoteTagIDs = array();


    protected function checkDomain( &$url ){
        $content = wp_remote_get( $url . ENDPOINT . '?per_page=1' );
        $status_code = wp_remote_retrieve_response_code( $content );
        return ( $status_code != 200 ? FALSE : TRUE );
    }

    protected function isRegisteredDomain( &$url ){
        return in_array( $url, $this->getDomains() );
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
    
    public function setDomain( &$shortname, &$url ){
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

    protected function getTaxonomies( &$url, $field, &$filter ){
        $items = array();    
        $url .= ENDPOINT . '_' . $field;    
        $slug = ( $filter ? '&slug=' . $filter : '' );
        $page = 1;

        do {
            $request = wp_remote_get( $url . '?page=' . $page . $slug );
            $status_code = wp_remote_retrieve_response_code( $request );
            if ( $status_code == 200 ){
                $entries = json_decode( wp_remote_retrieve_body( $request ), true );
                if ( !empty( $entries ) ){
                    foreach( $entries as $entry ){
                        // if ( $entry['meta']['source'] == 'website' ){
                            $items[$entry['id']] = array( 
                                'slug' => $entry['slug'],
                                'name' => $entry['name']
                            );
                            if ( isset( $entry['parent'] ) ){
                                $items[$entry['id']]['remote_parentID'] = $entry['parent'];
                            }    
                        // }
                    }
                }
            }
            $page++;   
        } while ( ( $status_code == 200 ) && ( !empty( $entries ) ) );

        return $items;
    }

    public function getCategories( &$url, &$categories = '' ){
        return $this->getTaxonomies( $url, 'category', $categories );
    }

    // public function getTags( $url, $tags = '' ){
    //     return $this->getTaxonomies( $url, 'tag', $tags );
    // }

    protected function getTaxonomyByID( &$url, &$remoteID, $field ){
        $item = array();
        $request = wp_remote_get( $url . ENDPOINT . '_' . $field . '/' . $remoteID . '/?_fields=name,parent,meta' );
        $status_code = wp_remote_retrieve_response_code( $request );
        if ( $status_code == 200 ){
            $entry = json_decode( wp_remote_retrieve_body( $request ), true );
            if ( !empty( $entry ) ){
                // if ( $entry['meta']['source'] == 'website' ){
                    $item = array( 
                        'remoteParentID' => ( isset( $entry['parent'] ) ? $entry['parent'] : 0 ),
                        'name' => $entry['name']
                    );
                // }
            }
        }
    return $item;
    }

    protected function getCategoryByID( &$url, &$remoteID ){
        return $this->getTaxonomyByID( $url, $remoteID, 'category' );
    }

    protected function getTagByID( &$url, &$remoteID ){
        return $this->getTaxonomyByID( $url, $remoteID, 'tag' );
    }


    protected function setCategories( &$aCategories, &$shortname ){
        $aRet = array();

        ksort( $aCategories ); 

        // insert or update categories:
        foreach ( $aCategories as $remoteID => $aDetails ){
            $term = term_exists( $aDetails['name'], 'faq_category' );
            if ( !$term ) {
                $term = wp_insert_term( $aDetails['name'], 'faq_category' );
            }
            update_term_meta( $term['term_id'], 'source', $shortname );

            $aRet[$remoteID] = array(
                'term_id' => (int) $term['term_id'],
                'term_taxonomy_id' => (int) $term['term_taxonomy_id'],
                'remoteParentID' => $aDetails['remoteParentID']                
            );
        }

        // set parent
        foreach ( $aCategories as $remoteID => $aDetails ){
            if ( $aDetails['remoteParentID'] ){
                $term = wp_update_term( $aRet[$remoteID]['term_id'], 'faq_category', array( 'parent' => $aRet[$aDetails['remoteParentID']]['term_id'] ) );
            }
        }
        return $aRet;
    }

    protected function setTags( &$aTags, &$shortname ){
        $aRet = array();
        foreach ( $aTags as $remoteID => $aDetails ){
            $term = term_exists( $aDetails['name'], 'faq_tag' );
            if ( !$term ) {
                $term = wp_insert_term( $aDetails['name'], 'faq_tag' );
            }
            update_term_meta( $term['term_id'], 'source', $shortname );
            $aRet[$remoteID] = array(
                'name' => $aDetails['name']
                );
        }
        return $aRet;
    }

    public function deleteFAQ( $source ){
        // deletes all FAQ by source
        $iDel = 0;
        $allFAQ = get_posts( array( 'post_type' => 'faq', 'meta_key' => 'source', 'meta_value' => $source, 'numberposts' => -1 ) );
        foreach ( $allFAQ as $faq ) {
            wp_delete_post( $faq->ID, true );
            $iDel++;
        } 
        return $iDel;
    }

    protected function getFAQ( &$url, &$categories ){
        $faqs = array();
        $filter = '&filter[faq_category]=' . $categories;
        $page = 1;

        do {
            $request = wp_remote_get( $url . ENDPOINT . '?_fields=title,content,faq_category,faq_tag,post-meta-fields&page=' . $page . $filter );
            $status_code = wp_remote_retrieve_response_code( $request );
            if ( $status_code == 200 ){
                $entries = json_decode( wp_remote_retrieve_body( $request ), true );
                if ( !empty( $entries ) ){
                    if ( !isset( $entries[0] ) ){
                        $entries = array( $entries );
                    }
                    foreach( $entries as $entry ){
                        // if ( $entry['post-meta-fields']['source'] == 'website' ){
                            $faqs[] = array(
                                'title' => $entry['title']['rendered'],
                                'content' => $entry['content']['rendered'],
                                'lang' => $entry['post-meta-fields']['lang'],
                                // 'faqID' => $entry['post-meta-fields']['faqID'],
                                'aRemoteCategoryIDs' => $entry['faq_category'],
                                'aRemoteTagIDs' => $entry['faq_tag']
                            );
    
                            $this->aRemoteTagIDs = array_merge( $this->aRemoteTagIDs, $entry['faq_tag'] );
                            $this->aRemoteCategoryIDs = array_merge( $this->aRemoteCategoryIDs, $entry['faq_category'] );
                        // }
                    }
                }
            }
            $page++;   
        } while ( ( $status_code == 200 ) && ( !empty( $entries ) ) );
        array_unique( $this->aRemoteCategoryIDs );
        array_unique( $this->aRemoteTagIDs );

        return $faqs;
    }

    protected function getRemoteCategories( &$url ){
        $aCategories = array();
        foreach( $this->aRemoteCategoryIDs as $remoteID ){
            $cat = $this->getCategoryByID( $url, $remoteID );
            if ( $cat['remoteParentID'] ){
                $this->aRemoteCategoryIDs[] = $cat['remoteParentID'];
            }
            $aCategories[$remoteID] = $cat;
        }
        return $aCategories;
    }

    protected function getRemoteTags( &$url ){
        $aTags = array();
        foreach( $this->aRemoteTagIDs as $remoteID ){
            $aTags[$remoteID] = $this->getTagByID( $url, $remoteID );
        }
        return $aTags;
    }

    protected function getTaxIDsAsString( &$aRemoteIDs, &$aMap ){
        $ret = '';
        foreach( $aRemoteIDs as $remoteID ){
            $ret .= $aMap[$remoteID]['term_id'] . ',';
        }
        return substr( $ret, 0, -1 );
    }

    protected function getTaxNamesAsString( &$aRemoteIDs, &$aMap ){
        $ret = '';
        foreach( $aRemoteIDs as $remoteID ){
            $ret .= $aMap[$remoteID]['name'] . ',';
        }
        return substr( $ret, 0, -1 );
    }

    public function setFAQ( &$url, &$categories, &$shortname ){
        $iCnt = 0;

        // get all FAQ
        $aFaq = $this->getFAQ( $url, $categories );

        // get all categories as parent categories as well and set them
        $aCategories = $this->getRemoteCategories( $url );
        $aCatMap = $this->setCategories( $aCategories, $shortname );

        // get remote tags and set them
        $aTags = $this->getRemoteTags( $url );
        $aTagMap = $this->setTags( $aTags, $shortname );

        // set FAQ
        foreach ( $aFaq as $faq ){

            $post_id = wp_insert_post( array(
                'post_type' => 'faq',
                'post_name' => sanitize_title( $faq['title'] ),
                'post_title' => $faq['title'],
                'post_content' => $faq['content'],
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_status' => 'publish',
                'meta_input' => array(
                    'source' => $shortname,
                    // 'faqID' => $faq['faqID'],
                    'lang' => $faq['lang']
                    ),
                'tax_input' => array(
                    'faq_category' => $this->getTaxIDsAsString( $faq['aRemoteCategoryIDs'], $aCatMap ),
                    'faq_tag' => $this->getTaxNamesAsString( $faq['aRemoteTagIDs'], $aTagMap )
                    )
                ) );
            $iCnt++;
        }
        return $iCnt;
    }
}    


