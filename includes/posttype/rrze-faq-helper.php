<?php

namespace RRZE\Glossar\Server;

function fau_glossary_rte_add_buttons( $plugin_array ) {
    $plugin_array['glossaryrteshortcodes'] = plugins_url('../../assets/js/tinymce-glossary.js', plugin_basename(__FILE__));
    return $plugin_array;
}

add_filter( 'mce_external_plugins','RRZE\Glossar\Server\fau_glossary_rte_add_buttons');