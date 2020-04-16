<?php

namespace RRZE\FAQ;

defined('ABSPATH') || exit;

use function RRZE\FAQ\Config\deleteLogfile;
use RRZE\FAQ\API;
use RRZE\FAQ\CPT;
use RRZE\FAQ\Cronjob;
use RRZE\FAQ\Layout;
use RRZE\FAQ\RESTAPI;
use RRZE\FAQ\Settings;
use RRZE\FAQ\Shortcode;


/**
 * Hauptklasse (Main)
 */
class Main {
    /**
     * Der vollständige Pfad- und Dateiname der Plugin-Datei.
     * @var string
     */
    protected $pluginFile;

    protected $settings;

    /**
     * Variablen Werte zuweisen.
     * @param string $pluginFile Pfad- und Dateiname der Plugin-Datei
     */
    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
    }

    /**
     * Es wird ausgeführt, sobald die Klasse instanziiert wird.
     */
    public function onLoaded() {
        add_action( 'wp_enqueue_scripts', [$this, 'enqueueScripts'] );
        // Actions: sync, add domain, delete domain, delete logfile
        add_action( 'update_option_rrze-faq', [$this, 'checkSync'] );
        add_filter( 'pre_update_option_rrze-faq',  [$this, 'switchTask'], 10, 1 );

        $cpt = new CPT(); 

        $this->settings = new Settings($this->pluginFile);
        $this->settings->onLoaded();

        $restAPI = new RESTAPI();
        $layout = new Layout();
        $cronjob = new Cronjob();
        $shortcode = new Shortcode();
    }


    /**
     * Enqueue der globale Skripte.
     */
    public function enqueueScripts() {
        // wp_register_style('rrze-faq', plugins_url('assets/css/plugin.css', plugin_basename($this->pluginFile)));
        wp_register_style('rrze-faq-styles', plugins_url('assets/css/rrze-faq.css', plugin_basename($this->pluginFile)));
    }





    /**
     * Click on buttons "sync", "add domain", "delete domain" or "delete logfile"
     */
    public function switchTask( $options ) {
        $api = new API();
        $domains = $api->getDomains();
        $tab = ( isset($_GET['doms'] ) ? 'doms' : ( isset( $_GET['sync'] ) ? 'sync' : ( isset( $_GET['del'] ) ? 'del' : '' ) ) );

        switch ( $tab ){
            case 'doms':
                if ( isset( $_POST['rrze-faq']['doms_new_url'] ) && $_POST['rrze-faq']['doms_new_url'] != '' ){
                    // add domain
                    $domains = $api->setDomain( $_POST['rrze-faq']['doms_new_name'], $_POST['rrze-faq']['doms_new_url'] );
                    if ( !$domains ){
                        $domains = $api->getDomains();
                        add_settings_error( 'doms_new_url', 'doms_new_error', $_POST['rrze-faq']['doms_new_url'] . ' is not valid.', 'error' );        
                    }
                    $options['doms_new_name'] = '';
                    $options['doms_new_url'] = '';
                } else {
                    // delete domain(s)
                    foreach ( $_POST as $key => $url ){
                        if ( substr( $key, 0, 11 ) === "del_domain_" ){
                            foreach( $options as $field => $val ){
                                if ( ( stripos( $field, 'sync_url' ) === 0 ) && ( $val == $url ) ){
                                    $parts = explode( '_', $field );
                                    $shortname = $parts[2];
                                    $api->deleteDomain( $shortname );
                                    unset( $options['sync_shortname_' . $shortname] );
                                    unset( $options['sync_url_' . $shortname] );
                                    unset( $options['sync_categories_' . $shortname] );
                                    unset( $options['sync_syncthis_' . $shortname] );
                                    unset( $options['sync_hr_' . $shortname] );
                                    if ( ( $key = array_search( $url, $domains ) ) !== false) {
                                        unset( $domains[$key] );
                                    }                                    
                                }
                            }   
                        }
                    }
                }    
            break;
            case 'del':
                deleteLogfile();
            break;
        }

        if ( !$domains ){
            unset( $options['registeredDomains'] );
        } else {
            $options['registeredDomains'] = $domains;
        }

        return $options;
    }

    public function checkSync() {
        if ( isset( $_GET['sync'] ) ){
            // $this->setCronjob();
            $sync = new Sync();
            $sync->doSync( 'manual' );
        }
    }

}
