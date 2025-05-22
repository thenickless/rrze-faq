<?php

namespace RRZE\FAQ;

defined('ABSPATH') || exit;

use function RRZE\FAQ\Config\getShortcodeSettings;
use RRZE\FAQ\Tools;


/**
 * Shortcode
 */
class Shortcode
{

    /**
     * Settings-Objekt
     * @var object
     */
    private $settings = '';
    private $pluginname = '';

    public function __construct()
    {
        $this->settings = getShortcodeSettings();
        $this->pluginname = $this->settings['block']['blockname'];
        // add_shortcode( 'fau_glossar', [ $this, 'shortcodeOutput' ]); // BK 2020-06-05 Shortcode [fau_glossar ...] is moved to its own plugin rrze-glossary, because for historical reasons incompatible code exists in FAU institutions, which was not known when rrze-faq was rebuilt
        // add_shortcode( 'glossary', [ $this, 'shortcodeOutput' ]); // BK 2020-06-05 Shortcode [glossary ...] is outsourced to its own plugin rrze-glossary, because for historical reasons incompatible code exists in FAU facilities, which was not known when rrze-faq was rebuilt

        add_shortcode('faq', [$this, 'shortcodeOutput']);
        add_action('admin_head', [$this, 'setMCEConfig']);
        add_filter('mce_external_plugins', [$this, 'addMCEButtons']);
    }

    /**
     * Translates composite shortcode attributes into individual properties
     * 
     * Splits the values of attributes such as “glossary”, “hide”, “show” and “class” into sub-terms 
     * and assigns these to logical individual fields in the attribute array. This simplifies further internal processing.
     * 
     * @param array $atts Reference to the shortcode attribute array
     * @return void
     */
    private function translateNewAttributes(array &$atts): void
    {
        // translate new attributes
        if (isset($atts['glossary'])) {
            $parts = explode(' ', $atts['glossary']);
            foreach ($parts as $part) {
                $part = trim($part);
                switch ($part) {
                    case 'category':
                    case 'tag':
                        $atts['glossary'] = $part;
                        break;
                    case 'a-z':
                    case 'tabs':
                    case 'tagcloud':
                        $atts['glossarystyle'] = $part;
                        break;
                }
            }
        }

        if (isset($atts['hide'])) {
            $parts = explode(' ', $atts['hide']);
            foreach ($parts as $part) {
                $part = trim($part);
                switch ($part) {
                    case 'title':
                        $atts['hide_title'] = true;
                        break;
                    case 'accordion':
                    case 'accordeon':
                        $atts['hide_accordion'] = true;
                        break;
                    case 'glossary':
                        $atts['glossarystyle'] = '';
                        break;
                }
            }
        }

        if (isset($atts['show'])) {
            $parts = explode(' ', $atts['show']);
            foreach ($parts as $part) {
                $part = trim($part);
                switch ($part) {
                    case 'expand-all-link':
                        $atts['expand_all_link'] = ' expand-all-link="true"';
                        break;
                    case 'load-open':
                        $atts['load_open'] = ' load="open"';
                        break;
                }
            }
        }

        $atts['additional_class'] = isset($atts['additional_class']) ? $atts['additional_class'] : '';
        if (isset($atts['class'])) {
            $parts = explode(' ', $atts['class']);
            foreach ($parts as $part) {
                $part = trim($part);
                switch ($part) {
                    case 'med':
                    case 'nat':
                    case 'phil':
                    case 'rw':
                    case 'tk':
                        $atts['color'] = $part;
                        break;
                    default:
                        $atts['additional_class'] .= ' ' . $part;
                        break;
                }
            }
        }

        $atts['sort'] = (isset($atts['sort']) && ($atts['sort'] == 'title' || $atts['sort'] == 'id' || $atts['sort'] == 'sortfield')) ? $atts['sort'] : 'title';

        $atts['expand_all_link'] = (isset($atts['expand_all_link']) && $atts['expand_all_link'] ? ' expand-all-link="true"' : '');
        $atts['load_open'] = (isset($atts['load_open']) && $atts['load_open'] ? ' load="open"' : '');
    }





