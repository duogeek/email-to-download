<?php

class Download_Model {
    
    private $ID;
    
    public $ed_file;
    
    public $post;
    
    private function __construct( $id ) {
        $this->ID = $id;
        $this->post = get_post( $id );
        
        $etd_meta = ( array ) get_post_meta( $this->ID, 'etd_meta', true );
        $this->ed_file = $etd_meta['etd_uploader'];
    }
    
    static public function get_instance( $id ) {
        return new self( $id );
    }
    
    static public function get_all()
    {
        return get_posts( array( 'post_type' => ETD_Plugin::get_etd_post_type() ) );
    }
    
}