<?php

namespace RRZE\FAQ;

use function RRZE\FAQ\Config\getOTRS;
use function RRZE\FAQ\Config\logIt;


defined('ABSPATH') || exit;


class Sync {
    public function doSync( $mode ) {

        define( 'OTRS', getOTRS() );

        // delete all FAQ that came from OTRS
        $iDel = 0;
        // $allFAQ = get_posts( array( 'post_type' => 'faq', 'meta_key' => 'source', 'meta_value' => 'OTRS', 'numberposts' => -1 ) );
        $allFAQ = get_posts( array( 'post_type' => 'faq', 'numberposts' => -1 ) );
        foreach ( $allFAQ as $faq ) {
            wp_delete_post( $faq->ID, true );
            $iDel++;
        }   
        
        // sync all FAQ for each selected category
        $iNew = 0;
        $option = get_option( 'rrze-faq' );

        foreach ( $option['otrs_categories'] as $catID ){
            $faqIDs = wp_remote_get( OTRS . '/FAQSearch?CategoryIDs=' . $catID );
            $status_code = wp_remote_retrieve_response_code( $faqIDs );
            if ( $status_code === 200 ) {
                $faqIDs = json_decode( $faqIDs['body'], true );
                if ( !isset( $faqIDs['Error'] ) ) {
                    foreach ( $faqIDs['ID'] as $faqID ){
                        $faq = wp_remote_get( OTRS . '/FAQ?ItemID=' . $faqID );
                        $status_code = wp_remote_retrieve_response_code($faq );
                        if ( $status_code === 200 ) {
                            $faq = json_decode( $faq['body'], true );
                            if ( !isset( $faq['Error'] ) && $faq['FAQItem'][0]['Valid'] == 'valid' ) {
                                // add FAQ
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
                                        'faq_category' => $faq['FAQItem'][0]['CategoryName'],
                                        'faq_tag' => str_replace( ' ', ',', $faq['FAQItem'][0]['Keywords'] )
                                        )
                                    ) );
                                $term_ids = wp_get_post_terms( $post_id, 'faq_category', array( 'fields' => 'ids' ) );
                                array_push( $term_ids, wp_get_post_terms( $post_id, 'faq_tag', array( 'fields' => 'ids' ) ) );
                                foreach( $term_ids as $id ){
                                    add_term_meta( $id, 'source', 'OTRS', TRUE );
                                }
                                $iNew++;
                            }
                        }
                    }
                }
            }
        }

        date_default_timezone_set('Europe/Berlin');
        $msg = $iNew . __( ' FAQ added', 'rrze-faq' ) . '. ' . $iDel . __( ' FAQ deleted', 'rrze-faq' ) . '. Required time: ' . sprintf( '%.1f ', microtime( true ) - $_SERVER["REQUEST_TIME_FLOAT"] ) . __( 'seconds', 'rrze-faq' );
        add_settings_error( 'Synchronization completed', 'synccompleted', $msg );
        settings_errors();
        logIt( date("Y-m-d H:i:s") . ' | ' . $msg );
        return;
    }
}