    /**
     * Outputs explicitly requested FAQs as accordion or simple content.
     *
     * Supports both Gutenberg blocks (multiple IDs as an array) and the classic editor (comma-separated).
     *
     * @param mixed $id Single ID or array of IDs
     * @param bool $gutenberg Whether Gutenberg is used
     * @param int $hstart HTML heading level
     * @param string $style Inline styles for the accordion
     * @param bool $masonry Whether tiles should be displayed (fake masonry - see https://github.com/RRZE-Webteam/rrze-faq/issues/105#issuecomment-2873361435 )
     * @param string $expand_all_link Attribute for “expand all” link
     * @param bool $hide_accordion Whether the accordion should be suppressed
     * @param bool $hide_title Whether the title should be suppressed
     * @param string $color Color attribute of the accordion
     * @param string $load_open Attribute for open state
     * @param string &$schema Is supplemented by generated JSON-LD schema
     * @return string The generated HTML content
     */


    private function renderExplicitFAQs($id, bool $gutenberg, int $hstart, string $style, bool $masonry, string $expand_all_link, bool $hide_accordion, bool $hide_title, string $color, string $load_open, string &$schema): string
    {
        $content = '';

        // EXPLICIT FAQ(s)
        if ($gutenberg) {
            $aIDs = $id;
        } else {
            // classic editor
            $aIDs = explode(',', $id);
        }

        $found = false;

        foreach ($aIDs as $id) {
            $id = trim($id);
            if ($id) {
                $title = get_the_title($id);
                $anchorfield = get_post_meta($id, 'anchorfield', true);

                if (empty($anchorfield)) {
                    $anchorfield = 'ID-' . $id;
                }

                $description = str_replace(']]>', ']]&gt;', apply_filters('the_content', get_post_field('post_content', $id)));
                if (!isset($description) || (mb_strlen($description) < 1)) {
                    $description = get_post_meta($id, 'description', true);
                }

                if ($hide_accordion) {
                    $content .= ($hide_title ? '' : '<h' . $hstart . '>' . $title . '</h' . $hstart . '>') .
                        ($description ? '<p>' . $description . '</p>' : '');
                } else {
                    if ($description) {
                        $content .= '<details' . ($load_open ? ' open' : '') . ' id="' . esc_attr($anchorfield) . '" class="faq-item' . ($color ? ' color-' . esc_attr($color) : '') . '">';
                        $content .= '<summary>' . esc_html($title) . '</summary>';
                        $content .= '<div class="faq-content">' . $description . '</div>';
                        $content .= '</details>';

                        $schema .= Tools::getSchema($id, $title, $description);
                    }
                }

                $found = true;
            }
        }

        return $content;
    }


