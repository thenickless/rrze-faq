<?php

namespace RRZE\FAQ;

defined('ABSPATH') || exit;

use function RRZE\FAQ\Config\getConstants;
use RRZE\FAQ\API;

/**
 * Layout settings for "faq"
 */
class Layout
{

    public function __construct()
    {

        add_filter('pre_get_posts', [$this, 'makeFaqSortable']);
        add_filter('enter_title_here', [$this, 'changeTitleText']);
        // show content in box if not editable ( not editable == source is not "website" - it is sychronized from another website )
        add_action('admin_menu', [$this, 'toggleEditor']);

        // Table "All FAQ"
        add_filter('manage_faq_posts_columns', [$this, 'addFaqColumns']);
        add_action('manage_faq_posts_custom_column', [$this, 'getFaqColumnsValues'], 10, 2);
        add_filter('manage_edit-faq_sortable_columns', [$this, 'addFaqSortableColumns']);
        add_action('restrict_manage_posts', [$this, 'addFaqFilters'], 10, 1);
        add_filter('parse_query', [$this, 'filterRequestQuery'], 10);

        // Table "Category"
        add_filter('manage_edit-faq_category_columns', [$this, 'addTaxColumns']);
        add_filter('manage_faq_category_custom_column', [$this, 'getTaxColumnsValues'], 10, 3);
        add_filter('manage_edit-faq_category_sortable_columns', [$this, 'addTaxColumns']);
        
        // Table "Tags"
        add_filter('manage_edit-faq_tag_columns', [$this, 'addTaxColumns']);
        add_filter('manage_faq_tag_custom_column', [$this, 'getTaxColumnsValues'], 10, 3);
        add_filter('manage_edit-faq_tag_sortable_columns', [$this, 'addTaxColumns']);
        add_action('save_post_faq', [$this, 'savePostMeta']);
    }

    public function makeFaqSortable($wp_query)
    {
        if (is_admin() && !empty($wp_query->query['post_type'])) {
            $post_type = $wp_query->query['post_type'];
            if ($post_type == 'faq') {
                if (!isset($wp_query->query['orderby'])) {
                    $wp_query->set('orderby', 'title');
                    $wp_query->set('order', 'ASC');
                }

                $orderby = $wp_query->get('orderby');
                if ($orderby == 'sortfield') {
                    $wp_query->set('meta_key', 'sortfield');
                    $wp_query->set('orderby', 'meta_value');
                }
            }
        }
    }

    public function savePostMeta($postID)
    {
        if (!current_user_can('edit_post', $postID) || !isset($_POST['sortfield']) || !isset($_POST['anchorfield']) || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)) {
            return $postID;
        }

        // Ensure slashes are unslashed and input is sanitized
        $source = (empty($_POST['source']) ? 'website' : sanitize_text_field(wp_unslash($_POST['source'])));
        update_post_meta($postID, 'source', $source);
        update_post_meta($postID, 'lang', substr(get_locale(), 0, 2));
        update_post_meta($postID, 'remoteID', $postID);
        update_post_meta($postID, 'remoteChanged', get_post_timestamp($postID, 'modified'));

