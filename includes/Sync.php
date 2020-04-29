<?php

namespace RRZE\FAQ;

use RRZE\FAQ\API;
use function RRZE\FAQ\Config\logIt;


defined('ABSPATH') || exit;


class Sync {

    public function doSync( $mode ) {
        date_default_timezone_set('Europe/Berlin');
        $max_exec_time = ini_get('max_execution_time') - 40; // ini_get('max_execution_time') is not the correct value perhaps due to load-balancer or proxy or other fancy things I've no clue of. But this workaround works for now.
        $iCnt = 0;
        $api = new API();
        $domains = $api->getDomains();
        $options = get_option( 'rrze-faq' );
        $allowSettingsError = ( $mode == 'manual' ? TRUE : FALSE );
        $syncRan = FALSE;
        foreach( $domains as $shortname => $url ){
            if ( isset( $options['faqsync_mode_' . $shortname] ) ){
                $categories = ( isset( $options['faqsync_categories_' . $shortname] ) ? implode( ',', $options['faqsync_categories_' . $shortname] ) : '' );
                switch ( $options['faqsync_mode_' . $shortname] ){
                    case 'auto':
                    case 'manual':
                        $tStart = microtime( TRUE );
                        $aCnt = $api->setFAQ( $url, $categories, $shortname  );
                        $syncRan = TRUE;
                        $tEND = microtime( TRUE );
                        $sync_msg = __( 'Domain', 'rrze-faq' ) . ' "' . $shortname . '": ' . __( 'Synchronization completed.', 'rrze-faq' ) . ' ' . $aCnt['iNew'] . ' ' . __( 'new', 'rrze-faq' ) . ', ' . $aCnt['iUpdated'] . ' ' . __( ' updated', 'rrze-faq' ) . ' ' . __( 'and', 'rrze-faq' ) . ' ' . $aCnt['iDeleted'] . ' ' . __( 'deleted', 'rrze-faq' ) . '.';
                        if ( $allowSettingsError ){
                            add_settings_error( 'Synchronization completed', 'synccompleted', $sync_msg, 'success' );
                        }
                        logIt( $sync_msg . ' | ' . $mode );
                    break;
                }
            }
        }        

        if ( $syncRan ){
            $sync_msg = __( 'All synchronizations completed', 'rrze-faq' ) . '. ' . __('Required time:', 'rrze-faq') . ' ' . sprintf( '%.1f ', microtime( true ) - $_SERVER["REQUEST_TIME_FLOAT"] ) . __( 'seconds', 'rrze-faq' );
        } else {
            $sync_msg = __( 'TEST Settings updated', 'rrze-faq' );
        }
        if ( $allowSettingsError ){
            add_settings_error( 'Synchronization completed', 'synccompleted', $sync_msg, 'success' );
            settings_errors();
        }
        logIt( $sync_msg . ' | ' . $mode );
        return;
    }
}
