<?php

namespace RRZE\FAQ;

defined( 'ABSPATH' ) || exit;

/**
 * Cronjob for "faq"
 */
class Cronjob {

    public function __construct() {
        // Auto-Sync
        add_action( 'rrze_faq_auto_update', [$this, 'runCronjob'] );
    }

    public function runCronjob() {
        // Wochentags, tagsüber 8-18 Uhr alle 3 Stunden, danach und am Wochenende: Alle 6 Stunden
        $sync = [
            'workdays' => [ 2, 8, 11, 14, 17, 20 ],
            'weekend' => [ 6, 12, 18, 0 ] 
        ];

        date_default_timezone_set('Europe/Berlin');
        $today = getdate();
        $weekday = $today["wday"]; // 0=sunday
        $hour = $today["hours"]; // 0 - 23

        // Sync hourly for testing
        if ( $weekday > 0 && $weekday < 6 ){
            if ( in_array( $hour, $sync["workdays"] ) ) {
                $sync = new Sync();
                $sync->doSync( 'automatic' );
            }
        } else {
            if ( in_array( $hour, $sync["weekend"] ) ) {
                $sync = new Sync();
                $sync->doSync( 'automatic' );
            }
        }
    }

    public function setCronjob( $activate ) {
        if ( !$activate ) {
            if ( wp_next_scheduled( 'rrze_faq_auto_update' ) ) {
                wp_clear_scheduled_hook( 'rrze_faq_auto_update' );
            }
            return;
        }

        //Use wp_next_scheduled to check if the event is already scheduled*/
        if( !wp_next_scheduled( 'rrze_faq_auto_update' )) {
            date_default_timezone_set( 'Europe/Berlin' );
            wp_schedule_event( strtotime( 'today 13:00' ), 'hourly', 'rrze_faq_auto_update' );
            $timestamp = wp_next_scheduled( 'rrze_faq_auto_update' );
            if ($timestamp) {
                $message = __( 'Settings saved', 'rrze-faq' )
                    . '<br />'
                    . __( 'Next automatically synchronization:', 'rrze-faq' ) . ' '
                    . get_date_from_gmt( date( 'Y-m-d H:i:s', $timestamp ), 'd.m.Y - H:i' );
                add_settings_error( 'AutoSyncComplete', 'autosynccomplete', $message , 'updated' );
                settings_errors();
            }
        }
    }

}
