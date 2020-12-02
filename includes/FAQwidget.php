<?php

namespace RRZE\FAQ;

defined( 'ABSPATH' ) || exit;

require_once ABSPATH.'wp-includes/class-wp-widget.php';

// Creating the widget 
class FAQwidget extends \WP_Widget {
  
    function __construct() {
        parent::__construct(
            'faq_widget', 
            __('FAQ Widget', 'rrze-faq'), 
            array( 'description' => __( 'Displays a FAQ', 'rrze-faq' ), ) 
        );
    }
      

    public function getRandomFAQID($catID){
        $aFaqIDs = get_posts([
            'posts_per_page' => -1,
            'post_type' => 'faq',
            'fields' => 'ids',
            'tax_query' => [[
                'taxonomy' => 'faq_category',
                'field' => 'term_id',
                'terms' => $catID,
            ]]
        ]);
        return $aFaqIDs[array_rand($aFaqIDs, 1)];
    }

    // Creating widget front-end
    public function widget( $args, $instance ) {
        $start = (isset($instance['start'] ) ? date('Y-m-d', strtotime($instance['start'])) : '');
        $end = (isset($instance['end'] ) ? date('Y-m-d', strtotime($instance['end'])) : '');

        if ($start || $end){
            $today = date('Y-m-d');
            if (($start && $today < $start) || ($end && $today > $end)){
                return;
            }
        }
        
        $faqID = (isset($instance['faqID'] ) ? $instance['faqID'] : 0);
        $catID = (isset($instance['catID']) ? $instance['catID'] : 0);

        $faqID = ($faqID ? $faqID : ($catID ? $this->getRandomFAQID($catID) : 0));

        if ($faqID){
            $attributes = (isset($instance['display'] ) ? $instance['display'] : '');
            switch($attributes){
                case 1 : $attributes = '';
                    break;
                case 2 : $attributes = "show='load-open'";
                    break;
                case 3 : $attributes = "hide='title'";
                    break;
            }
            echo $args['before_widget'];
            echo do_shortcode('[faq id="'. $faqID . '" ' . $attributes . ']');
            echo $args['after_widget'];
        }
    }

    public function dropdownFAQs($selectedID = 0) {
		$args = [
            'post_type'             => 'faq',
            'pagination'            => FALSE,
			'posts_per_page'        => -1,
			'post_status'           => 'publish',
			'cache_results'         => TRUE,
			'cache_post_meta_cache' => TRUE,
			'order'                 => 'ASC',
			'orderby'               => 'post_title',
        ];

		$posts  = get_posts($args);
		$output = '';

		if( ! empty($posts) ) {
            $output = "<p><label for='{$this->get_field_id('faqID')}'>" . __('Choose a FAQ', 'rrze-faq') . ":</label> ";
			$output .= "<select id='{$this->get_field_id('faqID')}' name='{$this->get_field_name('faqID')}' class='widefat'>";
            $output .= "<option value='0'>---</option>";
			foreach($posts as $post) {
                $sSelected = selected($selectedID, $post->ID, FALSE );
				$output .= "<option value='{$post->ID}' $sSelected>" . esc_html( $post->post_title ) . "</option>";
			}
			$output .= "</select></p>";
		}
		$html = apply_filters( 'dropdownFAQs', $output, $args, $posts );
	    echo $html;
    }
    
    public function displaySelect($selectedID = 0){
        $aOptions = [
            1 => __('show question and answer', 'rrze-faq'),
            2 => __('show question and answer opened', 'rrze-faq'),
            3 => __('hide question', 'rrze-faq')
        ];
        $output = "<p><label for='{$this->get_field_id('display')}'>" . __('Display options:', 'rrze-faq') . ":</label>";
        $output .= "<select id='{$this->get_field_id('display')}' name='{$this->get_field_name('display')}' class='widefat'>";
        foreach($aOptions as $ID => $txt){
            $sSelected = selected($selectedID, $ID, FALSE );
            $output .= "<option value='$ID' $sSelected>$txt</option>";
        }
        $output .= "</select></p>";
        echo $output;
    }

    public function dateFields($dates){
        $aFields = [
            'start' => __('Start', 'rrze-faq'),
            'end' => __('End', 'rrze-faq')
        ];
        $output = '';
        foreach($aFields as $field => $label){
            $val = $dates[$field];
            $output .= "<p><label for='$field'>" . $label . ":</label><br>";
            $output .= "<input type='date' id='{$this->get_field_id($field)}' name='{$this->get_field_name($field)}' value='$val' class='widefat'></p>";
        }
        echo $output;
    }
              
    // Widget Backend 
    public function form( $instance ) {
        $faqID = (isset($instance['faqID'] ) ? $instance['faqID'] : 0);
        $catID = (isset($instance['catID']) ? $instance['catID'] : 0);
        $dates = [
            'start' => (isset($instance['start']) ? $instance['start'] : ''),
            'end' => (isset($instance['end']) ? $instance['end'] : '')
        ];
        $display = (isset($instance['display']) ? $instance['display'] : 0);

        $this->dropdownFAQs($faqID);

        $args = [
            'show_option_none' => '---',
            'name' => $this->get_field_name('catID'),
            'taxonomy' => 'faq_category',
            'hide_empty' => 0,
            'orderby' => 'name',
            'selected' => $catID,
            'class' => 'widefat',
        ];
        echo "<p><label for='{$this->get_field_name('catID')}'>" . __('or choose a Category to display a FAQ randomly', 'rrze-faq') . ":</label>";
        wp_dropdown_categories($args);
        echo '</p>';
        $this->dateFields($dates);
        $this->displaySelect($display);
    }
          
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = [];
        $instance['faqID'] = (isset($new_instance['faqID']) ? $new_instance['faqID'] : 0);
        $instance['catID'] = (isset($new_instance['catID']) ? $new_instance['catID'] : 0);
        $instance['start'] = (isset( $new_instance['start']) ? $new_instance['start'] : '');
        $instance['end'] = (isset( $new_instance['end']) ? $new_instance['end'] : '');
        $instance['display'] = (isset($new_instance['display']) ? $new_instance['display'] : 0);
        return $instance;
    }
} 


     
