<?php

namespace RRZE\FAQ;

use RRZE\FAQ\API;
use function RRZE\FAQ\Config\logIt;


defined('ABSPATH') || exit;


class Sync {

    private function getURLs(){
        $options = get_option( 'rrze-faq' );



    }

    

    public function doSync( $mode ) {

        // delete all FAQ
        $iDel = 0;
        $allFAQ = get_posts( array( 'post_type' => 'faq', 'meta_key' => 'source', 'meta_value' => 'OTRS', 'numberposts' => -1 ) );
        $allFAQ = get_posts( array( 'post_type' => 'faq', 'numberposts' => -1 ) );
        foreach ( $allFAQ as $faq ) {
            wp_delete_post( $faq->ID, true );
            $iDel++;
        } 
        
        $max_exec_time = ini_get('max_execution_time') - 40; // ini_get('max_execution_time') is not the correct value perhaps due to load-balancer or proxy or other fancy things I've no clue of. But this workaround works for now.

        // sync all FAQ for each selected category
        $iNew = 0;
        $options = get_option( 'rrze-faq' );

        $OTRS_url = $options['otrs_otrs_url'];

        foreach ( $options['otrs_categories'] as $catID ){
            $last_faqID = $this->getLastFAQID( $catID );
            $faqIDs = wp_remote_get( $OTRS_url . '/FAQSearch?CategoryIDs=' . $catID );
            $status_code = wp_remote_retrieve_response_code( $faqIDs );
            if ( $status_code === 200 ) {
                $faqIDs = json_decode( $faqIDs['body'], true );
                if ( !isset( $faqIDs['Error'] ) ) {
                    if ( !is_array( $faqIDs['ID'] ) ){
                        // single entry
                        $faqIDs = array('ID' => $faqIDs);
                    }
                    asort($faqIDs['ID']);
                    foreach ( $faqIDs['ID'] as $faqID ){
                        if ( $faqID <= $last_faqID ){
                            // no new FAQ for this category
                            continue 2; 
                        }
                        $faq = wp_remote_get( $OTRS_url . '/FAQ?ItemID=' . $faqID );
                        $status_code = wp_remote_retrieve_response_code($faq );
                        if ( $status_code === 200 ) {
                            $faq = json_decode( $faq['body'], true );
                            if ( !isset( $faq['Error'] ) && $faq['FAQItem'][0]['Valid'] == 'valid' ) {
                                // add FAQ
                                $faq_categoryIDs = array();
                                if ( strpos( $faq['FAQItem'][0]['CategoryName'], '::' ) !== FALSE ) {
                                    // create parent and child resp children for faq_category
                                    $aFaq_category = explode( '::', $faq['FAQItem'][0]['CategoryName']);
                                    $i = 0;
                                    foreach ( $aFaq_category as $cat ){
                                        if ( $i == 0 ){
                                            $parent = 0;
                                        }else{
                                            $parent = array( 'parent' => $term['term_taxonomy_id'] );
                                        }
                                        $term = term_exists( $cat, 'faq_category', $parent );
                                        if ( !$term ) {
                                            $term = wp_insert_term( $cat, 'faq_category', $parent );
                                        }
                                        $faq_categoryIDs[] = $term['term_taxonomy_id'];
                                    $i++;
                                    }
                                }else{
                                    $term = term_exists( $faq['FAQItem'][0]['CategoryName'], 'faq_category', 0 );
                                    if ( !$term ) {
                                        $term = wp_insert_term( $faq['FAQItem'][0]['CategoryName'], 'faq_category', 0 );
                                    }
                                    $faq_categoryIDs = $term['term_taxonomy_id'];
                                }
                                $post_id = wp_insert_post( array(
                                    'post_title' => $faq['FAQItem'][0]['Title'],
                                    'post_content' => ( $faq['FAQItem'][0]['Field1'] ? '<h3>' . __( 'Symptom', 'rrze-faq' ) . '</h3><p>' . $faq['FAQItem'][0]['Field1'] . '<p/>' : '' ) . 
                                        ( $faq['FAQItem'][0]['Field2'] ? '<h3>' . __( 'Problem', 'rrze-faq' ) . '</h3><p>' . $faq['FAQItem'][0]['Field2'] . '<p/>' : '' ) . 
                                        ( $faq['FAQItem'][0]['Field3'] ? '<h3>' . __( 'Solution', 'rrze-faq' ) . '</h3><p>' . $faq['FAQItem'][0]['Field3'] . '<p/>' : '' ),
                                    'post_name' => sanitize_title( $faq['FAQItem'][0]['Title'] ),
                                    'post_type' => 'faq',
                                    'comment_status' => 'closed',
                                    'ping_status' => 'closed',
                                    'post_status' => 'publish',
                                    'meta_input' => array(
                                        'source' => 'OTRS',
                                        'faqID' => $faq['FAQItem'][0]['FAQID'],
                                        'lang' => $faq['FAQItem'][0]['Language']
                                        ),
                                    'tax_input' => array(
                                        'faq_category' => $faq_categoryIDs,
                                        'faq_tag' => str_replace( ' ', ',', $faq['FAQItem'][0]['Keywords'] )
                                        )
                                    ) );
                                $term_ids = wp_get_post_terms( $post_id, 'faq_category', array( 'fields' => 'ids' ) );
                                $term_ids = array_merge( $term_ids, wp_get_post_terms( $post_id, 'faq_tag', array( 'fields' => 'ids' ) ) );
                                foreach( $term_ids as $id ){
                                    update_term_meta( $id, 'source', 'OTRS' );
                                }
                                $iNew++;
                            }
                        }
                        $this->setLastFAQID( $catID, $faqID );

                        // check execution time to avoid a 402 error
                        $exec_time = (int) (microtime( true ) - $_SERVER["REQUEST_TIME_FLOAT"]);
                        if ( $exec_time >= $max_exec_time ){
                            $sync_msg = __( 'There are still FAQ to be fetched. ' . $iNew . ' FAQ added. Please click on "Synchronize now" again to fetch the rest', 'rrze-faq' ) . ( isset( $options['otrs_auto_sync'] ) && $options['otrs_auto_sync'] == 'on' ? ' or wait for the automatically synchronization.' : '.' ) . ' Required time: ' . sprintf( '%.1f ', microtime( true ) - $_SERVER["REQUEST_TIME_FLOAT"] ) . __( 'seconds', 'rrze-faq' );
                            date_default_timezone_set('Europe/Berlin');
                            add_settings_error( 'Synchronization not completed', 'syncnotcompleted', $sync_msg, 'error' );
                            settings_errors();
                            logIt( date("Y-m-d H:i:s") . ' | ' . $sync_msg . ' | ' . $mode );
                            return;
                        }

                    }
                }
            }
        }
        date_default_timezone_set('Europe/Berlin');
        $sync_msg = 'Synchronization completed. ' . $iNew . __( ' FAQ added', 'rrze-faq' ) . '. Required time: ' . sprintf( '%.1f ', microtime( true ) - $_SERVER["REQUEST_TIME_FLOAT"] ) . __( 'seconds', 'rrze-faq' );
        add_settings_error( 'Synchronization completed', 'synccompleted', $sync_msg, 'success' );
        settings_errors();
        logIt( date("Y-m-d H:i:s") . ' | ' . $sync_msg . ' | ' . $mode );
        return;
    }


    public function getLastFAQID( $catID ){
        $lastSync = get_option( 'rrze-faq-lastSync' );
        return ( isset( $lastSync['catID_' . $catID] ) ? $lastSync['catID_' . $catID] : 0 );
    }

    public function setLastFAQID( $catID, $faqID ){
        // store last $faqID for each $catID
        $lastSync = get_option( 'rrze-faq-lastSync' );
        $lastSync['catID_'.$catID] = $faqID;
        update_option( 'rrze-faq-lastSync', $lastSync );
    }
}
