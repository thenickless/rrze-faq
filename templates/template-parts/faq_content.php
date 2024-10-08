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

echo '<div id="post-' . esc_attr(get_the_ID()) . '" class="' . esc_attr(implode(' ', get_post_class())) .'">';

?>

<h2 class="glossary-title" itemprop="title"><?php echo esc_html(get_the_title()); ?></h2>

<?php 

$postID = get_the_ID();
$cats = wp_kses_post(Layout::getTermLinks( $postID, 'faq_category' ));
$tags = wp_kses_post(Layout::getTermLinks( $postID, 'faq_tag' ));            

$details = '<article class="news-details"><p class="meta-footer">'
. ( $cats ? '<span class="post-meta-categories"> ' . esc_html__( 'Categories', 'rrze-faq' ) . ': ' . $cats . '</span>' : '' )
. ( $tags ? '<span class="post-meta-tags"> ' . esc_html__( 'Tags', 'rrze-faq' ) . ': ' . $tags . '</span>' : '' )
. '</p></article>';

the_content(); 
echo wp_kses_post($details);

echo '</div>';
