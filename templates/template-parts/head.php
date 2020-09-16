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

$thisThemeGroup = Layout::getThemeGroup();

get_header();
if ($thisThemeGroup == 'fauthemes') {
    get_template_part('template-parts/hero', 'index'); 
?>

    <div id="content">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <main id="droppoint">
                        <h1 class="screen-reader-text"><?php echo __('Index','fau'); ?></h1>

<?php } elseif ($thisThemeGroup == 'rrzethemes') {

if (!is_front_page()) { ?>
    <div id="sidebar" class="sidebar">
        <?php get_sidebar('page'); ?>
    </div><!-- .sidebar -->
<?php } ?>

<div id="primary" class="content-area">
    <div id="content" class="site-content" role="main">

<?php }else{ ?>

<div id="sidebar" class="sidebar">

    <?php get_sidebar(); ?>

</div>
<div id="primary" class="content-area">
    <main id="main" class="site-main">

<?php }
