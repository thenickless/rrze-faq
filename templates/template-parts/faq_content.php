<?php
/**
 * This is part of the templates for displaying the FAQ
 *
 *
 * @package WordPress
 * @subpackage FAU
 * @since FAU 1.0
*/

namespace RRZE\FAQ;

use RRZE\FAQ\Layout;

$postID = get_the_ID();
$title = get_the_title();

$content .= '<div id="post-' . get_the_ID() . '" class="' . implode(' ', get_post_class()) .'">';
$content .= '[collapse title="' . $title . '"]';

$cats = Layout::getTermLinks( $postID, 'faq_category' );
$tags = Layout::getTermLinks( $postID, 'faq_tag' );            
$details = '<article class="news-details">
<!-- rrze-faq --><p id="rrze-faq" class="meta-footer">'
. ( $cats ? '<span class="post-meta-categories"> '. __( 'Categories', 'rrze-faq' ) . ': ' . $cats . '</span>' : '' )
. ( $tags ? '<span class="post-meta-tags"> '. __( 'Tags', 'rrze-faq' ) . ': ' . $tags . '</span>' : '' )
. '</p></article>';
$schema = '';
$source = get_post_meta( $postID, "source", TRUE );

if ( $source == 'website' ){
    $schema .= RRZE_SCHEMA_QUESTION_START . $title . RRZE_SCHEMA_QUESTION_END . RRZE_SCHEMA_ANSWER_START . wp_strip_all_tags( get_the_content(), TRUE ) . RRZE_SCHEMA_ANSWER_END;
}

$content .= get_the_content(); 
$content .= $details;


$content .= '[/collapse]';

$content .= '</div>';


