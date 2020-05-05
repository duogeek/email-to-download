<?php

if( ! class_exists( 'Subscribers_Controller' ) ) {
    
    class Subscribers_Controller extends Builder_Controller{
        
        const SLUG = 'email-to-download-subscribers';
        
        private $_options;
        
        private $_db;
        
        protected function __construct() {
            
            global $wpdb;
            $this->_db = $wpdb;
            $this->_tbl_name = $this->_db->prefix . ETD_Plugin::ETD_MANAGER_TBL;
            
            $this->_options = Builder_Options::get_instance( ETD_Plugin::OPTIONS_KEY, ETD_Plugin::$defaults );
            
            parent::__construct();
            
            add_action( 'admin_menu', array( &$this, 'etd_subscribers_page' ) );
        }
        
        static public function get_instance() {
            $Inst = null;
            if( $Inst == null ) {
                $Inst = new self();
            }
            
            return $Inst;
        }
        
        public function etd_subscribers_page() {
            add_submenu_page(
                'edit.php?post_type=' . ETD_Plugin::get_etd_post_type(),
                __( 'Subscribers', 'email-to-download' ),
                __( 'Subscribers', 'email-to-download' ),
                'manage_options',
                self::SLUG,
                array( &$this, 'email_to_download_Subscribers' )
            );
        }
        
        public function email_to_download_Subscribers() {
            
            if( isset( $_REQUEST['delete_subscriber'] ) ) {
                $this->_db->delete(
                    $this->_tbl_name,
                    array(
                        'id' => $_REQUEST['delete_subscriber']
                    )
                );
            }
            
            $data = array();
            $sql = "SELECT id, email, primary_time, GROUP_CONCAT(download_id SEPARATOR ',') AS items FROM {$this->_tbl_name} GROUP BY(email)";
            $data['emails'] = $this->_db->get_results( $sql, OBJECT );
            
            foreach( $data['emails'] as $key => $email ) {
                $ids = explode( ',', $email->items );
                $data['emails'][$key]->items = array();
                $temp = array();
                foreach( $ids as $id ) {
                    if( in_array( $id, $temp ) ) continue;
                    $post = get_post( $id );
                    $data['emails'][$key]->items[] = '<a href="' . get_permalink( $id ) . '" target="_blank">' . $post->post_title . '</a>';
                    $temp[] = $id;
                }
            }
            
            $this->view(
                'admin/subscribers',
                $data
            );
            
        }
        
    }
    
    Subscribers_Controller::get_instance();
    
}