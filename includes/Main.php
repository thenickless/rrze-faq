<?php

namespace RRZE\FAQ;

defined('ABSPATH') || exit;

use RRZE\FAQ\Settings;
use RRZE\FAQ\Shortcode;
use function RRZE\FAQ\Config\deleteLogfile;


/**
 * Hauptklasse (Main)
 */
class Main {
    /**
     * Der vollständige Pfad- und Dateiname der Plugin-Datei.
     * @var string
     */
    protected $pluginFile;

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
        // Auto-Sync
        add_action( 'rrze_faq_auto_update', [$this, 'runCronjob'] );
        // Editable FAQ (if synced: non-editable / if self-written (source == 'website'): editable)
        add_action( 'add_meta_boxes', [$this, 'add_content_box'] );
        add_action( 'edit_form_after_title', [$this, 'toggle_editor'] );
        add_filter( 'use_block_editor_for_post', [$this, 'gutenberg_post_meta'], 10, 2 );
        // Table "All FAQ"
        add_filter( 'manage_edit-faq_columns', [$this, 'faq_table_head'] );
        add_action( 'manage_faq_posts_custom_column', [$this, 'faq_table_content'], 10, 2 );
        add_filter( 'manage_edit-faq_sortable_columns', [$this, 'faq_sortable_columns'] );
        // Table "Category"
        add_filter( 'manage_edit-faq_category_columns', [$this, 'faq_table_head'] );
        add_filter( 'manage_faq_category_custom_column', [$this, 'faq_tax_table_content'], 10, 3 );
        add_filter( 'manage_edit-faq_category_sortable_columns', [$this, 'faq_tax_sortable_columns'] );
        // Table "Tags"
        add_filter( 'manage_edit-faq_tag_columns', [$this, 'faq_table_head'] );
        add_filter( 'manage_faq_tag_custom_column', [$this, 'faq_tax_table_content'], 10, 3 );
        add_filter( 'manage_edit-faq_tag_sortable_columns', [$this, 'faq_tax_sortable_columns'] );
 
        // Settings-Klasse wird instanziiert.
        $settings = new Settings($this->pluginFile);
        $settings->onLoaded();

        include_once( __DIR__ . '/posttype/rrze-faq-posttype.php' );
        include_once( __DIR__ . '/posttype/rrze-faq-taxonomy.php' );
        include_once( __DIR__ . '/posttype/rrze-faq-manage-posts.php' );
        include_once( __DIR__ . '/posttype/rrze-faq-metabox.php');
        include_once( __DIR__ . '/posttype/rrze-faq-admin.php' );
        include_once( __DIR__ . '/posttype/rrze-faq-helper.php' );
        include_once( __DIR__ . '/REST-API/rrze-faq-rest-filter.php' );

        // include_once( __DIR__ . '/REST-API/rrze-faq-posttype-rest.php' );
        // include_once( __DIR__ . '/faq/rrze-faq-list-table-helper.php' );
        // include_once( __DIR__ . '/faq/rrze-faq-list-table.php' );
        // include_once( __DIR__ . '/domain/rrze-faq-domain-get.php' );
        // include_once( __DIR__ . '/domain/rrze-faq-domain-list.php' );
        // new DOMAIN_FAQ();
        // include_once( __DIR__ . '/domain/rrze-faq-domain-add.php' );
        // new AddFaqDomain();

        // Shortcode wird eingebunden.
        include 'Shortcode.php';
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
     * Trigger editable of CPT faq: 
     * synced FAQ should not be editable
     * self-written FAQ have to be editable
     */
    public function gutenberg_post_meta( $can_edit, $post)  {
        return FALSE;
        // check settings from Plugin rrze-settings enable_classic_editor
        $settings = (array) get_option( 'rrze_settings' );
        if ( isset( $settings )){
            $settings = (array) $settings['writing'];
            if ( isset( $settings['enable_classic_editor'] ) && $settings['enable_classic_editor'] ) {
                return FALSE;
            }
        }

        $ret = TRUE;
        $source = get_post_meta( $post->ID, 'source', TRUE );
        if ( $source && $source != 'website' ) {
            $ret = FALSE;
        }
        return $ret;
    }
    public function toggle_editor( $post ) {
        if ( $post->post_type == 'faq' ) {
            $source = get_post_meta( $post->ID, "source", true );
            if ( $source && $source != 'website' ){
                remove_post_type_support( 'faq', 'title' );
                remove_post_type_support( 'faq', 'editor' );
                remove_meta_box( 'tagsdiv-faq_category', 'faq', 'side' );
                remove_meta_box( 'tagsdiv-faq_tag', 'faq', 'side' );
            } else {
                remove_meta_box( 'read_only_content_box', 'faq', 'normal' );
            }
        }
    }
    public function add_content_box() {
        add_meta_box(
            'read_only_content_box', // id, used as the html id att
            __( 'This FAQ cannot be edited because it is sychronized', 'rrze-faq'), // meta box title
            [$this, 'read_only_cb'], // callback function, spits out the content
            'faq', // post type or page. This adds to posts only
            'normal', // context, where on the screen
            'high' // priority, where should this go in the context
        );
    }
    public function read_only_cb( $post ) {
        $cats = implode( ', ', wp_get_post_terms( $post->ID,  'faq_category', array( 'fields' => 'names' ) ) );
        $tags = implode( ', ', wp_get_post_terms( $post->ID,  'faq_tag', array( 'fields' => 'names' ) ) );
        echo '<h1>' . $post->post_title . '</h1><br>' . apply_filters( 'the_content', $post->post_content ) . '<hr>' . ( $cats ? '<h3>' . __('Category', 'rrze-faq' ) . '</h3><p>' . $cats . '</p>' : '' ) . ( $tags ? '<h3>' . __('Tags', 'rrze-faq' ) . '</h3><p>' . $tags .'</p>' : '' );
    }

