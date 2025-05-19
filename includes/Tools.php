<?php

namespace RRZE\FAQ;

defined('ABSPATH') || exit;

class Tools
{
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
    public function preventGutenbergDoubleBracketBug(string $shortcode_tag)
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
     * Sorts an associative array alphabetically by its values (case-insensitive).
     *
     * @param array &$arr The array to sort.
     * @return void
     */
    public function sortIt(&$arr)
    {
        uasort($arr, function ($a, $b) {
            return strtolower($a) <=> strtolower($b);
        });
    }

    /**
     * Searches an associative array by a given key and returns the matching value.
     *
     * @param string $needle The key to search for.
     * @param array  $aHaystack The array to search in.
     * @return mixed The value if found, false otherwise.
     */
    public function searchArrayByKey(&$needle, &$aHaystack)
    {
        foreach ($aHaystack as $k => $v) {
            if ($k === $needle) {
                return $v;
            }
        }
        return false;
    }

    /**
     * Wraps the given content in a DIV with optional layout classes and ARIA label binding.
     *
     * Uses the provided header ID for aria-labelledby. Supports optional Masonry layout,
     * color scheme, and additional CSS classes.
     *
     * @param string &$content           The HTML content to wrap.
     * @param string &$header_id         The header ID used for aria-labelledby.
     * @param bool   &$masonry           Whether to apply Masonry layout classes.
     * @param string &$color             Optional color class (e.g. 'blue', 'phil', ...).
     * @param string &$additional_class  Additional CSS classes to append.
     * @return string The wrapped HTML output.
     */
    public static function renderFAQWrapper(string &$content, string &$header_id, bool &$masonry, string &$color, string &$additional_class): string
    {
        $classes = 'rrze-faq';

        if ($masonry) {
            $classes .= ' faq-masonry';
        }

        if (!empty($color)) {
            $classes .= ' ' . trim($color);
        }

        if (!empty($additional_class)) {
            $classes .= ' ' . trim($additional_class);
        }

        return '<div class="' . esc_attr($classes) . '" aria-labeledby="' . esc_attr($header_id) . '">' . $content . '</div>';
    }


    /**
     * Extracts and returns the uppercase first letter of a given string.
     *
     * @param string $txt The input string.
     * @return string The uppercase initial letter.
     */
    public function getLetter(&$txt)
    {
        return mb_strtoupper(mb_substr(remove_accents($txt), 0, 1), 'UTF-8');
    }

    /**
     * Generates an A–Z letter navigation HTML block based on available letters.
     *
     * @param array &$aSearch Array of available letters.
     * @return string HTML output of the letter navigation.
     */
    public function createAZ(&$aSearch)
    {

        // echo Tools::renderFaqWrapper($content, $headerId, false);
        if (count($aSearch) == 1) {
            return '';
        }
        $ret = '<ul class="letters">';

        foreach (range('A', 'Z') as $a) {
            if (array_key_exists($a, $aSearch)) {
                $ret .= '<li class="filled"><a href="#letter-' . $a . '">' . $a . '</a></li>';
            } else {
                $ret .= '<li>' . $a . '</li>';
            }
        }
        return $ret . '</ul>';
    }

    /**
     * Generates a tab-style navigation based on terms.
     *
     * @param array $aTerms List of terms.
     * @param array $aPostIDs Mapping of term IDs to post IDs.
     * @return string HTML output of the tab navigation.
     */
    public function createTabs(&$aTerms, $aPostIDs)
    {
        if (count($aTerms) == 1) {
            return '';
        }
        $ret = '';
        foreach ($aTerms as $name => $aDetails) {
            $ret .= '<a href="#ID-' . $aDetails['ID'] . '">' . $name . '</a> | ';
        }
        return rtrim($ret, ' | ');
    }

    /**
     * Generates a tag cloud with font size scaled by post count.
     *
     * @param array $aTerms List of terms.
     * @param array $aPostIDs Mapping of term IDs to post IDs.
     * @return string HTML output of the tag cloud.
     */
    public function createTagcloud(&$aTerms, $aPostIDs)
    {
        if (count($aTerms) == 1) {
            return '';
        }
        $ret = '';
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
            $ret .= '<a href="#ID-' . $aDetails['ID'] . '" style="font-size:' . $aSizes[$aDetails['ID']] . 'px">' . $name .
                '</a> | ';
        }
        return rtrim($ret, ' | ');
    }

    /**
     * Builds a tax_query array for use in WP_Query based on taxonomy source-value pairs.
     *
     * @param array &$aTax The structured taxonomy input.
     * @return array A WP-compatible tax_query array.
     */
    public function getTaxQuery(&$aTax)
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

    /**
     * Creates schema.org markup for a FAQ item if source is 'website'.
     *
     * @param int    $postID   The post ID of the FAQ item.
     * @param string $question The FAQ question.
     * @param string $answer   The FAQ answer.
     * @return string JSON-LD schema markup string.
     */
    public function getSchema($postID, $question, $answer)
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

    /**
     * Parses a string of source-prefixed taxonomies into a structured array.
     *
     * Input format: "rrze:cat1, fau:cat2, general"
     *
     * @param string $input The raw input string.
     * @return array Parsed array of [ 'source' => string, 'value' => string ] pairs.
     */
    public function getTaxBySource($input)
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
     * Returns a comma-separated list of term links for a given taxonomy.
     *
     * Retrieves all terms assigned to the specified post and taxonomy,
     * and outputs them as linked names pointing to the respective term archive pages.
     *
     * @param int    $postID     The ID of the post.
     * @param string $mytaxonomy The taxonomy name (e.g., 'faq_category', 'faq_tag').
     * @return string HTML string of linked term names, separated by commas.
     */

    public static function getTermLinks(&$postID, $mytaxonomy)
    {
        $ret = '';
        $terms = wp_get_post_terms($postID, $mytaxonomy);

        foreach ($terms as $term) {
            $ret .= '<a href="' . get_term_link($term->slug, $mytaxonomy) . '">' . $term->name . '</a>, ';
        }
        return substr($ret, 0, -2);
    }

}