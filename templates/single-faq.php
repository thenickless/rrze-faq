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
<?php }

include(plugin_dir_path( __DIR__ ) .'templates/faq_content.php');
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

