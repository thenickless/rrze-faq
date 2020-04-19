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
        $options = get_option( 'rrze-faq' );
        foreach( $domains as $shortname => $url ){
            if ( isset( $options['sync_mode_' . $shortname] ) ){
                $categories = ( isset( $options['sync_categories_' . $shortname] ) ? implode( ',', $options['sync_categories_' . $shortname] ) : '' );
                switch ( $options['sync_mode_' . $shortname] ){
                    case 'auto':
                    case 'manual':
                        $tStart = microtime( TRUE );
                        $aCnt = $api->setFAQ( $url, $categories, $shortname  );
                        $tEND = microtime( TRUE );
                        $sync_msg = $shortname . ': ' . __( 'Synchronization completed.', 'rrze-faq' ) . ' ' . $aCnt['iNew'] . ' ' . __( 'new', 'rrze-faq' ) . ', ' . $aCnt['iUpdated'] . ' ' . __( ' updated', 'rrze-faq' ) . ' ' . __( 'and', 'rrze-faq' ) . ' ' . $aCnt['iDeleted'] . ' ' . __( 'deleted', 'rrze-faq' ) . '.';
                        add_settings_error( 'Synchronization completed', 'synccompleted', $sync_msg, 'success' );
                        logIt( date("Y-m-d H:i:s") . ' | ' . $sync_msg . ' | ' . $mode );
                    break;
                }
            }
        }        

        date_default_timezone_set('Europe/Berlin');
        $sync_msg = __( 'All synchronizations completed', 'rrze-faq' ) . '. ' . __('Required time:', 'rrze-faq') . ' ' . sprintf( '%.1f ', microtime( true ) - $_SERVER["REQUEST_TIME_FLOAT"] ) . __( 'seconds', 'rrze-faq' );
        add_settings_error( 'Synchronization completed', 'synccompleted', $sync_msg, 'success' );
        settings_errors();
        logIt( date("Y-m-d H:i:s") . ' | ' . $sync_msg . ' | ' . $mode );
        return;
    }
}
