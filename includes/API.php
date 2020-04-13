<?php

namespace RRZE\FAQ;


defined('ABSPATH') || exit;

define ('ENDPOINT', 'wp-json/wp/v2/faq' );

class API {

    private $aAllCategories = array();
    private $aAllTags = array();


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

    protected function getTaxonomies( $url, $field, &$filter ){
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
                        if ( !isset( $items[$entry['name']] ) ){
                            $items[$entry['name']] = array( 'children' => '' );
                        }
                        if ( isset( $entry['parent'] ) && $entry['parent'] ){
                            $entry['parents'] = rtrim( $entry['parents'], ',' );
                            $aRel = explode( ',', $entry['parents'] );                                                    
                            $iCnt = count( $aRel );                
                            for ( $i = 0; $i < $iCnt; $i++ ){
                                $y = $i + 1;
                                if ( $y < $iCnt ){
                                    $items[$aRel[$i]]['children'][$aRel[$y]] = $aRel[$y];
                                } 
                            }
                        }    
                    }
                }
            }
            $page++;   
        } while ( ( $status_code == 200 ) && ( !empty( $entries ) ) );

        return $items;
    }

    public function sortIt( &$arr ){
        uasort( $arr, function($a, $b) {
            return strtolower( $a ) <=> strtolower( $b );
        } );
    }
    
    public function getCategories( $url, $shortname, $categories = '' ){
        $aCategories = $this->getTaxonomies( $url, 'category', $categories );
        $this->setCategories( $aCategories, $shortname );
        $cats = get_terms( array( 
            'taxonomy' => 'faq_category',
            'hide_empty' => FALSE,
            'meta_query' => array( array(
                    'key' => 'source',
                    'value' => $shortname,
            )),
            'orderby' => 'term_id',
            'order' => 'DESC'
         ) );  
         
         $aCats = array();
         foreach ( $cats as $cat ){
             $aCats[$cat->term_id] = array(
                 'id' => $cat->term_id,
                 'name' => $cat->name,
                 'slug' => $cat->slug,
                 'parentID' => $cat->parent,
             );
         }
         foreach ( $aCats as $cat ){
            if ( $cat['parentID'] ){
                $aRet[$cat['parentID']]['children'][$cat['name']] = $cat;
                $aRet[$cat['parentID']]['id'] = $aCats[$cat['parentID']]['id'];
                $aRet[$cat['parentID']]['name'] = $aCats[$cat['parentID']]['name'];                
                $aRet[$cat['parentID']]['slug'] = $aCats[$cat['parentID']]['slug'];                
             } else {
                $aRet[$cat['id']]['id'] = $cat['id'];
                $aRet[$cat['id']]['name'] = $cat['name'];
                $aRet[$cat['id']]['slug'] = $cat['slug'];
             }            
         }
         $aOrdered = array(); 
         foreach ( $aCats as $id => $aDetails ){             
            if ( isset( $aDetails['children'] )){
                asort( $aDetails['children'] );
            }
            $aOrdered[$aDetails['name']] = $aDetails;
        }

        $aRet = array();
        $aUsed = array();
        foreach ( $aOrdered as $name => $cat ){             
            if ( isset( $cat['children'] ) ){
                foreach ( $cat['children'] as $childname => $child ){
                    if ( isset( $aOrdered[$childname] ) ){
                        $cat['children'][$childname] = $aOrdered[$childname];
                        $aUsed[] = $childname;
                    }
                }
            }
            $aRet[$name] = $cat;
        }
        foreach( $aUsed as $name ){
            unset( $aRet[$name] );
        }
    ksort( $aRet, SORT_STRING | SORT_FLAG_CASE );
    return $aRet;
    }

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
        foreach ( $aCategories as $name => $aDetails ){
            $term = term_exists( $name, 'faq_category' );
            if ( !$term ) {
                $term = wp_insert_term( $name, 'faq_category' );
                update_term_meta( $term['term_id'], 'source', $shortname );    
            }    
            if ( $aDetails['children'] ){
                foreach ( $aDetails['children'] as $child ) {
                    $childterm = term_exists( $child, 'faq_category' );
                    if ( !$childterm ) {
                        $childterm = wp_insert_term( $child, 'faq_category', array( 'parent' => $term['term_id'] ) );
                        update_term_meta( $childterm['term_id'], 'source', $shortname );    
                    }
                }
            }
        }
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
        $aCategoryRelation = array();
        $filter = '&filter[faq_category]=' . $categories;
        $page = 1;

        do {
            $request = wp_remote_get( $url . ENDPOINT . '?page=' . $page . $filter );
            $status_code = wp_remote_retrieve_response_code( $request );
            if ( $status_code == 200 ){
                $entries = json_decode( wp_remote_retrieve_body( $request ), true );
                if ( !empty( $entries ) ){
                    if ( !isset( $entries[0] ) ){
                        $entries = array( $entries );
                    }
                    foreach( $entries as $entry ){
                        // echo $entry['meta']['source'][0];
                        if ( $entry['meta']['source'][0] == 'website' ){
                            $content = substr( $entry['content']['rendered'], 0, strpos( $entry['content']['rendered'], '<!-- rrze-faq -->' ));

                            $faqs[$entry['id']] = array(
                                'title' => $entry['title']['rendered'],
                                'content' => $content,
                                'lang' => $entry['meta']['lang'][0],
                                'faq_category' => $entry['faq_category'],
                            );
                            $sTag = '';
                            foreach ( $entry['faq_tag'] as $tag ){
                                $sTag .= $tag . ',';
                            }
                            $faqs[$entry['id']]['faq_tag'] = trim( $sTag, ',' );
                        }
                    }
                    // exit;
                }
            }
            $page++;   
        } while ( ( $status_code == 200 ) && ( !empty( $entries ) ) );

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

        // set FAQ
        foreach ( $aFaq as $faq ){
            $aCategoryIDs = array();
            foreach ( $faq['faq_category'] as $name ){
                $term = get_term_by( 'name', $name, 'faq_category' );
                $aCategoryIDs[] = $term->term_taxonomy_id;
            }

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
                    'lang' => $faq['lang']
                    ),
                'tax_input' => array(
                    'faq_category' => $aCategoryIDs,
                    'faq_tag' => $faq['faq_tag']
                    )
                ) );
            $iCnt++;
        }
        return $iCnt;
    }
}    


