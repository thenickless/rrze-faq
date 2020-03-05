<?php

namespace RRZE\FAQ\Server;

function rrze_faq_rte_add_buttons( $plugin_array ) {
    $plugin_array['faqrteshortcodes'] = get_template_directory_uri().'/js/tinymce-glossary.js';
    return $plugin_array;
}

// add_filter( 'mce_external_plugins','RRZE\FAQ\Server\rrze_faq_rte_add_buttons');