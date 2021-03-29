<?php
/**
 * The template for displaying a single FAQ
 *
 *
 * @package WordPress
 * @subpackage FAU
 * @since FAU 1.0
*/

$content = '[collapsibles]';

include_once('template-parts/head.php');
include_once('template-parts/faq_content.php');

$content .= '[/collapsibles]';
echo do_shortcode($content);

include_once('template-parts/foot.php');
