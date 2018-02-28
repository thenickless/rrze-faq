<?php

namespace RRZE\Glossar\Server;

function fau_glossary_rte_add_buttons( $plugin_array ) {
    $plugin_array['glossaryrteshortcodes'] = get_template_directory_uri().'/js/tinymce-glossary.js';
    return $plugin_array;
}

add_filter( 'mce_external_plugins','RRZE\Glossar\Server\fau_glossary_rte_add_buttons');