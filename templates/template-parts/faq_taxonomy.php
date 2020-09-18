<?php
/* 
Template Name: Part of the Custom Taxonomy Templates
*/

$cat_slug = get_queried_object()->slug;
$cat_name = get_queried_object()->name;

echo '<h2>'.$cat_name . '</h2>';

$tax_post_args = array(
    'post_type' => 'faq',
    'posts_per_page' => 999,
    'order' => 'ASC',
    'tax_query' => array(
        array(
            'taxonomy' => $taxonomy,
            'field' => 'slug',
            'terms' => $cat_slug
        )
    )
);
$tax_post_query = new WP_Query($tax_post_args);

if ($tax_post_query->have_posts()){
    echo '<ul>';
    while($tax_post_query->have_posts()){
        $tax_post_query->the_post();
        echo '<li><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></li>';
    }
    echo '</ul>';
}
    