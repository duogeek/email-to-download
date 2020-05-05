<?php

if( ! class_exists( 'Builder_Helper' ) ) {
    
    class Builder_Helper{
        
        static public function objectToArray( $object ) {
            if( ! is_object( $object ) && ! is_array( $object ) )
                return $object;
            
            return array_map( array( __CLASS__, 'objectToArray' ), (array) $object );
        }
        
        static public function select_blog( $site_id = null ) {
            
            if( is_multisite() ) {
                if( null === $site_id ) {
                    if ( defined( 'BLOG_ID_CURRENT_SITE' ) ) {
                        $site_id = BLOG_ID_CURRENT_SITE;
                    }else{
                        $site_id = 1;
                    }
                }
                
                switch_to_blog( $site_id );
            }
            
        }
        
        static public function revert_blog() {
            
            if( is_multisite() ) {
                restore_current_blog();
            }
            
        }
        
        static public function is_true( $value ) {
            
            if ( false === $value || null === $value || '' === $value ) {
                return false;
            } elseif ( true === $value ) {
                return true;
            } elseif ( is_numeric( $value ) ) {
                $value = intval( $value );
                return $value != 0;
            } elseif ( is_string( $value ) ) {
                $value = strtolower( trim( $value ) );
                return in_array(
                    $value,
                    array( 'true', 'yes', 'on', '1' )
                );
            }
            return false;
            
        }
        
        static public function is_false( $value ) {
            return ! self::is_true( $value );
        }
        
        static public function dump( $var, $echo = true ) {
            ob_start();
            echo "<pre>";
            print_r($var);
            echo "</pre>";
            $dump = ob_get_contents();
            ob_end_clean();
            
            if( $echo ) {
                echo $dump;
            }else{
                return $dump;
            }
        }
        
        static public function check_nonce( $action, $field = null ) {
            
            if( $field == null ) {
                $field = $action . '_field';
            }
            
            if( ! isset( $_POST[$field] ) ) {
                return false;
            }
            
            if ( ! wp_verify_nonce( $_POST[$field], $action ) ) {
                return false;
            }
            
            return true;
            
        }
        
    }
}