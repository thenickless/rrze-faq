<?php

namespace RRZE\FAQ;

defined('ABSPATH') || exit;

use function RRZE\FAQ\Config\getShortcodeSettings;

$settings;

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
        // add_shortcode( 'fau_glossar', [ $this, 'shortcodeOutput' ]); // BK 2020-06-05 Shortcode [fau_glossar ...] wird in eigenes Plugin rrze-glossary ausgelagert, weil aus historischen Gründen inkompatibler Code in FAU-Einrichtungen besteht, was beim Umbau von rrze-faq nicht bekannt war
        // add_shortcode( 'glossary', [ $this, 'shortcodeOutput' ]); // BK 2020-06-05 Shortcode [glossary ...] wird in eigenes Plugin rrze-glossary ausgelagert, weil aus historischen Gründen inkompatibler Code in FAU-Einrichtungen besteht, was beim Umbau von rrze-faq nicht bekannt war

        add_shortcode('faq', [$this, 'shortcodeOutput']);
        add_action('admin_head', [$this, 'setMCEConfig']);
        add_filter('mce_external_plugins', [$this, 'addMCEButtons']);
    }

    /**
     * Enqueue der Skripte.
     */
    private function getLetter(&$txt)
    {
        return mb_strtoupper(mb_substr(remove_accents($txt), 0, 1), 'UTF-8');
    }

    private function createAZ(&$aSearch)
    {
        if (count($aSearch) == 1) {
            return '';
        }
        $ret = '<div class="rrze-faq"><ul class="letters">';
        foreach (range('A', 'Z') as $a) {
            if (array_key_exists($a, $aSearch)) {
                $ret .= '<li class="filled"><a href="#letter-' . $a . '">' . $a . '</a></li>';
            } else {
                $ret .= '<li>' . $a . '</li>';
            }
        }
        return $ret . '</ul></div>';
    }

    private function createTabs(&$aTerms, $aPostIDs)
    {
        if (count($aTerms) == 1) {
            return '';
        }
        $ret = '<div class="rrze-faq">';
        foreach ($aTerms as $name => $aDetails) {
            $ret .= '<a href="#ID-' . $aDetails['ID'] . '">' . $name . '</a> | ';
        }
        return rtrim($ret, ' | ') . '</div>';
    }

    private function createTagcloud(&$aTerms, $aPostIDs)
    {
        if (count($aTerms) == 1) {
            return '';
        }
        $ret = '<div class="rrze-faq">';
        $smallest = 12;
        $largest = 22;
        $aCounts = array();
        foreach ($aTerms as $name => $aDetails) {
            $aCounts[$aDetails['ID']] = count($aPostIDs[$aDetails['ID']]);
        }
        $iMax = max($aCounts);
        $aSizes = array();
        foreach ($aCounts as $ID => $cnt) {
            $aSizes[$ID] = round(($cnt / $iMax) * $largest, 0);
            $aSizes[$ID] = ($aSizes[$ID] < $smallest ? $smallest : $aSizes[$ID]);
        }
        foreach ($aTerms as $name => $aDetails) {
            $ret .= '<a href="#ID-' . $aDetails['ID'] . '" style="font-size:' . $aSizes[$aDetails['ID']] . 'px">' . $name . '</a> | ';
        }
        return rtrim($ret, ' | ') . '</div>';
    }

    private function getTaxQuery(&$aTax)
    {
        $ret = array();

        foreach ($aTax as $taxfield => $aEntries) {
            $term_queries = array();
            $sources = array();

            foreach ($aEntries as $entry) {
                $source = !empty($entry['source']) ? $entry['source'] : '';
                $term_queries[$source][] = $entry['value'];
            }

            foreach ($term_queries as $source => $aTerms) {

                $query = array(
                    'taxonomy' => $taxfield,
                    'field' => 'slug',
                    'terms' => $aTerms,
                );

                if (count($aTerms) > 1) {
                    $query['operator'] = 'IN';
                }

                if (!empty($source)) {
                    $query['meta_key'] = 'source';
                    $query['meta_value'] = $source;
                }

                $ret[$taxfield][] = $query;
            }
            if (count($ret[$taxfield]) > 1) {
                $ret[$taxfield]['relation'] = 'OR';
            }
        }

        if (count($ret) > 1) {
            $ret['relation'] = 'AND';
        }

        return $ret;
    }

    private function searchArrayByKey(&$needle, &$aHaystack)
    {
        foreach ($aHaystack as $k => $v) {
            if ($k === $needle) {
                return $v;
            }
        }
        return false;
    }

    private function getSchema($postID, $question, $answer)
    {
        $schema = '';
        $source = get_post_meta($postID, "source", true);
        $answer = wp_strip_all_tags($answer, true);
        if ($source == 'website') {
            $schema = RRZE_SCHEMA_QUESTION_START . $question . RRZE_SCHEMA_QUESTION_END;
            $schema .= RRZE_SCHEMA_ANSWER_START . $answer . RRZE_SCHEMA_ANSWER_END;
        }
        return $schema;
    }

    // returns the pairs source and category(or tag) as an Array; values without source are added to "sourceless" 
    // example:
    // $atts['category'] = "rrze:allgemeines, fau:allgemeines, fau:neues, sonstiges";
    // getTaxBySource($atts['category']) returns [faq_category] => ['source' => 'rrze', 'value' => 'allgemeines'], ['source' => 'fau', 'value' => 'allgemeines'], ['source' => 'fau', 'value' => 'neues'], ['source' => '', 'value' => 'sonstiges']
    private function getTaxBySource($input)
    {
        $result = [];

        if (empty($input)) {
            return $result;
        }

        // Teilen des Eingabestrings in einzelne Kategorien
        $categories = explode(', ', $input);

        foreach ($categories as $category) {
            // Teilen der Kategorie in Quelle und Wert
            list($source, $value) = array_pad(explode(':', $category, 2), 2, '');

            // Überprüfen, ob $value leer ist
            if ($value === '') {
                $value = $source; // Wenn $value leer ist, setze $value auf $source
                $source = ''; // Setze $source auf leer
            }

            // Erstellen des Ergebnisarrays für jede Kategorie
            $result[] = array(
                'source' => preg_replace('/[\s,]+$/', '', $source),
                'value' => preg_replace('/[\s,]+$/', '', $value)
            );
        }

        return $result;
    }

    /**
     * Übersetzt zusammengesetzte Shortcode-Attribute in Einzeleigenschaften
     * 
     * Zerlegt die Werte von Attributen wie "glossary", "hide", "show" und "class" in Teilbegriffe 
     * und weist diesen logische Einzelfelder im Attribut-Array zu. Dadurch wird die interne Weiterverarbeitung vereinfacht.
     * 
     * @param array $atts Referenz auf das Shortcode-Attribut-Array
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
     * Workaround for a known Gutenberg issue where shortcodes within Preformatted blocks
     * are incorrectly parsed and executed, even when wrapped in double brackets [[shortcode]].
     *
     * $post is only needed for the workaround.
     *
     * In most cases, $post is defined. However, if do_shortcode() is called manually,
     * via AJAX callbacks, or within a REST API context, $post may not be available.
     * Therefore, we check if $post is a valid WP_Post object before proceeding.
     *
     * This check prevents execution by detecting the double-bracketed shortcode in the post content.
     *
     * @param string $shortcode_tag The shortcode name
     * @return string|false Escaped placeholder if found, or false to continue normal processing
     */
    private function preventGutenbergDoubleBracketBug(string $shortcode_tag)
    {
        global $post;

        if (!($post instanceof \WP_Post) || !isset($post->post_content)) {
            return '';
        }

        if (strpos($post->post_content, '[[' . $shortcode_tag . ']]') !== false) {
            return esc_html("[[$shortcode_tag]]");
        }

        return false;
    }

    /**
     * Gibt explizit angeforderte FAQs als Akkordeon oder einfachen Inhalt aus.
     *
     * Unterstützt sowohl Gutenberg-Blöcke (mehrere IDs als Array) als auch den klassischen Editor (kommasepariert).
     *
     * @param mixed  $id               Einzelne ID oder Array von IDs
     * @param bool   $gutenberg        Ob Gutenberg verwendet wird
     * @param int    $hstart           HTML-Überschrift-Level
     * @param string $style            Inline-Styles für das Akkordeon
     * @param string $expand_all_link  Attribut für "alle ausklappen"-Link
     * @param bool   $hide_accordion   Ob das Akkordeon unterdrückt werden soll
     * @param bool   $hide_title       Ob der Titel unterdrückt werden soll
     * @param string $color            Farbattribut des Akkordeons
     * @param string $load_open        Attribut für offenen Zustand
     * @param string &$schema          Wird ergänzt um generiertes JSON-LD-Schema
     * @return string Der generierte HTML-Inhalt
     */
    private function renderExplicitFAQs($id, bool $gutenberg, int $hstart, string $style, string $expand_all_link, bool $hide_accordion, bool $hide_title, string $color, string $load_open, string &$schema): string
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
        $accordion = '[collapsibles hstart="' . $hstart . '" ' . $style . ' ' . $expand_all_link . ']';

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
                        $accordion .= '[collapse title="' . $title . '" color="' . $color . '" name="' . $anchorfield . '"' . $load_open . ']' .
                            $description . '[/collapse]';
                        $schema .= $this->getSchema($id, $title, $description);
                    }
                }

                $found = true;
            }
        }

        if ($found && !$hide_accordion) {
            $accordion .= '[/collapsibles]';
            $content = do_shortcode($accordion);
        }

        return $content;
    }


    /**
     * Gibt FAQs basierend auf Taxonomien (Kategorie/Tag) oder Glossaransicht aus.
     * 
     * Unterstützt klassische und alphabetische Ausgabe, Tabs oder Tagcloud-Darstellung.
     * 
     * @param array  $atts             Ursprüngliche Shortcode-Attribute
     * @param int    $hstart           HTML-Überschrift-Level
     * @param string $style            Inline-Styles für das Akkordeon
     * @param string $expand_all_link  Attribut für "alle ausklappen"-Link
     * @param bool   $hide_accordion   Ob das Akkordeon unterdrückt werden soll
     * @param bool   $hide_title       Ob der Titel unterdrückt werden soll
     * @param string $color            Farbattribut
     * @param string $load_open        Attribut für offenen Zustand
     * @param string $sort             Sortierkriterium (title, id, sortfield)
     * @param string $order            Sortierreihenfolge
     * @param mixed  $category         Kategorie(n) als String oder Array
     * @param mixed  $tag              Tag(s) als String oder Array
     * @param string $glossary         "category" oder "tag"
     * @param string $glossarystyle    "a-z", "tabs", "tagcloud" oder leer
     * @param string &$schema          Referenz auf das Schema-Markup
     * @return string Gerenderter HTML-Inhalt
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
        $aTax['faq_category'] = $this->getTaxBySource($category);
        $aTax['faq_tag'] = $this->getTaxBySource($tag);
        $aTax = array_filter($aTax); // delete empty entries

        if ($aTax) {
            $tax_query = $this->getTaxQuery($aTax);
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
                            $letter = $this->getLetter($t->name);
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
                            $content = $this->createAZ($aLetters);
                            $anchor = 'letter';
                            break;
                        case 'tabs':
                            $content = $this->createTabs($aUsedTerms, $aPostIDs);
                            break;
                        case 'tagcloud':
                            $content = $this->createTagcloud($aUsedTerms, $aPostIDs);
                            break;
                    }
                }
                $accordion = '[collapsibles hstart="' . $hstart . '" ' . $style . ' ' . $expand_all_link . ']';
                $last_anchor = '';
                foreach ($aUsedTerms as $k => $aVal) {
                    if ($glossarystyle == 'a-z' && $content) {
                        $accordion_anchor = '';
                        $accordion .= ($last_anchor != $aVal[$anchor] ? '<h2 id="' . $anchor . '-' . $aVal[$anchor] . '">' . $aVal[$anchor] . '</h2>' : '');
                    } else {
                        $accordion_anchor = 'name="' . $anchor . '-' . $aVal[$anchor] . '"';
                    }
                    $accordion .= '[collapse title="' . $k . '" color="' . $color . '" ' . $accordion_anchor . $load_open . ']';

                    // find the postIDs to this tag
                    $aIDs = $this->searchArrayByKey($aVal['ID'], $aPostIDs);

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

                        $accordion .= '[accordion][accordion-item title="' . $title . '" name="' . $anchorfield . '"]' . $tmp . '[/accordion-item][/accordion]';
                        $schema .= $this->getSchema($ID, $title, $tmp);
                    }
                    $accordion .= '[/collapse]';
                    $last_anchor = $aVal[$anchor];
                }
                $accordion .= '[/collapsibles]';
                $content .= do_shortcode($accordion);
            } else {
                // attribut glossary is not given
                if (!$hide_accordion) {
                    $accordion = '[collapsibles hstart="' . $hstart . '" ' . $style . ' ' . $expand_all_link . ']';
                }
                $last_anchor = '';
                foreach ($posts as $post) {

                    $title = get_the_title($post->ID);
                    $letter = $this->getLetter($title);
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
                            $accordion .= ($last_anchor != $letter ? '<h2 id="letter-' . $letter . '">' . $letter . '</h2>' : '');
                        }
                        $accordion .= '[collapse title="' . $title . '" color="' . $color . '" name="' . $anchorfield . '"' . $load_open . ']' . $tmp . '[/collapse]';
                    } else {
                        $content .= ($hide_title ? '' : '<h' . $hstart . '>' . $title . '</h' . $hstart . '>') . ($tmp ? '<p>' . $tmp . '</p>' : '');
                    }
                    $schema .= $this->getSchema($post->ID, $title, $tmp);
                    $last_anchor = $letter;
                }

                if (!$hide_accordion) {
                    $accordion .= '[/collapsibles]';
                    $content .= do_shortcode($accordion);
                }
            }
        }

        return $content;
    }


    /**
     * Generieren Sie die Shortcode-Ausgabe
     * @param  array   $atts Shortcode-Attribute
     * @param  string  $content Beiliegender Inhalt
     * @return string Gib den Inhalt zurück
     */
    public function shortcodeOutput($atts, $content = null, $shortcode_tag = '')
    {
        // Workaround - see: https://github.com/RRZE-Webteam/rrze-faq/issues/132#issuecomment-2839668060
        if (($skip = $this->preventGutenbergDoubleBracketBug($shortcode_tag)) !== false) {
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
            $content = $this->renderExplicitFAQs($id, $gutenberg, $hstart, $style, $expand_all_link, $hide_accordion, $hide_title, $color, $load_open, $schema);
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

        wp_enqueue_style('rrze-faq-style');

        return '<div class="rrze-faq ' . ($color ? '' . $color . ' ' : '') . (isset($additional_class) ? $additional_class : '') . '">' . $content . '</div>';
    }

    public function sortIt(&$arr)
    {
        uasort($arr, function ($a, $b) {
            return strtolower($a) <=> strtolower($b);
        });
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
