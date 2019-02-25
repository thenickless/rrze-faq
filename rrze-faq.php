<?php

/**
 * Plugin Name:     RRZE-FAQ
 * Plugin URI:      https://github.com/RRZE-Webteam/rrze-faq.git
 * Description:     WordPress-Plugin: Shortcode zur Einbindung von eigenen FAQs, Synonymen oder Glossaren in Websites. Die Einträge können Site-Übergreifend mit anderen Websites des Netzwerks synchronisiert werden.
 * Version:         1.0.7
 * Author:          RRZE-Webteam
 * Author URI:      https://blogs.fau.de/webworking/
 * License:         GNU General Public License v2
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:     /languages
 * Text Domain:     rrze-faq
 */

namespace RRZE\Glossar\Server;

const RRZE_PHP_VERSION              = '7.0';
const RRZE_WP_VERSION               = '4.9';
    
add_action('plugins_loaded', 'RRZE\Glossar\Server\init');
add_action ('faqhook', 'RRZE\Glossar\Server\updateList');
add_action( 'wp_enqueue_scripts', 'RRZE\Glossar\Server\custom_libraries');
register_activation_hook(__FILE__, 'RRZE\Glossar\Server\activation');


function init() {
    textdomain();
    include_once('includes/posttype/rrze-faq-posttype.php');
    include_once('includes/posttype/rrze-faq-taxonomy.php');
    include_once('includes/posttype/rrze-faq-manage-posts.php');
    include_once('includes/posttype/rrze-faq-metabox.php');
    include_once('includes/posttype/rrze-faq-admin.php');
    include_once('includes/posttype/rrze-faq-helper.php');
    include_once('includes/REST-API/rrze-faq-rest-filter.php');
    include_once('includes/REST-API/rrze-faq-posttype-rest.php');
    include_once('includes/REST-API/rrze-faq-taxonomy-rest.php');
    include_once('includes/faq/rrze-faq-list-table-helper.php');
    include_once('includes/faq/rrze-faq-list-table.php');
    include_once('includes/domain/rrze-faq-domain-list.php');
    include_once('includes/domain/rrze-faq-domain-add.php');
    new AddFaqDomain();
    include_once('includes/domain/rrze-faq-domain-get.php');
    new DomainFaqWPListTable();
    include_once('includes/shortcode/rrze-glossary-shortcode.php');
}

function textdomain() {
    load_plugin_textdomain('rrze-faq', FALSE, sprintf('%s/languages/', dirname(plugin_basename(__FILE__))));
}

function activation() {
    textdomain();
    system_requirements();
    faq_cron();

}

function system_requirements() {
    $error = '';

    if (version_compare(PHP_VERSION, RRZE_PHP_VERSION, '<')) {
        $error = sprintf(__('Your server is running PHP version %s. Please upgrade at least to PHP version %s.', 'rrze-plugin-help'), PHP_VERSION, RRZE_PHP_VERSION);
    }

    if (version_compare($GLOBALS['wp_version'], RRZE_WP_VERSION, '<')) {
        $error = sprintf(__('Your Wordpress version is %s. Please upgrade at least to Wordpress version %s.', 'rrze-plugin-help'), $GLOBALS['wp_version'], RRZE_WP_VERSION);
    }

    // Wenn die Überprüfung fehlschlägt, dann wird das Plugin automatisch deaktiviert.
    if (!empty($error)) {
        deactivate_plugins(plugin_basename(__FILE__), FALSE, TRUE);
        wp_die($error);
    }
}

function custom_libraries() {
    wp_register_style( 'rrze-faq-styles', plugins_url( 'rrze-faq/assets/css/rrze-faq.css', dirname(__FILE__)));
    wp_register_script( 'rrze-faq-js', plugins_url( 'rrze-faq/assets/js/rrze-faq.min.js', dirname(__FILE__)), array('jquery'),'', true);
   
}

function faq_cron_schedules($schedules){
    if(!isset($schedules["15min"])){
        $schedules["15min"] = array(
            'interval' => 15*60,
            'display' => __('Once every 15 minutes'));
    }
    return $schedules;
}

add_filter('cron_schedules','RRZE\Glossar\Server\faq_cron_schedules');

function faq_cron() {
    if (!wp_next_scheduled( 'faqhook' )) {
      wp_schedule_event( time(), '15min', 'faqhook' );
    }
}

function updateList() {
    
    //delete_option('urls');
    //getSynonymsForWPListTable
    
    $faq_option = 'serverfaq';
    $faq = FaqListTableHelper::getGlossaryForWPListTable();
    
    if( get_option($faq_option) !== false) {
        update_option($faq_option, $faq);
    } else {
        $autoload = 'no';
        add_option ($faq_option, $faq);
    }
}