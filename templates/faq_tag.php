<?php
/* 
Template Name: Custom Taxonomy faq_tag Template
*/
use RRZE\FAQ\Layout;

$bFAUTheme = Layout::isFAUTheme();

get_header();

if ($bFAUTheme) {
    get_template_part('template-parts/hero', 'index'); ?>
    <div id="content">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <main id="droppoint">
                        <h1 class="screen-reader-text"><?php echo __('Index','fau'); ?></h1>
<?php } else { ?>
    <div id="sidebar" class="sidebar">
        <?php get_sidebar(); ?>
    </div>
    <div id="primary" class="content-area">
		<main id="main" class="site-main">
<?php }

    $cat_slug = get_queried_object()->slug;
    $cat_name = get_queried_object()->name;

echo '<h2>'.$cat_name . '</h2>';

    $tax_post_args = array(
        'post_type' => 'faq',
        'posts_per_page' => 999,
        'order' => 'ASC',
        'tax_query' => array(
            array(
                'taxonomy' => 'faq_tag',
                'field' => 'slug',
                'terms' => $cat_slug
            )
        )
    );
    $tax_post_query = new WP_Query($tax_post_args);

    if ($tax_post_query->have_posts()) :
        echo '<ul>';
        while($tax_post_query->have_posts()) :
            $tax_post_query->the_post();
            echo '<li>';

?>
            <a href="<?php the_permalink(); ?>">
                 <?php the_title(); ?>
            </a>
<?php
            echo '</li>';
        endwhile;
       echo '</ul>';
    endif;

if ($bFAUTheme) { ?>
    </main>
</div>
</div>
</div>
</div>
<?php } else { ?>
</main>
</div>
<?php }
get_footer();

