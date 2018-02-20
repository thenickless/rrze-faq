<?php

namespace RRZE\Glossar\Server;

Class RRZE_Faq extends \WP_List_Table {
        
        public function __construct() {
		// Set parent defaults.
		parent::__construct( array(
			'singular' => 'movie',     // Singular name of the listed records.
			'plural'   => 'movies',    // Plural name of the listed records.
			'ajax'     => false,       // Does this table support ajax?
		) );
	}
        
        public function get_columns() {
		$columns = array(
			'cb'       => '<input type="checkbox" />', // Render a checkbox instead of text.
			'title'    => _x( 'Title', 'Column label', 'wp-list-table-example' ),
			'content'   => _x( 'Rating', 'Column label', 'wp-list-table-example' ),
			'domain' => _x( 'Director', 'Column label', 'wp-list-table-example' ),
		);
		return $columns;
	}
        
        protected function get_sortable_columns() {
		$sortable_columns = array(
			'title'    => array( 'title', false ),
			'rating'   => array( 'rating', false ),
			'director' => array( 'director', false ),
		);
		return $sortable_columns;
	}
        
        protected function get_hidden_columns() {
            return array();
        }
        
        protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
                        case 'title':
			case 'content':
			case 'domain':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); // Show the whole array for troubleshooting purposes.
		}
	}
        
        protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],  // Let's simply repurpose the table's singular label ("movie").
			$item['id']                // The value of the checkbox should be the record's ID.
		);
	}
        
        
        protected function get_bulk_actions() {
		$actions = array(
			'delete' => _x( 'Delete', 'List table bulk action', 'wp-list-table-example' ),
		);
		return $actions;
	}
        
        protected function process_bulk_action() {
		// Detect when a bulk action is being triggered.
		if ( 'delete' === $this->current_action() ) {
			wp_die( 'Items deleted (or they would be if we had items to delete)!' );
		}
	}
        
        function prepare_items() {
		$per_page = 10;
		$columns  = $this->get_columns();
                $this->_column_headers = $this->get_column_info();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->process_bulk_action();
		$data = FaqListTableHelper::getGlossaryForWPListTable();
		usort( $data, array( $this, 'usort_reorder' ) );
		$current_page = $this->get_pagenum();
		$total_items = count( $data );
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
		$this->items = $data;
		$this->set_pagination_args( array(
			'total_items' => $total_items,                     // WE have to calculate the total number of items.
			'per_page'    => $per_page,                        // WE have to determine how many items to show on a page.
			'total_pages' => ceil( $total_items / $per_page ), // WE have to calculate the total number of pages.
		) );
	}
        
        protected function usort_reorder( $a, $b ) {
		// If no sort, default to title.
		$orderby = ! empty( $_REQUEST['orderby'] ) ? wp_unslash( $_REQUEST['orderby'] ) : 'title'; // WPCS: Input var ok.
		// If no order, default to asc.
		$order = ! empty( $_REQUEST['order'] ) ? wp_unslash( $_REQUEST['order'] ) : 'asc'; // WPCS: Input var ok.
		// Determine sort order.
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );
		return ( 'asc' === $order ) ? $result : - $result;
	}

}
