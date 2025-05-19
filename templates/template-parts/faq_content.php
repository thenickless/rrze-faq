<?php
/**
 * This is part of the templates for displaying the FAQ
 *
 * @package WordPress
 * @subpackage FAU
 * @since FAU 1.0
 */

namespace RRZE\FAQ;

use RRZE\FAQ\Tools;

$postID = get_the_ID();
$random = wp_rand(); // In case there are multiple FAQs on the same page
$header_id = 'header-' . $postID . '-' . $random;

$cats = Tools::getTermLinks($postID, 'faq_category');
$tags = Tools::getTermLinks($postID, 'faq_tag');

$content = '';
$content .= '<article>';
$content .= '<header>';
$content .= '<h1 id="' . esc_attr($header_id) . '">' . esc_html(get_the_title()) . '</h1>';
$content .= '</header>';
$content .= apply_filters('the_content', get_the_content());
$content .= '<footer><p class="meta-footer">';

$terms = get_the_terms(get_the_ID(), 'faq_category');
if ($terms && !is_wp_error($terms)) {
    $primary_term = reset($terms);
    $linked_page_id = get_term_meta($primary_term->term_id, 'linked_page', true);
    if ($linked_page_id) {
        $linked_url = get_permalink($linked_page_id);
        $content .= '<p class="faq-linked-page"><a href="' . esc_url($linked_url) . '">';
        $content .= esc_html(get_the_title($linked_page_id)) . '</a></p>';
    }
}

if ($cats) {
    $content .= '<span class="post-meta-categories">' . esc_html__('Categories', 'rrze-faq') . ': ' . wp_kses_post($cats) . '</span> ';
}
if ($tags) {
    $content .= '<span class="post-meta-tags">' . esc_html__('Tags', 'rrze-faq') . ': ' . wp_kses_post($tags) . '</span>';
}
$content .= '</p></footer>';
$content .= '</article>';

$masonry = false;
$color = '';
$additional_class = '';

echo Tools::renderFaqWrapper($content, $header_id, $masonry, $color, $additional_class);

