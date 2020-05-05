<?php

if( ! defined( 'BL_WP_LOAD_TIMES' ) ) define( 'BL_WP_LOAD_TIMES', 2 );

if( ! class_exists( 'Builder_Flush' ) ) {
    
    class Builder_Flush{
        
        const FLUSH_MESSAGES = 'bl_flush_msg';
        static private $_flush_messages = array();
        static private $_allowed_classes = array( 'error', 'updated', 'update-nag' );
        static private $_default_class = 'updated';
        
        private function __construct() {
            add_action( 'admin_notices', array( &$this, 'show_flush_messages' ) );
            add_action( 'init', array( &$this, 'session_init' ) );
        }
        
        static public function get_instance() {
            static $Inst = null;
            if( $Inst == null ) {
                $Inst = new self();
            }
            
            return $Inst;
        }
        
        public function session_init() {
            if( ! session_id() ) {
                session_start();
            }
            
            $_SESSION[self::FLUSH_MESSAGES] = array();
        }
        
        public function add_flush( $msg = array() ) {
     
            if( empty( $msg ) ) return;
            
            if( ! is_array( $msg ) ) {
                $msg = array(
                            'msg' => $msg,
                            'dismissable' => true
                        );
            }
            
            if( Builder_Helper::is_true( $msg['dismissable'] ) ) {
                $msg['dismissable'] = true;
            }
            
            self::$_flush_messages[] = $msg;
            
            $this->_update_all_messages();
            
        }
        
        public function show_flush_messages() {
            
            self::$_flush_messages = $this->_get_all_messages();
            
            if( ! is_array( self::$_flush_messages ) || empty( self::$_flush_messages ) ) {
                return;
            }
            $_SESSION['times']++;
            
            foreach( self::$_flush_messages as $flush_message ) {
                $class = $this->_get_notice_class( $flush_message );
                ?>
                <div class="notice <?php echo $class ?>">
                    <p><?php echo $flush_message['msg']; ?></p>
                </div>
                <?php
            }
            
            $this->_delete_flush();
            
        }
        
        private function _get_notice_class( $flush_message ) {
            
            $class = '';
            if( isset( $flush_message['class'] ) ) {
                if( in_array( $flush_message['class'], self::$_allowed_classes ) ) {
                    $class = $flush_message['class'];
                }else{
                    $class = self::$_default_class;
                }
            }else{
                $class = self::$_default_class;
            }
            
            if( Builder_Helper::is_true( $flush_message['dismissable'] ) ) {
                $class .= ' is-dismissible';
            }
            
            return $class;
        }
        
        private function _get_all_messages() {
            
            if( defined( 'BL_FLUSH_USE_DB ' ) && BL_FLUSH_USE_DB ) {
                get_option( self::FLUSH_MESSAGES );
            }else{
                return isset( $_SESSION[self::FLUSH_MESSAGES] ) ? $_SESSION[self::FLUSH_MESSAGES] : '';
            }
            
        }
        
        private function _update_all_messages() {
            
            if( defined( 'BL_FLUSH_USE_DB ' ) && BL_FLUSH_USE_DB ) {
                update_option( self::FLUSH_MESSAGES, self::$_flush_messages );
            }else{
                $_SESSION[self::FLUSH_MESSAGES] = self::$_flush_messages;
            }
            
        }
        
        private function _delete_flush() {
            
            if( $_SESSION['times'] > ( ( int ) BL_WP_LOAD_TIMES - 1 ) ) {
                if( defined( 'BL_FLUSH_USE_DB ' ) && BL_FLUSH_USE_DB  ) {
                    delete_option( self::FLUSH_MESSAGES );
                }else{
                    unset( $_SESSION[self::FLUSH_MESSAGES] );
                    unset( $_SESSION['times'] );
                }
            }
            
        }
        
    }
    
}