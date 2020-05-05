<?php

if( ! class_exists( 'Help_Controller' ) ) {
    
    class Help_Controller extends Builder_Controller{
        
        const SLUG = 'etd-help';
        
        protected function __construct() {
            parent::__construct();
            add_action( 'admin_menu', array( &$this, 'etd_help_page' ) );
        }
        
        static public function get_instance() {
            $Inst = null;
            if( $Inst == null ) {
                $Inst = new self();
            }
            
            return $Inst;
        }
        
        public function etd_help_page() {
            add_submenu_page(
                'edit.php?post_type=' . ETD_Plugin::get_etd_post_type(),
                __( 'Help', 'email-to-download' ),
                __( 'Help', 'email-to-download' ),
                'manage_options',
                self::SLUG,
                array( &$this, 'email_to_download_help' )
            );
        }
        
        public function email_to_download_help() {
            
            $this->view(
                        'admin/help',
                        $data
                    );
            
        }
        
    }
    
    Help_Controller::get_instance();
    
}