    /**
     * Outputs FAQs based on taxonomies (category/tag) or glossary view.
     * 
     * Supports classic and alphabetical output, tabs or tag cloud display.
     * 
     * @param array $atts Original shortcode attributes
     * @param int $hstart HTML heading level
     * @param string $style Inline styles for the accordion
     * @param string $expand_all_link Attribute for “expand all” link
     * @param bool $hide_accordion Whether the accordion should be suppressed
     * @param bool $hide_title Whether the title should be suppressed
     * @param string $color Color attribute
     * @param string $load_open Attribute for open state
     * @param string $sort Sort criterion (title, id, sortfield)
     * @param string $order Sort order
     * @param mixed $category Category(ies) as string or array
     * @param mixed $tag Tag(s) as string or array
     * @param string $glossary “category” or “tag”
     * @param string $glossarystyle “a-z”, “tabs”, “tagcloud” or empty
     * @param string &$schema Reference to the schema markup
     * @return string Rendered HTML content
     */
    private function renderFilteredFAQs(array $atts, int $hstart, string $style, string $expand_all_link, bool $hide_accordion, bool $hide_title, string $color, string $load_open, string $sort, string $order, $category, $tag, string $glossary, string $glossarystyle, string &$schema): string
    {
        $content = '';

        // attribute category or tag is given or none of them
        $aLetters = array();
        $tax_query = '';

        $postQuery = array('post_type' => 'faq', 'post_status' => 'publish', 'numberposts' => -1, 'suppress_filters' => false);
        if ($sort == 'sortfield') {
            $postQuery['orderby'] = array(
                'meta_value' => $order,
                'title' => $order,
            );
            $postQuery['meta_key'] = 'sortfield';
        } else {
            $postQuery['orderby'] = $sort;
            $postQuery['order'] = $order;
        }

        // filter by category and/or tag and -if given- by domain related to category/tag, too
        $aTax = [];
        $aTax['faq_category'] = Tools::getTaxBySource($category);
        $aTax['faq_tag'] = Tools::getTaxBySource($tag);
        $aTax = array_filter($aTax); // delete empty entries

        if ($aTax) {
            $tax_query = Tools::getTaxQuery($aTax);
            if ($tax_query) {
                $postQuery['tax_query'] = $tax_query;
            }
        }

        $metaQuery = [];
        $lang = $atts['lang'] ? trim($atts['lang']) : '';
        if ($lang) {
            $metaQuery[] = [
                'key' => 'lang',
                'value' => $lang,
                'compare' => '=',
            ];
        }

        $source = !empty($atts['domain']) ?
            array_filter(array_map('trim', explode(',', $atts['domain']))) :
            [];
        if ($source) {
            $metaQuery[] = [
                'key' => 'source',
                'value' => $source,
                'compare' => 'IN',
            ];
        }

        if ($metaQuery) {
            $postQuery['meta_query'] = array_merge([
                'relation' => 'AND'
            ], $metaQuery);
        }
        // error_log(print_r($postQuery, true));

        $posts = get_posts($postQuery);

        if ($posts) {
            if ($glossary) {
                // attribut glossary is given
                // get all used tags or categories
                $aUsedTerms = array();
                $aPostIDs = array();
                foreach ($posts as $post) {
                    // get all tags for each post
                    $aTermIds = array();
                    $valid_term_ids = array();
                    if ($glossary == 'category' && $category) {
                        if (!is_array($category)) {
                            $aCats = array_map('trim', explode(',', $category));
                        } else {
                            $aCats = $category;
                        }
                        foreach ($aCats as $slug) {
                            $filter_term = get_term_by('slug', $slug, 'faq_category');
                            if ($filter_term) {
                                $valid_term_ids[] = $filter_term->term_id;
                            }
                        }
                    } elseif ($glossary == 'tag' && $tag) {
                        if (!is_array($tag)) {
                            $aTags = array_map('trim', explode(',', $tag));
                        } else {
                            $aTags = $tag;
                        }
                        foreach ($aTags as $slug) {
                            $filter_term = get_term_by('slug', $slug, 'faq_tag');
                            if ($filter_term) {
                                $valid_term_ids[] = $filter_term->term_id;
                            }
                        }
                    }
                    $terms = wp_get_post_terms($post->ID, 'faq_' . $glossary);
                    if ($terms) {
                        foreach ($terms as $t) {
                            if ($valid_term_ids && in_array($t->term_id, $valid_term_ids) === false) {
                                continue;
                            }
                            $aTermIds[] = $t->term_id;
                            $letter = Tools::getLetter($t->name);
                            $aLetters[$letter] = true;
                            $aUsedTerms[$t->name] = array('letter' => $letter, 'ID' => $t->term_id);
                            $aPostIDs[$t->term_id][] = $post->ID;
                        }
                    }
                }
                ksort($aUsedTerms);
                $anchor = 'ID';
                if ($aLetters) {
                    switch ($glossarystyle) {
                        case 'a-z':
                            $content = Tools::createAZ($aLetters);
                            $anchor = 'letter';
                            break;
                        case 'tabs':
                            $content = Tools::createTabs($aUsedTerms, $aPostIDs);
                            break;
                        case 'tagcloud':
                            $content = Tools::createTagCloud($aUsedTerms, $aPostIDs);
                            break;
                    }
                }

                $last_anchor = '';
                foreach ($aUsedTerms as $k => $aVal) {
                    if ($glossarystyle == 'a-z' && $content) {
                        $content .= ($last_anchor != $aVal[$anchor] ? '<h2 id="' . $anchor . '-' . $aVal[$anchor] . '">' . esc_html($aVal[$anchor]) . '</h2>' : '');
                    }

                    $term_id_attr = $anchor . '-' . $aVal[$anchor];
                    $content .= '<details' . ($load_open ? ' open' : '') . ' id="' . esc_attr($term_id_attr) . '" class="faq-term' . ($color ? ' color-' . esc_attr($color) : '') . '">';
                    $content .= '<summary>' . esc_html($k) . '</summary>';
                    $content .= '<div class="faq-term-content">';

                    // find the postIDs to this tag
                    $aIDs = Tools::searchArrayByKey($aVal['ID'], $aPostIDs);

                    foreach ($aIDs as $ID) {
                        $tmp = str_replace(']]>', ']]&gt;', apply_filters('the_content', get_post_field('post_content', $ID)));
                        if (!isset($tmp) || (mb_strlen($tmp) < 1)) {
                            $tmp = get_post_meta($ID, 'description', true);
                        }
                        $title = get_the_title($ID);

                        $anchorfield = get_post_meta($ID, 'anchorfield', true);
                        if (empty($anchorfield)) {
                            $anchorfield = 'innerID-' . $ID;
                        }



                        // HERE WE ARE

                        $content .= '<details id="' . esc_attr($anchorfield) . '" class="faq-item">';
                        $content .= '<summary>' . esc_html($title) . '</summary>';
                        $content .= '<div class="faq-content">' . $tmp . '</div>';
                        $content .= '</details>';

                        $schema .= Tools::getSchema($ID, $title, $tmp);
                    }

                    $content .= '</div></details>';
                    $last_anchor = $aVal[$anchor];
                }
            } else {
                // attribut glossary is not given
                $last_anchor = '';
                foreach ($posts as $post) {

                    $title = get_the_title($post->ID);
                    $letter = Tools::getLetter($title);
                    $aLetters[$letter] = true;

                    $tmp = str_replace(']]>', ']]&gt;', apply_filters('the_content', get_post_field('post_content', $post->ID)));
                    if (!isset($tmp) || (mb_strlen($tmp) < 1)) {
                        $tmp = get_post_meta($post->ID, 'description', true);
                    }

                    if (!$hide_accordion) {
                        $anchorfield = get_post_meta($post->ID, 'anchorfield', true);

                        if (empty($anchorfield)) {
                            $anchorfield = 'ID-' . $post->ID;
                        }

                        if ($glossarystyle == 'a-z' && count($posts) > 1) {
                            $content .= ($last_anchor != $letter ? '<h2 id="letter-' . $letter . '">' . $letter . '</h2>' : '');
                        }
                        $content .= '<details' . ($load_open ? ' open' : '') . ' id="' . esc_attr($anchorfield) . '" class="faq-item' . ($color ? ' color-' . esc_attr($color) : '') . '">';
                        $content .= '<summary>' . esc_html($title) . '</summary>';
                        $content .= '<div class="faq-content">' . $tmp . '</div>';
                        $content .= '</details>';

                    } else {
                        $content .= ($hide_title ? '' : '<h' . $hstart . '>' . $title . '</h' . $hstart . '>') . ($tmp ? '<p>' . $tmp . '</p>' : '');
                    }
                    $schema .= Tools::getSchema($post->ID, $title, $tmp);
                    $last_anchor = $letter;
                }

            }
        }

        return $content;
    }


