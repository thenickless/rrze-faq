<?php

namespace RRZE\Synonym\Server;

if(is_admin()) {
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
    
}
?>
