<?php
/**
 * The template for displaying all FAQ
 *
 *
 * @package WordPress
 * @subpackage FAU
 * @since FAU 1.0
*/

include_once(WP_PLUGIN_DIR . '/rrze-faq/templates/template-parts/archive_head.php');

if ( have_posts() ) : while ( have_posts() ) : the_post();

include(WP_PLUGIN_DIR . '/rrze-faq/templates/template-parts/faq_content.php');

endwhile; endif;

include_once(WP_PLUGIN_DIR . '/rrze-faq/templates/template-parts/foot.php');
