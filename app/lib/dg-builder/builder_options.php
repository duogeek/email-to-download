<?php

if( ! defined( 'ABSPATH' ) ) die( 'Nice try!' );

if( ! class_exists( 'Builder_Options' ) ) {

    class Builder_Options {
            
            const OPTIONS_KEY = 'plg_settings';
            
            private static $_instance;
            private $_data = array();
            private $_key;
            private $_defaults;
    
            private function __clone () {}
            private function __construct ( $key = null, $defaults = array() ) {
                
                if( $key == null ) {
                    $this->_key = self::OPTIONS_KEY;
                }else{
                    $this->_key = $key;
                }
                
                $this->_defaults = $defaults;
                $this->_populate();
            }
            
            public static function get_instance ( $key = null, $defaults = array() ) {
                    if ( ! isset( self::$_instance ) ) self::$_instance = new self( $key, $defaults );
                    return self::$_instance;
            }
            
            /**
             * @return array
             */
            public function get_options () {
                    return $this->_data;
            }
    
            public function get_default_options() {
                    return $this->_defaults;
            }
            
            /**
             * @param string $name Option name
             * @param mixed $default Optional default return value
             * @return mixed Option value, or $default
             */
            public function get_option ( $name, $default = false ) {
                return isset( $this->_data[$name] ) ? $this->_data[$name] : $default;
            }
            
            public function set_option ( $name, $value ) {
                $this->_data[$name] = $value;
            }
            
            /**
             * Sets and stores options.
             * @param array $values A hash of values to be stored.
             */
            public function set_options ( $values ) {
                    if ( ! $values ) return false;
                    foreach ( $values as $name => $value ) {echo $value;
                            $this->set_option( $name, $value );
                    }
                    $this->update();
            }
    
            public function update () {
                    return update_option( $this->_key, $this->_data );
            }
            
            private function _populate () {
                    $this->_data = get_option( $this->_key, $this->get_default_options() );
                    
                    /*foreach( $this->_data as $key => $val ) {
                        if( trim( $val ) == '' ) {
                            $this->_data[$key] = $this->_defaults[$key];
                        }
                    }*/
                    
                    $this->_data = wp_parse_args( $this->_data, $this->_defaults );
            }
    }

}