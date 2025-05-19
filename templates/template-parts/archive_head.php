<?php
/**
 * The template for displaying a single FAQ
 *
 *
 * @package WordPress
 * @subpackage FAU
 * @since FAU 1.0
*/

// 5.3.14 - Removed theme-specific templates for switches. Current and upcoming theme versions no longer require them. See https://github.com/RRZE-Webteam/rrze-faq/issues/131

// use RRZE\FAQ\Layout;
// $thisThemeGroup = Layout::getThemeGroup();

get_header();

// if ($thisThemeGroup == 'fauthemes') {
//     $currentTheme = wp_get_theme();		
//     $vers = $currentTheme->get( 'Version' );
//     if (version_compare($vers, "2.3", '<')) {      
//         get_template_part('template-parts/hero', 'index'); 
//     }
?>

<!--
    <div id="content">
        <div class="content-container">
            <div class="post-row">
                <main class="col-xs-12">
                    <h1 class="screen-reader-text"><?php // echo esc_html__('Index','fau'); ?></h1>
-->

<?php 
// } elseif ($thisThemeGroup == 'rrzethemes') {

//     if (!is_front_page()) { ?>
<!--
        <div id="sidebar" class="sidebar">
            <?php // get_sidebar('page'); ?>
        </div>
-->
    <?php // } ?>
<!--
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
-->

<?php // }else{ ?>

    <div id="sidebar" class="sidebar">
        <?php get_sidebar(); ?>
    </div>
    <div id="primary" class="content-area">
        <main id="main" class="site-main">

<?php // } ?>
