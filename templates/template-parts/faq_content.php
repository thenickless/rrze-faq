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

echo '<div id="post-' . get_the_ID() . '" class="' . implode(' ', get_post_class()) .'">';

?>



<h2 class="glossary-title" itemprop="title"><?php the_title(); ?></h2>


<?php 

$postID = get_the_ID();
$cats = Layout::getTermLinks( $postID, 'faq_category' );
$tags = Layout::getTermLinks( $postID, 'faq_tag' );            
$details = '<article class="news-details"><p class="meta-footer">'
. ( $cats ? '<span class="post-meta-categories"> '. __( 'Categories', 'rrze-faq' ) . ': ' . $cats . '</span>' : '' )
. ( $tags ? '<span class="post-meta-tags"> '. __( 'Tags', 'rrze-faq' ) . ': ' . $tags . '</span>' : '' )
. '</p></article>';

the_content(); 
echo $details;

echo '</div>';
