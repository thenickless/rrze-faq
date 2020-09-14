<?php
/**
 * The template for displaying a single FAQ
 *
 *
 * @package WordPress
 * @subpackage FAU
 * @since FAU 1.0
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
<?php } ?>

<h1 id="droppoint" class="faq-title" itemprop="title"><?php the_title(); ?></h1>
<?php 

$postID = get_the_ID();
$cats = Layout::getTermLinks( $postID, 'faq_category' );
$tags = Layout::getTermLinks( $postID, 'faq_tag' );            
$details = '<article class="news-details">
<!-- rrze-faq --><p id="rrze-faq" class="meta-footer">'
. ( $cats ? '<span class="post-meta-categories"> '. __( 'Categories', 'rrze-faq' ) . ': ' . $cats . '</span>' : '' )
. ( $tags ? '<span class="post-meta-tags"> '. __( 'Tags', 'rrze-faq' ) . ': ' . $tags . '</span>' : '' )
. '</p></article>';
$schema = '';
$source = get_post_meta( $postID, "source", TRUE );

// to avoid duplicate content use schema if this is a FAQ from this website only
if ( $source == 'website' ){
    $schema = '<div style="display:none" itemscope itemtype="https://schema.org/FAQPage">';
    $schema .= '<div style="display:none" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">';
    $schema .= '<div style="display:none" itemprop="name">' . get_the_title() . '</div>';
    $schema .= '<div style="display:none" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">';
    $schema .= '<div style="display:none" itemprop="text">' . wp_strip_all_tags( get_the_content(), TRUE ) . '</div></div></div></div>';
}

the_content(); 
echo $details;
echo $schema;

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

