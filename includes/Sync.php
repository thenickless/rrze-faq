<?php

namespace RRZE\FAQ;

use RRZE\FAQ\API;
use function RRZE\FAQ\Config\logIt;


defined('ABSPATH') || exit;


class Sync {

    public function doSync( $mode ) {
        $max_exec_time = ini_get('max_execution_time') - 40; // ini_get('max_execution_time') is not the correct value perhaps due to load-balancer or proxy or other fancy things I've no clue of. But this workaround works for now.
        $iCnt = 0;
        $api = new API();
        $domains = $api->getDomains();
        foreach( $domains as $shortname => $url ){
            $url = $api->getUrl( $url );
            $options = get_option( 'rrze-faq' );
            if ( isset( $options['sync_mode_' . $shortname] ) ){
                $categories = ( isset( $options['sync_categories_' . $shortname] ) ? implode( ',', $options['sync_categories_' . $shortname] ) : '' );
                switch ( $options['sync_mode_' . $shortname] ){
                    case 'auto':
                        // set cronjob
                    case 'manual':
                        $iDel = $api->deleteFAQ( $shortname );
                        $iCnt = $api->setFAQ( $url, $categories, $shortname  );
                    break;
                }
            }
            // // check execution time to avoid a 402 error
            // $exec_time = (int) (microtime( true ) - $_SERVER["REQUEST_TIME_FLOAT"]);
            // if ( $exec_time >= $max_exec_time ){
            //     $sync_msg = __( 'There are still FAQ to be fetched. ' . $iCnt . ' FAQ updated. Please click on "Synchronize now" again to fetch the rest', 'rrze-faq' ) . ( isset( $options['otrs_auto_sync'] ) && $options['otrs_auto_sync'] == 'on' ? ' or wait for the automatically synchronization.' : '.' ) . ' Required time: ' . sprintf( '%.1f ', microtime( true ) - $_SERVER["REQUEST_TIME_FLOAT"] ) . __( 'seconds', 'rrze-faq' );
            //     date_default_timezone_set('Europe/Berlin');
            //     add_settings_error( 'Synchronization not completed', 'syncnotcompleted', $sync_msg, 'error' );
            //     settings_errors();
            //     logIt( date("Y-m-d H:i:s") . ' | ' . $sync_msg . ' | ' . $mode );
            //     return;
            // }
        }        

        date_default_timezone_set('Europe/Berlin');
        $sync_msg = 'Synchronization completed. ' . $iCnt . __( ' FAQ updated', 'rrze-faq' ) . '. Required time: ' . sprintf( '%.1f ', microtime( true ) - $_SERVER["REQUEST_TIME_FLOAT"] ) . __( 'seconds', 'rrze-faq' );
        add_settings_error( 'Synchronization completed', 'synccompleted', $sync_msg, 'success' );
        settings_errors();
        logIt( date("Y-m-d H:i:s") . ' | ' . $sync_msg . ' | ' . $mode );
        return;
    }
}
