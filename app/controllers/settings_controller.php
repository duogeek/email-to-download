<?php

if( ! class_exists( 'Settings_Controller' ) ) {
    
    class Settings_Controller extends Builder_Controller{
        
        const NONCE = 'ed_save_settings_nonce';
        const SLUG = 'email-to-download-settings';
        
        private $_options;
        
        public function __construct() {
            
            parent::__construct();
            
            $this->_options = Builder_Options::get_instance( ETD_Plugin::OPTIONS_KEY, ETD_Plugin::$defaults );
            
            add_action( 'admin_menu', array( &$this, 'etd_settings_page' ) );
            add_action( 'admin_action_ed_settings_save', array( &$this, 'ed_settings_save_cb' ) );
        }
        
        static public function get_instance() {
            $Inst = null;
            if( $Inst == null ) {
                $Inst = new self();
            }
            
            return $Inst;
        }
        
        public function etd_settings_page() {
            add_submenu_page(
                'edit.php?post_type=' . ETD_Plugin::get_etd_post_type(),
                __( 'Email to Download Settings', 'email-to-download' ),
                __( 'Settings', 'email-to-download' ),
                'manage_options',
                self::SLUG,
                array( &$this, 'email_to_download_settings' )
            );
        }
        
        public function email_to_download_settings() {
            
            $data = array();
            $data['form'] = new Builder_Form( $this->_options->get_options(), ETD_Plugin::OPTIONS_KEY );
            
            $this->view(
                        'admin/settings',
                        $data
                    );
            
        }
        
        public function ed_settings_save_cb() {
            
            if( ! Builder_Helper::check_nonce( Settings_Controller::NONCE ) ) {
                return;
            }
            
            $this->_options->set_options( $_POST[ ETD_Plugin::OPTIONS_KEY ] );
            $this->add_flush( __( 'Settings Saved.', 'email-to-download' ) );
            
            $this->redirect(
                        
                            admin_url( 'edit.php?post_type=ed_download&page=email-to-download-settings' )
                        
                    );
        }
        
    }
    
    Settings_Controller::get_instance();
    
}