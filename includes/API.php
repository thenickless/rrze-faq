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


    public function getUrl( $url ){
        $ret = FALSE;
        $domains = $this->getDomains();
        if ( $this->isRegisteredDomain( $url ) ){
            $ret = $url . 'wp-json/wp/v2/faq';
        }
        return $ret;
    }

    protected function getTaxonomies( $url, $field, $filter ){
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
                                $items[$entry['id']]['remote_parentID'] = $entry['parent'];
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

    protected function getTaxonomyByID( $url, $ID, $field ){
        $item = array();
        $request = wp_remote_get( $url . '_' . $field . '/' . $ID );
        $status_code = wp_remote_retrieve_response_code( $request );
        if ( $status_code == 200 ){
            $entry = json_decode( wp_remote_retrieve_body( $request ), true );
            if ( !empty( $entry ) ){
                $item = array( 
                    'remote_ID' => $ID,
                    'slug' => $entry['slug'],
                    'name' => $entry['name']
                );
                if ( isset( $entry['parent'] ) ){
                    $item['remote_parentID'] = $entry['parent']; 
                } 
            }
        }
    return $item;
    }

    private function getCategoryByID( $url, $ID ){
        return $this->getTaxonomyByID( $url, $ID, 'category' );
    }

    private function getTagByID( $url, $ID ){
        return $this->getTaxonomyByID( $url, $ID, 'tag' );
    }


    protected function setCategories( $url, $aCategories, $shortname ){
        $aRet = array();
        $all_IDs = array();
        
        foreach ( $aCategories as $ID => $aDetails ){
            $all_IDs[] = $ID;
            $all_IDs[] = $aDetails['remote_parentID'];
        }
        $all_IDs = array_unique( $all_IDs );
        sort($all_IDs);
        if ( !$all_IDs[0] ){
            unset( $all_IDs[0] );
        }

        // get parent and grandparent and greatgrandparent and ... of categories
        foreach ( $all_IDs as $ID ){
            $category = $this->getCategoryByID( $url, $ID );
            $aCategories[$ID] = $category;
            if ( $category['remote_parentID'] ){
                $all_IDs[] = $category['remote_parentID'];
                $all_IDs = array_unique( $all_IDs );
            }
        }

        ksort( $aCategories ); 

        $map = array();
        // insert or update categories:
        foreach ( $aCategories as $ID => $aDetails ){
            $parent = ( isset( $aDetails['remote_parentID'] ) && isset( $map[$aDetails['remote_parentID']] ) ? $map[$aDetails['remote_parentID']] : 0 );
            $term = term_exists( $aDetails['name'], 'faq_category', $parent );
            if ( !$term ) {
                $term = wp_insert_term( $aDetails['name'], 'faq_category', array( 'parent' => $parent), $aDetails['slug'] );
            }
            $map[$ID] = $term['term_id'];
            update_term_meta( $term['term_id'], 'source', $shortname );

            $aRet[$ID] = array(
                'term_id' => $term['term_id'],
                'term_taxonomy_id' => $term['term_taxonomy_id'],
                'remote_term_taxonomy_id' => $ID,
                'slug' => $aDetails['slug'],
                'parentID' => $parent,
                'remote_parentID' => $aDetails['remote_parentID']                
            );
        }

        return $aRet;
    }

    protected function setTags( $url, $aTags, $shortname ){
        $aRet = array();
        foreach ( $aTags as $ID => $aDetails ){
            $term = term_exists( $aDetails['name'], 'faq_tag' );
            if ( !$term ) {
                $term = wp_insert_term( $aDetails['name'], 'faq_tag' );
                update_term_meta( $term['term_taxonomy_id'], 'source', $shortname );
            }
            $aRet = array(
                'remote_term_taxonomy_id' => $ID,
                'term_taxonomy_id' => $term['term_taxonomy_id'],
                'slug' => $aDetails['slug']                
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

    protected function getFAQ( $url, $categories ){
        $items = array();
        // $filter = '';
        // foreach( $aCategories as $category ){
        //     $filter .= $category['slug'] . ',';    
        // }
        // $filter = substr( $filter, 0, -1 );
        $filter = '&filter[faq_category]=' . $categories;

        // echo '<br>getFAQ<br>';
        // echo $filter;
        // exit;
        // echo '<pre>';

        $page = 1;
        do {
            // echo $url . '?page=' . $page . $filter;
            // echo '<br>';
            $request = wp_remote_get( $url . '?page=' . $page . $filter );
            $status_code = wp_remote_retrieve_response_code( $request );
            if ( $status_code == 200 ){
                $entries = json_decode( wp_remote_retrieve_body( $request ), true );
                if ( !empty( $entries ) ){
                    if ( !isset( $entries[0] ) ){
                        $entries = array( $entries );
                    }

                    foreach( $entries as $entry ){
                        $items[] = array(
                            'faqID' => $entry['post-meta-fields']['faqID'],
                            'title' => $entry['title']['rendered'],
                            'content' => $entry['content']['rendered'],
                            'slug' => $entry['slug'],
                            'lang' => $entry['post-meta-fields']['lang'],
                            'remote_category_ids' => $entry['faq_category'],
                            'remote_tag_ids' => $entry['faq_tag']
                        );
                    }
                    // var_dump($entry);
                }else{
                    continue;
                }
            }
            $page++;   
        } while ( $status_code == 200 );

        // echo 'page=' . $page;
        // exit;

        return $items;
    }

    public function setFAQ( $url, $categories, $shortname ){
        $iCnt = 0;
        $aCategories = $this->getCategories( $url, $categories );
        $aCategories = $this->setCategories( $url, $aCategories, $shortname );

        // echo '<pre>';
        // echo 'setFAQ 1';
        // var_dump($aCategories);
        // exit;

        // get FAQ
        $aFaq = $this->getFAQ( $url, $categories );

        // echo '<pre>';
        // echo 'setFAQ 1 : ';
        // echo count($aFaq);
        // exit;

        // get Tags
        $remote_tag_ids = array();
        foreach ( $aFaq as $faq ){
            foreach( $faq['remote_tag_ids'] as $ID ){
                $remote_tag_ids[] = $ID;
            }
        }
        $remote_tag_ids = array_unique( $remote_tag_ids );
        $aTags = array();
        foreach( $remote_tag_ids as $ID ){
            $tag = $this->getTagByID( $url, $ID );
            $aTags[$tag['remote_ID']] = $tag['slug'];
        }

        // set FAQ
        foreach ( $aFaq as $faq ){
            $tags = '';
            foreach( $faq['remote_tag_ids'] as $ID ){
                $tags .= $aTags[$ID] . ',';
            }
            $tags = substr( $tags, 0, -1 );

            $categories = array();
            foreach( $faq['remote_category_ids'] as $ID ){
                $categories[] = $aCategories[$ID]['term_taxonomy_id'];
            }
    
            $post_id = wp_insert_post( array(
                'post_title' => $faq['title'],
                'post_content' => $faq['content'],
                'post_name' => sanitize_title( $faq['title'] ),
                'post_type' => 'faq',
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_status' => 'publish',
                'meta_input' => array(
                    'source' => $shortname,
                    'faqID' => $faq['faqID'],
                    'lang' => $faq['lang']
                    ),
                'tax_input' => array(
                    'faq_category' => $categories,
                    'faq_tag' => $tags
                    )
                ) );
            $iCnt++;
        }
        return $iCnt;
    }
}    


