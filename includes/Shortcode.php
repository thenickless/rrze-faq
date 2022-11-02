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
        add_action('admin_enqueue_scripts', [$this, 'enqueueGutenberg']);
        add_action('init', [$this, 'initGutenberg']);
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
        $ret = '<div class="fau-glossar"><ul class="letters">';
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
        $ret = '<div class="fau-glossar">';
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
        $ret = '<div class="fau-glossar">';
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
        $ret = '';
        $aTmp = array();
        foreach ($aTax as $field => $aVal) {
            if ($aVal[0]) {
                $aTmp[] = array(
                    'taxonomy' => 'faq_' . $field,
                    'field' => 'slug',
                    'terms' => $aVal,
                );
            }
        }
        if ($aTmp) {
            $ret = $aTmp;
            if (count($aTmp) > 1) {
                $ret['relation'] = 'AND';
            }
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

    /**
     * Generieren Sie die Shortcode-Ausgabe
     * @param  array   $atts Shortcode-Attribute
     * @param  string  $content Beiliegender Inhalt
     * @return string Gib den Inhalt zurück
     */
    public function shortcodeOutput($atts)
    {
        if (!$atts) {
            $atts = array();
        }
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
                    case 'accordion':
                    case 'accordeon':
                        $atts['hide_accordion'] = true;
                        break;
                        break;
                    case 'glossary':
                        $atts['glossarystyle'] = '';
                        break;
                }
            }
        }

        $atts['expand_all_link'] = (isset($atts['expand_all_link']) && $atts['expand_all_link'] ? ' expand-all-link="true"' : '');
        $atts['load_open'] = (isset($atts['load_open']) && $atts['load_open'] ? ' load="open"' : '');
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
        $atts['additional_class'] = (isset($atts['additional_class']) ? $atts['additional_class'] : '');
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
        // possible values for "sort" : title, id and sortfield / default = 'title'
        $atts['sort'] = (isset($atts['sort']) && ($atts['sort'] == 'title' || $atts['sort'] == 'id' || $atts['sort'] == 'sortfield') ? $atts['sort'] : 'title');

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
        // if ( $glossary && ( array_key_exists( $glossary, $this->settings['glossary']['values'] ) == FALSE )){
        //     return __( 'Attribute glossary is not correct. Please use either glossary="category" or glossary="tag".', 'rrze-faq' );
        // }
        // if ( array_key_exists( $color, $this->settings['color']['values'] ) == FALSE ){
        //     return __( 'Attribute color is not correct. Please use either \'medfak\', \'natfak\', \'rwfak\', \'philfak\' or \'techfak\'', 'rrze-faq' );
        // }

        $gutenberg = (is_array($id) ? true : false);

        if ($id && (!$gutenberg || $gutenberg && $id[0])) {
            // EXPLICIT FAQ(s)
            if ($gutenberg) {
                $aIDs = $id;
            } else {
                // classic editor
                $aIDs = explode(',', $id);
            }
            $found = false;
            $accordion = '[collapsibles hstart="' . $hstart . '"' . $expand_all_link . ']';
            foreach ($aIDs as $faqID) {
                $faqID = trim($faqID);
                if ($faqID) {
                    $title = get_the_title($faqID);
                    $description = str_replace(']]>', ']]&gt;', apply_filters('the_content', get_post_field('post_content', $faqID)));
                    if (!isset($description) || (mb_strlen($description) < 1)) {
                        $description = get_post_meta($id, 'description', true);
                    }
                    if ($hide_accordion) {
                        $content .= ($hide_title ? '' : '<h' . $hstart . '>' . $title . '</h' . $hstart . '>') . ($description ? '<p>' . $description . '</p>' : '');
                    } else {
                        if ($description) {
                            $accordion .= '[collapse title="' . $title . '" color="' . $color . '" name="ID-' . $faqID . '"' . $load_open . ']' . $description . '[/collapse]';
                            $schema .= $this->getSchema($faqID, $title, $description);
                        }
                    }
                    $found = true;
                }
            }
            if ($found && !$hide_accordion) {
                $accordion .= '[/collapsibles]';
                $content = do_shortcode($accordion);
            }
        } else {
            // attribute category or tag is given or none of them
            $aLetters = array();
            $aCategory = array();
            $aTax = array();
            $tax_query = '';

            // $postQuery = array('post_type' => 'faq', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => $sort, 'order' => $order, 'suppress_filters' => false);
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

            $fields = array('category', 'tag');
            foreach ($fields as $field) {
                if (!is_array($$field)) {
                    $aTax[$field] = explode(',', trim($$field));
                } elseif ($$field[0]) {
                    $aTax[$field] = $$field;
                }
            }
            if ($aTax) {
                $tax_query = $this->getTaxQuery($aTax);
                if ($tax_query) {
                    $postQuery['tax_query'] = $tax_query;
                }
            }

            if (!empty($atts['lang'])) {
                $postQuery['meta_query'] = [[
                    'key' => 'lang',
                    'value' => $atts['lang'],
                    'compare' => '=',
                ]];
            }

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
                    $accordion = '[collapsibles hstart="' . $hstart . '"' . $expand_all_link . ']';
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
                            $accordion .= '[accordion][accordion-item title="' . $title . '" name="innerID-' . $ID . '"]' . $tmp . '[/accordion-item][/accordion]';
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
                        $accordion = '[collapsibles hstart="' . $hstart . '"' . $expand_all_link . ']';
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
                            $accordion_anchor = '';
                            $accordion_anchor = 'name="ID-' . $post->ID . '"';
                            if ($glossarystyle == 'a-z' && count($posts) > 1) {
                                $accordion .= ($last_anchor != $letter ? '<h2 id="letter-' . $letter . '">' . $letter . '</h2>' : '');
                            }
                            $accordion .= '[collapse title="' . $title . '" color="' . $color . '" ' . $accordion_anchor . $load_open . ']' . $tmp . '[/collapse]';
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

        return '<div class="' . ($color ? '' . $color . ' ' : '') . (isset($additional_class) ? $additional_class : '') . '">' . $content . '</div>';
    }

    public function sortIt(&$arr)
    {
        uasort($arr, function ($a, $b) {
            return strtolower($a) <=> strtolower($b);
        });
    }

    public function isGutenberg()
    {
        $postID = get_the_ID();
        if ($postID && !use_block_editor_for_post($postID)) {
            return false;
        }

        return true;
    }

    public function fillGutenbergOptions()
    {
        // fill selects "category" and "tag"
        $fields = array('category', 'tag');
        foreach ($fields as $field) {
            // set new params for gutenberg / the old ones are used for shortcode in classic editor
            $this->settings[$field]['values'] = array();
            $this->settings[$field]['field_type'] = 'multi_select';
            $this->settings[$field]['default'] = array(0);
            $this->settings[$field]['type'] = 'array';
            $this->settings[$field]['items'] = array('type' => 'string');
            $this->settings[$field]['values'][] = ['id' => 0, 'val' => __('-- all --', 'rrze-faq')];

            // get categories and tags from this website
            $terms = get_terms([
                'taxonomy' => 'faq_' . $field,
                'hide_empty' => true,
                'orderby' => 'name',
                'order' => 'ASC',
            ]);

            foreach ($terms as $term) {
                $this->settings[$field]['values'][] = [
                    'id' => $term->slug,
                    'val' => $term->name,
                ];
            }
        }

        // fill select id ( = FAQ )
        $faqs = get_posts(array(
            'posts_per_page' => -1,
            'post_type' => 'faq',
            'orderby' => 'title',
            'order' => 'ASC',
        ));

        $this->settings['id']['values'] = array();
        $this->settings['id']['field_type'] = 'multi_select';
        $this->settings['id']['default'] = array(0);
        $this->settings['id']['type'] = 'array';
        $this->settings['id']['items'] = array('type' => 'number');
        $this->settings['id']['values'][] = ['id' => 0, 'val' => __('-- all --', 'rrze-faq')];
        foreach ($faqs as $faq) {
            $this->settings['id']['values'][] = [
                'id' => $faq->ID,
                'val' => str_replace("'", "", str_replace('"', "", $faq->post_title)),
            ];
        }

        return $this->settings;
    }

    public function initGutenberg()
    {
        if (!$this->isGutenberg()) {
            return;
        }

        // get prefills for dropdowns
        $this->settings = $this->fillGutenbergOptions();

        // register js-script to inject php config to call gutenberg lib
        $editor_script = $this->settings['block']['blockname'] . '-block';
        $js = '../assets/js/' . $editor_script . '.js';

        wp_register_script(
            $editor_script,
            plugins_url($js, __FILE__),
            array(
                'RRZE-Gutenberg',
            ),
            null
        );
        wp_localize_script($editor_script, $this->settings['block']['blockname'] . 'Config', $this->settings);

        // register block
        register_block_type($this->settings['block']['blocktype'], array(
            'editor_script' => $editor_script,
            'render_callback' => [$this, 'shortcodeOutput'],
            'attributes' => $this->settings,
        )
        );
    }

    public function enqueueGutenberg()
    {
        if (!$this->isGutenberg()) {
            return;
        }

        // include gutenberg lib
        wp_enqueue_script(
            'RRZE-Gutenberg',
            plugins_url('../assets/js/gutenberg.js', __FILE__),
            array(
                'wp-blocks',
                'wp-i18n',
                'wp-element',
                'wp-components',
                'wp-editor',
            ),
            null
        );
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
                'name': <?php echo json_encode($this->pluginname); ?>,
                'title': <?php echo json_encode($this->settings['block']['title']); ?>,
                'icon': <?php echo json_encode($this->settings['block']['tinymce_icon']); ?>,
                'shortcode': <?php echo json_encode($shortcode); ?>,
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
