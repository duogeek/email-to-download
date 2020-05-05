<?php

if( ! class_exists( 'Builder_Controller' ) ) {
    
    class Builder_Controller extends Builder_Flush{
        
        public function __construct() {
            parent::get_instance();
            
            add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
        }
        
        public function admin_notices() {
            if ( empty( $_REQUEST['bl_msg'] ) ) {
                return;
            }
            
            $msg = base64_decode( $_REQUEST['bl_msg'] );
            $class = isset( $_REQUEST['bl_type'] ) ? $_REQUEST['bl_type'] : 'updated';
            
            if ( $msg ) {
                ?>
                <div id="message" class="<?php echo $class; ?> notice notice-success is-dismissible">
                    <p><?php echo wp_kses_post( $_REQUEST['bl_msg'] ); ?></p>
                    <button type="button" class="notice-dismiss"></button>
                </div>
                <?php
            }
        }
        
        public function view( $file, $params = array(), $echo = true ) {
            
            if( ! is_array( $params ) ) {
                $params = Builder_Helper::objectToArray( $params );
            }
            extract( $params );
            
            $reflector = new ReflectionClass( get_class( $this ) );
            $base_path = substr( $reflector->getFileName(), 0, stripos( $reflector->getFileName(), 'controllers' ) );
            $target = $base_path . 'views/' . $file . '.php';
            
            ob_start();
            if( file_exists( $target ) ) {
                include $target;
            }else{
                echo 'View file doesn\'t exist';
            }
            $output = ob_get_clean();
            
            if( $echo ) {
                echo $output;
            }else{
                return $output;
            }
            
        }
        
        public function redirect( $url, $safe = false ) {
            if( $safe ) {
                wp_safe_redirect( esc_url_raw( $url ) );
            }else{
                wp_redirect( esc_url_raw( $url ) );
            }
        }
        
    }
    
}