    /**
     * Generate the shortcode output
     * @param array $atts Shortcode attributes
     * @param string $content Enclosed content
     * @return string Return the content
     */
    public function shortcodeOutput($atts, $content = null, $shortcode_tag = '')
    {
        // Workaround - see: https://github.com/RRZE-Webteam/rrze-faq/issues/132#issuecomment-2839668060
        if (($skip = Tools::preventGutenbergDoubleBracketBug($shortcode_tag)) !== false) {
            return $skip;
        }

        if (empty($atts)) {
            $atts = array();
        } else {
            $atts = array_map('sanitize_text_field', $atts);
        }

        $this->translateNewAttributes($atts);

        // merge given attributes with default ones
        $atts_default = array();
        foreach ($this->settings as $k => $v) {
            if ($k != 'block') {
                $atts_default[$k] = $v['default'];
            }
        }

        $atts = shortcode_atts($atts_default, $atts);
        extract($atts);

        $content = '';
        $schema = '';
        $glossarystyle = (isset($glossarystyle) ? $glossarystyle : '');
        $hide_title = (isset($hide_title) ? $hide_title : false);
        $color = (isset($color) ? $color : '');
        $style = (isset($style) ? 'style="' . $style . '"' : '');

        $gutenberg = (is_array($id) ? true : false);

        if ($id && (!$gutenberg || $gutenberg && $id[0])) {
            $content = $this->renderExplicitFAQs($id, $gutenberg, $hstart, $style, $masonry, $expand_all_link, $hide_accordion, $hide_title, $color, $load_open, $schema);
        } else {
            $content = $this->renderFilteredFAQs($atts, $hstart, $style, $expand_all_link, $hide_accordion, $hide_title, $color, $load_open, $sort, $order, $category, $tag, $glossary, $glossarystyle, $schema);
        }

        if ($schema) {
            $content .= RRZE_SCHEMA_START . $schema . RRZE_SCHEMA_END;
        }

        // 2020-05-12 THIS IS NOT IN USE because f.e. [faq glossary="category"] led to errors ("TypeError: e.$slides is null slick.min.js" and "TypeError: can't access property "add"" ) as FAQ can have >1 category and so equal sliders would be returned in output which leads to JS errors that avoid accordeons to work properly
        // => sliders are not syncable / this info is provided to the user during Sync and in Logfile
        // check if theme 'FAU-Einrichtungen' and [gallery ...] is in use
        // if ( ( wp_get_theme()->Name == 'FAU-Einrichtungen' ) && ( strpos( $content, 'slider') !== false ) ) {
        //     wp_enqueue_script( 'fau-js-heroslider' );
        // }

        $postID = get_the_ID();
        $headerID = Tools::getHeaderID($postID);

        wp_enqueue_style('rrze-faq-css');

        $content = Tools::renderFAQWrapper($content, $headerID, $masonry, $color, $additional_class);

        return $content;

    }




    public function setMCEConfig()
    {
        $shortcode = '';
        foreach ($this->settings as $att => $details) {
            if ($att != 'block') {
                $shortcode .= ' ' . $att . '=""';
            }
        }
        $shortcode = '[' . $this->pluginname . ' ' . $shortcode . ']';
        ?>
        <script type='text/javascript'>
            tmp = [{
                'name': <?php echo wp_json_encode($this->pluginname); ?>,
                'title': <?php echo wp_json_encode($this->settings['block']['title']); ?>,
                'icon': <?php echo wp_json_encode($this->settings['block']['tinymce_icon']); ?>,
                'shortcode': <?php echo wp_json_encode($shortcode); ?>,
            }];
            phpvar = (typeof phpvar === 'undefined' ? tmp : phpvar.concat(tmp));
        </script>
        <?php
    }

    public function addMCEButtons($pluginArray)
    {
        if (current_user_can('edit_posts') && current_user_can('edit_pages')) {
            $pluginArray['rrze_shortcode'] = plugins_url('../assets/js/tinymce-shortcodes.js', plugin_basename(__FILE__));
        }
        return $pluginArray;
    }
}
