<?php
/**
 * This is part of the templates for displaying the FAQ
 *
 * @package WordPress
 * @subpackage FAU
 * @since FAU 1.0
 */

namespace RRZE\FAQ;

use RRZE\FAQ\Layout;

$postID = get_the_ID();
$random = wp_rand(); // In case there are multiple FAQs on the same page
$header_id = 'header-' . $postID . '-' . $random;

$cats = Layout::getTermLinks($postID, 'faq_category');
$tags = Layout::getTermLinks($postID, 'faq_tag');

echo '<div class="rrze-faq" aria-labeledby="' . esc_attr($header_id) . '">';
echo '<article>';
echo '<header>';
echo '<h1 id="' . esc_attr($header_id) . '">' . esc_html(get_the_title()) . '</h1>';
echo '</header>';

the_content();

echo '<footer><p class="meta-footer">';
if ($cats) {
    echo '<span class="post-meta-categories">' . esc_html__('Categories', 'rrze-faq') . ': ' . wp_kses_post($cats) . '</span> ';
}
if ($tags) {
    echo '<span class="post-meta-tags">' . esc_html__('Tags', 'rrze-faq') . ': ' . wp_kses_post($tags) . '</span>';
}
echo '</p></footer>';

echo '</article>';
echo '</div>';
