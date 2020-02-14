<?php

namespace RRZE\FAQ;

defined('ABSPATH') || exit;

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
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);

        // Settings-Klasse wird instanziiert.
        $settings = new Settings($this->pluginFile);
        $settings->onLoaded();

        // Shortcode-Klasse wird instanziiert.
        $shortcode = new Shortcode($this->pluginFile, $settings);
        $shortcode->onLoaded();

        // Custom Post Types werden instanziiert.
        // var_dump(__DIR__ );
        // var_dump($this->pluginFile);
        // var_dump(__FILE__);
        // exit;
        include_once( __DIR__ . '/posttype/rrze-faq-posttype.php' );
        include_once( __DIR__ . '/posttype/rrze-faq-taxonomy.php' );
        include_once( __DIR__ . '/posttype/rrze-faq-manage-posts.php' );
        include_once( __DIR__ . '/posttype/rrze-faq-metabox.php');
        include_once( __DIR__ . '/posttype/rrze-faq-admin.php' );
        include_once( __DIR__ . '/posttype/rrze-faq-helper.php' );
        include_once( __DIR__ . '/REST-API/rrze-faq-rest-filter.php' );
        include_once( __DIR__ . '/REST-API/rrze-faq-posttype-rest.php' );
        include_once( __DIR__ . '/REST-API/rrze-faq-taxonomy-rest.php' );
        include_once( __DIR__ . '/faq/rrze-faq-list-table-helper.php' );
        include_once( __DIR__ . '/faq/rrze-faq-list-table.php' );
        include_once( __DIR__ . '/domain/rrze-faq-domain-get.php' );
        include_once( __DIR__ . '/domain/rrze-faq-domain-list.php' );
        new DOMAIN_FAQ();
        // new DomainFaqWPListTable();
        include_once( __DIR__ . '/domain/rrze-faq-domain-add.php' );
        new AddFaqDomain();
        // include_once( __DIR__ . '/shortcode/rrze-glossary-shortcode.php' );
    }

    /**
     * Enqueue der globale Skripte.
     */
    public function enqueueScripts() {
        wp_register_style('rrze-faq', plugins_url('assets/css/plugin.css', plugin_basename($this->pluginFile)));
    }
}
