<?php

namespace RRZE\FAQ;

use function RRZE\FAQ\Config\logIt;


defined('ABSPATH') || exit;

class Sync {
    public function doSync( $mode ) {
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
        foreach ( $option['sync_categories'] as $catID ){
            $faqIDs = wp_remote_get( 'https://www.helpdesk.rrze.fau.de/otrs/nph-genericinterface.pl/Webservice/RRZEPublicFAQConnectorREST/FAQSearch?CategoryIDs=' . $catID );
            $status_code = wp_remote_retrieve_response_code( $faqIDs );
            if ( $status_code === 200 ) {
                $faqIDs = json_decode( $faqIDs['body'], true );
                if ( !isset( $faqIDs['Error'] ) ) {
                    foreach ( $faqIDs['ID'] as $faqID ){
                        $faq = wp_remote_get( 'https://www.helpdesk.rrze.fau.de/otrs/nph-genericinterface.pl/Webservice/RRZEPublicFAQConnectorREST/FAQ?ItemID=' . $faqID );
                        $status_code = wp_remote_retrieve_response_code($faq );
                        if ( $status_code === 200 ) {
                            $faq = json_decode( $faq['body'], true );
                            if ( !isset( $faq['Error'] ) && $faq['FAQItem'][0]['Valid'] == 'valid' ) {
                                // add FAQ
                                $meta_input  = array(
                                    'source' => 'OTRS',
                                    'faqID' => $faq['FAQItem'][0]['FAQID'],
                                    'lang' => $faq['FAQItem'][0]['Language'],
                                    'field1' => $faq['FAQItem'][0]['Field1'],
                                    'field2' => $faq['FAQItem'][0]['Field2'],
                                    'field3' => $faq['FAQItem'][0]['Field3'],
                                    'field4' => $faq['FAQItem'][0]['Field4'],
                                    'field5' => $faq['FAQItem'][0]['Field5'],
                                );

                                $tax_input = array(
                                    'faq_category' => $faq['FAQItem'][0]['CategoryName'],
                                    'faq_tag' => str_replace( ' ', ',', $faq['FAQItem'][0]['Keywords'] )
                                );
        
                                $postVals = array(
                                    'post_title' => $faq['FAQItem'][0]['Title'],
                                    // 'post_content' => '',
                                    'post_name' => sanitize_title( $faq['FAQItem'][0]['Title'] ),
                                    'post_type' => 'faq',
                                    'comment_status' => 'closed',
                                    'ping_status' => 'closed',
                                    'post_status' => 'publish',
                                    'meta_input' => $meta_input,
                                    'tax_input' => $tax_input
                                );
                                $post_id = wp_insert_post( $postVals );
                                $iNew++;
                            }
                        }
                    }
                }
            }
        }

        date_default_timezone_set('Europe/Berlin');
        $msg = date("Y-m-d H:i:s") . ',' . $iDel . ' deleted | ' . $iNew . ' inserted,' . sprintf( '%.2f', microtime( true ) - $_SERVER["REQUEST_TIME_FLOAT"] );
        logIt( $msg );
        return;
    }
}
