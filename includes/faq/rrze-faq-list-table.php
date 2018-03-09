<?php

namespace RRZE\Glossar\Server;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class FAQ_List extends \WP_List_Table {

	public function __construct() {

		parent::__construct( [
			'singular' => __( 'FAQ', 'rrze-faq' ),
			'plural'   => __( 'FAQs', 'rrze-faq' ),
			'ajax'     => false
		] );

	}

	public static function delete_customer( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}customers",
			[ 'ID' => $id ],
			[ '%d' ]
		);
	}


	public function no_items() {
		_e( 'No entries', 'rrze-faq' );
                delete_option('serverfaq');
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
                        case 'id':
                        case 'category':
			case 'title':
			case 'content':
			case 'domain':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}

	function get_columns() {
		$columns = [
			'cb'        => '<input type="checkbox" />',
                        'id'        => __( 'ID', 'rrze-faq' ),
                        'title'     => __( 'Title', 'rrze-faq' ),
                        'category'  => __( 'Category', 'rrze-faq' ),
			'content'   => __( 'Content', 'rrze-faq' ),
			'domain'    => __( 'Domain', 'rrze-faq' )
		];

		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'title' => array( 'title', true ),
			'content' => array( 'content', true ),
                        'domain' => array( 'domain', true),
                        'category' => array( 'category', true)
		);

		return $sortable_columns;
	}

	public function get_bulk_actions() {
		$actions = [
			'update' => __( 'Update list', 'rrze-faq' )
		];

		return $actions;
	}
        
        function extra_tablenav( $which ) {
            $search = @$_POST['s'] ? esc_attr($_POST['s']) : "";
            if ( $which == "top" ) : ?>
            <form method="post">
                <div class="actions">
                        <p class="search-box">
                                <label for="post-search-input" class="screen-reader-text">Search Pages:</label>
                                <input type="search" value="<?php echo $search; ?>" name="s" id="post-search-input">
                                <input type="submit" value="<?php _e( 'Search', 'rrze-synonym-server' ); ?>" class="button" id="search-submit" name="">
                        </p>
                </div>
            </form>
            <?php endif;
	}

	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();
		$this->process_bulk_action();
		$per_page     = $this->get_items_per_page( 'customers_per_page', 5 );
		$current_page = $this->get_pagenum();
                $data = get_option('serverfaq');
                if(!$data) $data = FaqListTableHelper::getGlossaryForWPListTable();
                //$data = FaqListTableHelper::getGlossaryForWPListTable();
                if(@$_POST['s']) {
                $s =  $_POST['s'];
                $filterBy = $s;
                $data = array_filter($data, function ($var) use ($filterBy) {
                    return ($var['domain']      == $filterBy ||
                            $var['title']       == $filterBy ||
                            $var['category']    == $filterBy ||
                            $var['content']     == $filterBy ||
                            $var['id']          == $filterBy);
                    });
                }
                $total_items  = count( $data );
                if($data) usort( $data, array( $this, 'usort_reorder' ) );
                //$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
                if($data) {
                    $items = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
                } else {
                    $items = '';
                }
		$this->items = $items;
                
                $this->set_pagination_args( array(
			'total_items' => $total_items,                     // WE have to calculate the total number of items.
			'per_page'    => $per_page,                        // WE have to determine how many items to show on a page.
			'total_pages' => ceil( $total_items / $per_page ), // WE have to calculate the total number of pages.
		) );
	}

	public function process_bulk_action() {
            if ('update' === $this->current_action()) {
               $faq = FaqListTableHelper::getGlossaryForWPListTable();
               update_option('serverfaq', $faq);
               $html = '<div id="message" class="updated notice is-dismissible">
                           <p>' . __( 'List updated!', 'rrze-faq' ) .'</p>
                   </div>';
               echo $html;
            }
	}
        
        public function usort_reorder( $a, $b ) {
		// If no sort, default to title.
		$orderby = ! empty( $_REQUEST['orderby'] ) ? wp_unslash( $_REQUEST['orderby'] ) : 'title'; // WPCS: Input var ok.
		// If no order, default to asc.
		$order = ! empty( $_REQUEST['order'] ) ? wp_unslash( $_REQUEST['order'] ) : 'asc'; // WPCS: Input var ok.
		// Determine sort order.
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );
		return ( 'asc' === $order ) ? $result : - $result;
	}

}


class RRZE_FAQ {

	static $instance;

	public $faq_obj;

	public function __construct() {
		add_filter( 'set-screen-option', array(&$this, 'set_screen'), 10, 3 );
		add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
	}

	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function plugin_menu() {
                
            $faq_page = add_submenu_page( 
                'edit.php?post_type=glossary', __( 'Remote Faqs', 'rrze-faq' ), __( 'Remote Faqs', 'rrze-faq' ), 'manage_options', 'rrze_faq_options', array($this, 'plugin_settings_page'));

            add_action("load-{$faq_page}", array( $this, 'screen_option'));

	}

	public function plugin_settings_page() {
        ?>
            <div class="wrap">
                <h2><?php _e( 'Remote Faq List', 'rrze-faq' ) ?></h2>

                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="post-body-content">
                            <div class="meta-box-sortables ui-sortable">
                                <form method="post">
                                        <?php
                                        $this->faq_obj->prepare_items();
                                        $this->faq_obj->display(); ?>
                                </form>
                            </div>
                        </div>
                    </div>
                    <br class="clear">
                </div>
            </div>
	<?php
	}

	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => 'Customers',
			'default' => 5,
			'option'  => 'customers_per_page'
		];

		add_screen_option( $option, $args );

		$this->faq_obj = new FAQ_List();
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}

RRZE_FAQ::get_instance();