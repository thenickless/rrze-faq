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
      
    // Creating widget front-end
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
          
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if ( ! empty( $title ) ){
            echo $args['before_title'] . $title . $args['after_title'];
        }
          
        // This is where you run the code and display the output
        echo __( 'Hello, World!', 'rrze-faq' );
        echo $args['after_widget'];
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
			$output = "<select name='{$this->get_field_name('faq_id')}' class='widefat'>";
            $output .= "<option value='0'>--- " . __('Choose a FAQ', 'rrze-faq') . " ---</option>";
			foreach($posts as $post) {
                $sSelected = selected($selectedID, $post->ID, FALSE );
				$output .= "<option value='{$post->ID}' $sSelected>" . esc_html( $post->post_title ) . "</option>";
			}
			$output .= "</select>";
		}
		$html = apply_filters( 'dropdownFAQs', $output, $args, $posts );
	    echo $html;
	}
              
    // Widget Backend 
    public function form( $instance ) {
        $faq_id = (isset($instance['faq_id'] ) ? $instance['faq_id'] : 0);
        $faq_cat = (isset($instance['faq_cat']) ? $instance['faq_cat'] : '');

        $this->dropdownFAQs($faq_id);

        $args = [
            'show_option_none' => '--- ' . __('Choose a category', 'rrze-faq') . ' ---',
            'name' => $this->get_field_name('faq_cat'),
            'taxonomy' => 'faq_category',
            'hide_empty' => 0,
            'orderby' => 'name',
            'selected' => $faq_cat,
            'class' => 'widefat',
        ];
        wp_dropdown_categories($args);
    }
          
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = [];
        $instance['faq_id'] = ( !empty( $new_instance['faq_id'] ) ) ? $new_instance['faq_id'] : 0;
        $instance['faq_cat'] = ( !empty( $new_instance['faq_cat'] ) ) ? strip_tags( $new_instance['faq_cat'] ) : '';
        return $instance;
    }
} 


     
