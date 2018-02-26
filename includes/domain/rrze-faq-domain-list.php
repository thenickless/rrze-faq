<?php

namespace RRZE\Synonym\Server;

/*if(is_admin()) {
    new Synonym_Render();
}

ob_start();

class Synonym_Render {
    
    public function __construct() {
        add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
        add_action( 'admin_menu', array($this, 'rrze_synonym_client_list_table_page' ), 11);
    }
    
    public static function set_screen( $status, $option, $value ) {
        return $value;
    }
    
    public function screen_option() {

        $option = 'per_page';
        $args   = [
                'label'   => 'Synonyme',
                'default' => 20,
                'option'  => 'files_per_page'
        ];

        add_screen_option( $option, $args );
        
    }
   
    public function rrze_synonym_client_list_table_page() {
         $hook = add_submenu_page( 
           'edit.php?post_type=synonym', __( 'Show All Domains', 'rrze-synonym-server' ), __( 'Show All Domains', 'rrze-synonym-server' ), 'manage_options', 'rrze_synonym_server_options', array(&$this, 'rrze_synonym_server_list')
        );
        
        add_action( "load-$hook", [ $this, 'screen_option' ] );
        
    } 
  
    public function rrze_synonym_server_list() {
        $remote_synonyme = new RRZE_Domains(); ?>
        <form method="post">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
        <?php $remote_synonyme->prepare_items();
        ?>
            <div class="wrap">
                <div id="icon-users" class="icon32"></div>
                    <h2><?php _e( 'Domain list', 'rrze-synonym-server' ) ?></h2>
                <?php $remote_synonyme->display(); ?>
            </div>
        </form>
        <?php
    }
}

class RRZE_Domains extends \WP_List_Table {
    
    function prepare_items() {
        
        $this->_column_headers = $this->get_column_info();
        $columns = $this->get_columns();
        $this->process_bulk_action();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $data = DomainWPListTable::listDomains();
        if($data) usort( $data, array( &$this, 'usort_reorder' ) );
        $this->_column_headers = array($columns, $hidden, $sortable);
        $perPage     = $this->get_items_per_page( 'files_per_page', 10 );
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);
        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );
        if($data) {
            $items = array_slice($data,(($currentPage-1)*$perPage),$perPage);
        } else {
            $items = '';
        }
        $this->items = $items;
    }
    
    public function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="domains[]" value="%s" />', $item['id']
        );    
    }
    
    public function get_bulk_actions() {
        $actions = array(
            'delete'        => __( 'Delete domains', 'rrze-synonym-server' )
        );
        return $actions;
    }

    
    public function process_bulk_action() {
        
        if ( 'delete' === $this->current_action() ) {
            
            if(!isset($_REQUEST['domains'])) {
                $html = '<div id="message" class="updated notice is-dismissible">
			<p>' . __( 'Please selected a domain you want to delete.', 'rrze-synonym-server' ) .'</p>
                        </div>';
                echo $html;
            } else {
                $v = $_REQUEST['domains'];
                $t = get_option('registerServer');
                $c = array_flip($v);
                $res = array_diff_key($t, $c);
                update_option('registerServer', $res);
                $html = '<div id="message" class="updated notice is-dismissible">
			<p>' . __( 'Domain deleted.', 'rrze-synonym-server' ) .'</p>
                        </div>';
                echo $html;
            }
        }
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'domain'    => array('domain', false),
            'id'        => array('id',false)
        );
        return $sortable_columns;
    }
    
    function get_columns(){
        $columns = array(
            'cb'        =>  '<input type="checkbox" />',
            'id'      => 'ID',
            'domain'  => 'Domain',
        );
        return $columns;
    }
    
    function column_default( $item, $column_name ) {
        switch( $column_name ) { 
            case 'domain':
            case 'id':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }
    
    function usort_reorder( $a, $b ) {
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'id';
        $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
        $result = strcmp( $a[$orderby], $b[$orderby] );
        return ( $order === 'asc' ) ? $result : -$result;
    }
    
    function no_items() {
        _e( 'No data found!', 'rrze-synonym-server' );
        delete_option('registerServer');
    }
    
}*/

/*
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Domain_List extends \WP_List_Table {

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
		_e( 'No Glossary Servers avaliable.', 'rrze-faq' );
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
                        case 'id':
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
                        'id'        => __( 'ID', 'rrze-fau' ),
			'domain'    => __( 'Domain', 'rrze-fau' )
		];

		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'title' => array( 'title', true ),
                        'domain' => array( 'domain', true),
                       
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
                $data = DomainWPListTable::listDomains();
                $total_items  = count( $data );
                usort( $data, array( $this, 'usort_reorder' ) );
                $data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
		$this->items = $data;
                
                $this->set_pagination_args( array(
			'total_items' => $total_items,                     // WE have to calculate the total number of items.
			'per_page'    => $per_page,                        // WE have to determine how many items to show on a page.
			'total_pages' => ceil( $total_items / $per_page ), // WE have to calculate the total number of pages.
		) );
	}

	public function process_bulk_action() {

            if ( 'update' === $this->current_action() ) {

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


class DOMAIN_FAQ {

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
                
            $domain_page = add_submenu_page( 
                'edit.php?post_type=glossary', __( 'Glossary list', 'rrze-faq' ), __( 'Glossary list', 'rrze-faq' ), 'manage_options', 'rrze_faq_options', array($this, 'plugin_settings_page'));

            add_action("load-{$domain_page}", array( $this, 'screen_option'));

	}

	public function plugin_settings_page() {
        ?>
            <div class="wrap">
                <h2><?php _e( 'Domain Glossary List', 'rrze-faq' ) ?></h2>

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

		$this->faq_obj = new Domain_List();
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}

DOMAIN_FAQ::get_instance();*/
