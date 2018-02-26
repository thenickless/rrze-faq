<?php

namespace RRZE\Glossar\Server;

class AddDomain {
    
    public function __construct() {
        add_action('admin_menu', array($this,'rrze_faq_add_server'));
        add_action('admin_init', array($this, 'rrze_faq_server_settings'));
        //delete_option('urls');
        //delete_option(' serversynonyms');
    }
    
    public function rrze_faq_add_server() {
        add_submenu_page( 
            'edit.php?post_type=glossary', 
                __( 'Add New Domain', 'rrze-faq' ), 
                __( 'Add New Domain', 'rrze-faq' ), 
                'manage_options', 
                'rrze_faq_server_add_options', 
                array(&$this, 'rrze_faq_server_add_settings')
        );
    }
    
    function rrze_faq_server_add_settings() { 
    //delete_option('registerDomain');?>
        <div class="wrap">
            <h2><?php  _e( 'Register new domain', 'rrze-faq' ) ?></h2>
            <h2><?php  _e( 'Register the domain from which you want to get glossaries.', 'rrze-faq' ) ?></h2>
            <form method="post">
                <?php settings_fields('rrze_faq_add_server_options_group');?>
                <?php do_settings_sections('rrze_faq_server_plugin');?>
                <p>
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save') ?>"  />
                </p>
            </form>
        </div>
    <?php if(!empty($_POST)) $this->rrze_faq_server_register_save($_POST);
    }
    
    function rrze_faq_server_settings(){

        register_setting( 
            'rrze_faq_add_server_options_group',
            'rrze_faq_add_server_options' 
        );
        
        add_settings_section(
            'rrze_faq_add_server_main', 
            '', 
            array($this, 'rrze_faq_server_section_text'), 
            'rrze_faq_server_plugin'
        );
        
        add_settings_field(
            'register_textarea', 
            __( 'Domain:', 'rrze-faq' ), 
            array($this, 'rrze_faq_server_textarea_callback'), 
            'rrze_faq_server_plugin', 
            'rrze_faq_add_server_main'
        );

    }
    
    function rrze_faq_server_section_text() {
        echo '';
    }
    
    function rrze_faq_server_textarea_callback() {
	$options = get_option('rrze_faq_server_options');
	echo "https://www.<input type='text' placeholder='z.B. musik.fau.de' size='50' id='rrze_faq_server_input' name='rrze_faq_server_options[register_server]' />";
    }
    
    function rrze_faq_server_register_save($value) {
        
        $url = $value['rrze_faq_server_options']['register_server'];
         
        if(!empty($url)) {
            if( get_option('registerDomain') === false ) {
                $reg = array();
                $reg[1] = $url;
                add_option('registerDomain', $reg);
            } else {
                $server = get_option('registerDomain');
                if(!in_array($url, $server)) {
                    array_push($server, $url);
                    update_option('registerDomain', $server);
                }
            }
            
            $html = '<div id="message" class="updated notice is-dismissible">
                    <p>' . __( 'Domain added. ', 'rrze-faq' ) . '
                    <a href="admin.php?page=rrze_faq_server_options">Zur Übersicht</a></p>    
                    </div>';
            echo $html;
        }
        
        print_r(get_option('registerDomain'));
    }
}