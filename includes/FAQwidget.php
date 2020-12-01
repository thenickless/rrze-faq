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
              
    // Widget Backend 
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = 'TEST' . $instance[ 'title' ];
        }else{
            $title = __( 'New title', 'rrze-faq' );
        }
        // Widget admin form

        // fields:
        // 1. drop-down:
        // random
        // explizit FAQ
        // 2. date from - date to (if empty: unlimited)

        // fill select id ( = FAQ )
        // $faqs = get_posts( array(
        //     'posts_per_page'  => -1,
        //     'post_type' => 'faq',
        //     'orderby' => 'title',
        //     'order' => 'ASC'
        // ));


        ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php 
    }
          
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
} 


     