    /**
     * Adds sortable column "source" to tables "All FAQ", "Category" and "Tags"
     */
    public function faq_table_head( $columns ) {
        $columns['source'] = __( 'Source', 'rrze-faq' );
        return $columns;
    }
    public function faq_table_content( $column_name, $post_id ) {
        if( $column_name == 'source' ) {
            $source = get_post_meta( $post_id, 'source', true );
            echo $source;
        }
    }
    public function faq_sortable_columns( $columns ) {
        $columns['taxonomy-faq_category'] = 'taxonomy-faq_category';
        $columns['source'] = __( 'Source', 'rrze-faq' );;
        return $columns;
    }
    public function faq_tax_table_content( $content, $column_name, $term_id ) {
        if( $column_name == 'source' ) {
            $source = get_term_meta( $term_id, 'source', true );
            echo $source;
        }
    }
    public function faq_tax_sortable_columns( $columns ) {
        $columns['source'] = __( 'Source', 'rrze-faq' );
        return $columns;
    }



    /**
     * Click on buttons "sync", "add domain", "delete domain" or "delete logfile"
     */
    public function switchTask( $options ) {
        if ( isset( $_GET['doms'] ) ){
            if ( isset( $_POST['rrze-faq']['doms_new_url'] ) && $_POST['rrze-faq']['doms_new_url'] != '' && isset( $_POST['rrze-faq']['doms_new_name'] ) && $_POST['rrze-faq']['doms_new_name'] != '' ){
                $this->addDomain( $_POST['rrze-faq']['doms_new_url'], $_POST['rrze-faq']['doms_new_name'] );
                unset( $options['doms_new_url'] );
                unset( $options['doms_new_name'] );
            } else {
                foreach ( $_POST as $key => $val ){
                    if ( substr( $key, 0, 11 ) === "del_domain_" ){
                        $this->deleteDomain( $val );
                    }
                }
            }
        } elseif ( isset( $_GET['del'] ) ){
            deleteLogfile();
        }
        return $options;
    }

    public function checkSync() {
        if ( is_super_admin() ){
            if ( isset( $_GET['sync'] ) ){
                $this->setCronjob();
                $sync = new Sync();
                $sync->doSync( 'manual' );
            }
        }
    }

    public function deleteDomain( $name ){
        if ( $name ){
            $domains = get_option( 'registeredDomains' );
            update_option( 'registeredDomains', array_merge( array_diff_key( $domains, array( $name => '' ) ) ) );
        }
    }

    public function addDomain( $url, $name ) {
        $url = trailingslashit( preg_replace( "/^((http|https):\/\/)?/i", "https://", $url ) );
        $domains = get_option( 'registeredDomains' );

        if ( !$domains || in_array( $url, $domains ) === FALSE ) {
            // $content = wp_remote_get( $domain . 'wp-json/wp/v2/glossary?per_page=1' );
            $content = wp_remote_get( $url . 'wp-json/wp/v2/faq?per_page=1' );
            $status_code = wp_remote_retrieve_response_code( $content );
            if ( $status_code != 200 ) {
                add_settings_error( 'doms_new_url', 'doms_new_error', $url . ' is not valid.', 'error' );        
            } else {
                $domains = get_option( 'registeredDomains' );
                $domains[$name] = $url;
                update_option( 'registeredDomains', $domains );
            }
        }
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

    public function setCronjob() {
        $options = get_option( 'rrze-faq' );
        if ( isset( $options['otrs_auto_sync'] ) && $options['otrs_auto_sync'] != 'on' ) {
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