        // Sanitize and unslash the input fields
        update_post_meta($postID, 'sortfield', sanitize_text_field(wp_unslash($_POST['sortfield'])));
        update_post_meta($postID, 'anchorfield', sanitize_title(wp_unslash($_POST['anchorfield'])));
    }

    public function sortboxCallback($meta_id)
    {
        $output = '<input type="hidden" name="source" id="source" value="' . esc_attr(get_post_meta($meta_id->ID, 'source', true)) . '">';
        $output .= '<input type="text" name="sortfield" id="sortfield" class="sortfield" value="' . esc_attr(get_post_meta($meta_id->ID, 'sortfield', true)) . '">';
        $output .= '<p class="description">' . __('Criterion for sorting the output of the shortcode', 'rrze-faq') . '</p>';
        echo wp_kses_post($output);
    }

    public function anchorboxCallback($meta_id)
    {
        $output = '<input type="hidden" name="source" id="source" value="' . esc_attr(get_post_meta($meta_id->ID, 'source', true)) . '">';
        $output .= '<input type="text" name="anchorfield" id="anchorfield" class="anchorfield" value="' . esc_attr(get_post_meta($meta_id->ID, 'anchorfield', true)) . '">';
        $output .= '<p class="description">' . __('Anchor field (optional) to define jump marks when displayed in accordions ', 'rrze-faq') . '</p>';
        echo wp_kses_post($output);
    }


    public function langboxCallback($meta_id)
    {
        $output = '<input type="text" name="lang" id="lang" class="lang" value="' . esc_attr(get_post_meta($meta_id->ID, 'lang', true)) . '">';
        $output .= '<p class="description">' . __('Language of this FAQ', 'rrze-faq') . '</p>';
        echo wp_kses_post($output);
    }

    public function fillContentBox($post)
    {
        $mycontent = apply_filters('the_content', $post->post_content);
        echo '<h1>' . esc_html($post->post_title) . '</h1><br>' . wp_kses_post($mycontent);
    }

    public function fillShortcodeBox()
    {
        global $post;
        $ret = '';
        $category = '';
        $tag = '';
        $fields = array('category', 'tag');
        foreach ($fields as $field) {
            $terms = wp_get_post_terms($post->ID, 'faq_' . $field);
            foreach ($terms as $term) {
                $$field .= $term->slug . ', ';
            }
            $$field = rtrim($$field, ', ');
        }

        if ($post->ID > 0) {
            $ret .= '<h3 class="hndle">' . __('Single entries', 'rrze-faq') . ':</h3><p>[faq id="' . $post->ID . '"]</p>';
            $ret .= ($category ? '<h3 class="hndle">' . __('Accordion with category', 'rrze-faq') . ':</h3><p>[faq category="' . $category . '"]</p><p>' . __('If there is more than one category listed, use at least one of them.', 'rrze-faq') . '</p>' : '');
            $ret .= ($tag ? '<h3 class="hndle">' . __('Accordion with tag', 'rrze-faq') . ':</h3><p>[faq tag="' . $tag . '"]</p><p>' . __('If there is more than one tag listed, use at least one of them.', 'rrze-faq') . '</p>' : '');
            $ret .= '<h3 class="hndle">' . __('Accordion with all entries', 'rrze-faq') . ':</h3><p>[faq]</p>';
        }
        echo wp_kses_post($ret);
    }

    public function changeTitleText($title)
    {
        $screen = get_current_screen();
        if ($screen->post_type == 'faq') {
            $title = __('Enter question here', 'rrze-faq');
        }
        return $title;
    }

    public function toggleEditor()
    {
        $post_id = isset($_GET['post']) ? sanitize_text_field(wp_unslash($_GET['post'])) : (isset($_POST['post_ID']) ? sanitize_text_field(wp_unslash($_POST['post_ID'])) : 0);

        if ($post_id) {
            if (get_post_type($post_id) == 'faq') {
                $source = get_post_meta($post_id, "source", true);
                if ($source && $source != 'website') {
                    $api = new API();
                    $domains = $api->getDomains();
                    $remoteID = get_post_meta($post_id, "remoteID", true);
                    $link = $domains[$source] . 'wp-admin/post.php?post=' . $remoteID . '&action=edit';
                    remove_post_type_support('faq', 'title');
                    remove_post_type_support('faq', 'editor');
                    remove_meta_box('faq_categorydiv', 'faq', 'side');
                    remove_meta_box('tagsdiv-faq_tag', 'faq', 'side');

                    add_meta_box(
                        'read_only_content_box',
                        __('This FAQ cannot be edited because it is synchronized', 'rrze-faq') . '. <a href="' . esc_url($link) . '" target="_blank">' . __('You can edit it at the source', 'rrze-faq') . '</a>',
                        [$this, 'fillContentBox'],
                        'faq',
                        'normal',
                        'high'
                    );
                }
            }

            add_meta_box(
                'shortcode_box',
                __('Integration in pages and posts', 'rrze-faq'),
                [$this, 'fillShortcodeBox'],
                'faq',
                'normal'
            );
        }

        add_meta_box(
            'langbox',
            __('Language', 'rrze-faq'),
            [$this, 'langboxCallback'],
            'faq',
            'side'
        );
        add_meta_box(
            'sortbox',
            __('Sort', 'rrze-faq'),
            [$this, 'sortboxCallback'],
            'faq',
            'side'
        );
        add_meta_box(
            'anchorbox',
            __('Anchor', 'rrze-faq'),
            [$this, 'anchorboxCallback'],
            'faq',
            'side'
        );
    }

    public function addFaqColumns($columns)
    {
        $columns['lang'] = __('Language', 'rrze-faq');
        $columns['sortfield'] = __('Sort criterion', 'rrze-faq');
        $columns['source'] = __('Source', 'rrze-faq');
        $columns['id'] = __('ID', 'rrze-faq');
        return $columns;
    }

    public function addFaqSortableColumns($columns)
    {
        $columns['taxonomy-faq_category'] = __('Category', 'rrze-faq');
        $columns['taxonomy-faq_tag'] = __('Tag', 'rrze-faq');
        $columns['lang'] = __('Language', 'rrze-faq');
        $columns['sortfield'] = 'sortfield';
        $columns['source'] = __('Source', 'rrze-faq');
        $columns['id'] = __('ID', 'rrze-faq');
        return $columns;
    }

    public function addFaqFilters( $post_type ) {
        if ( $post_type !== 'faq' ) {
            return;
        }
    
        $taxonomies_slugs = [ 'faq_category', 'faq_tag' ];
        foreach ( $taxonomies_slugs as $slug ) {
            $taxonomy = get_taxonomy( $slug );
            $selected = isset( $_REQUEST[ $slug ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $slug ] ) ) : '';
            wp_dropdown_categories( [
                'show_option_all' => $taxonomy->labels->all_items,
                'taxonomy'        => $slug,
                'name'            => $slug,
                'orderby'         => 'name',
                'value_field'     => 'slug',
                'selected'        => $selected,
                'hierarchical'    => true,
                'hide_empty'      => true,
                'show_count'      => true,
            ] );
        }
    
        // dropdown "source"
        global $wpdb;
        $selectedVal = isset( $_REQUEST['source'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['source'] ) ) : '';
    
        // Prepare the SQL query to prevent SQL injection
        $query = "
            SELECT DISTINCT pm.meta_value
            FROM {$wpdb->postmeta} pm
            LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE pm.meta_key = %s
            AND p.post_status = %s
            ORDER BY pm.meta_value
        ";
        $meta_key = 'source';
        $post_status = 'publish';
        $prepared_query = $wpdb->prepare( $query, $meta_key, $post_status );
    
        // Execute the prepared query
        $myTerms = $wpdb->get_col( $prepared_query );
    
        $output = "<select name='source'>";
        $output .= '<option value="0">' . __( 'All Sources', 'rrze-faq' ) . '</option>';
    
        foreach ( $myTerms as $term ) {
            $selected = ( $term == $selectedVal ) ? 'selected' : '';
            $output .= "<option value='" . esc_attr( $term ) . "' $selected>" . esc_html( $term ) . "</option>";
        }
    
        $output .= "</select>";
        echo wp_kses_post( $output );
    }
    
    public function filterRequestQuery($query)
    {
        if (!(is_admin() && $query->is_main_query())) {
            return $query;
        }

        if ($query->query['post_type'] !== 'faq') {
            return $query;
        }

        if (!empty($_REQUEST['source'])) {
            $query->query_vars['meta_query'] = [
                [
                    'key' => 'source',
                    'value' => sanitize_text_field(wp_unslash($_REQUEST['source'])),
                    'compare' => '=',
                ],
            ];
        }

        return $query;
    }

    public function addTaxColumns($columns)
    {
        $columns['lang'] = __('Language', 'rrze-faq');
        $columns['source'] = __('Source', 'rrze-faq');
        return $columns;
    }

    public function getFaqColumnsValues($column_name, $post_id)
    {
        if ($column_name == 'id') {
            echo esc_html($post_id);
        }
        if ($column_name == 'lang') {
            echo esc_html(get_post_meta($post_id, 'lang', true) );
        }
        if ($column_name == 'source') {
            echo esc_html(get_post_meta($post_id, 'source', true));
        }
        if ($column_name == 'sortfield') {
            echo esc_html(get_post_meta($post_id, 'sortfield', true));
        }
        if ($column_name == 'anchorfield') {
            echo esc_html(get_post_meta($post_id, 'anchorfield', true));
        }
    }

    public function getTaxColumnsValues($content, $column_name, $term_id)
    {
        if ($column_name == 'lang') {
            $lang = get_term_meta($term_id, 'lang', true);
            echo esc_html($lang);
        }
        if ($column_name == 'source') {
            $source = get_term_meta($term_id, 'source', true);
            echo esc_html($source);
        }
    }

    public static function getThemeGroup()
    {
        $constants = getConstants();
        $ret = '';
        $active_theme = wp_get_theme();
        $active_theme = $active_theme->get('Name');

        if (in_array($active_theme, $constants['fauthemes'])) {
            $ret = 'fauthemes';
        } elseif (in_array($active_theme, $constants['rrzethemes'])) {
            $ret = 'rrzethemes';
        }
        return $ret;
    }
}
