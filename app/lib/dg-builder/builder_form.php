<?php

if( ! defined( 'ABSPATH' ) ) die( 'Nice try!' );

if( ! class_exists( 'Builder_Form' ) ) {
    /**
     * Class Builder_Form
     *
     * @since 1.0.0
     */
    class Builder_Form{
        
        private $data = array();
        
        private $identifier;
        
        public function __construct( $data = array(), $identifier = 'bf_form_' ) {
            $this->data = $data;
            $this->identifier = $identifier;
        }
        
        public function open( $args = array(), $echo = true ) {
            $default = array(
                'url' => '#',
                'method' => 'POST',
                'atts' => array()
            );
            
            $args = wp_parse_args( $args, $default );
            $atts = self::build_atts( $args['atts'] );
            
            $ret = apply_filters(
                    'sm_form_open',
                    sprintf(
                        '<form action="%s" method="%s" %s>',
                        $args['url'],
                        $args['method'],
                        $atts
                    ),
                    $args,
                    $this
                );
            
            if( $echo ) {
                echo $ret;
            }else{
                return $ret;
            }
        }
        
        public function close( $echo = true ) {
            
            $ret = apply_filters(
                    'sm_form_close',
                    '</form>',
                    $this
                );
            
            if( $echo ) {
                echo $ret;
            }else{
                return $ret;
            }
        }
        
        public function nonce( $action, $field = null ) {
            if( $field == null ) {
                wp_nonce_field( $action, $action . '_field' );
            }else{
                wp_nonce_field( $action, $field );
            }
        }
        
        public function label( $field, $args = array(), $echo = true ) {
            $default = array(
                'for' => '',
                'text' => '',
                'atts' => array()
            );
            $args['for'] = $this->build_id( $field );
            
            $args = wp_parse_args( $args, $default );
            $atts = self::build_atts( $args['atts'] );
            
            $ret = apply_filters(
                    'sm_form_label',
                    sprintf( '<label for="%s" %s >%s</label>', esc_attr( $args['for'] ), $atts, esc_html( $args['text'] ) ),
                    $field,
                    $args,
                    $this
                );
            
            if( $echo ) {
                echo $ret;
            }else{
                return $ret;
            }
        }
        
        public function input( $type, $field, $args = array(), $echo = true ) {
            $default = array(
                'name' => '',
                'value' => '',
                'atts' => array()
            );
            
            $args['name'] = $this->build_name( $field );
            $args['value'] = ! isset( $args['value'] ) ? $this->data[$field] : $args['value'];
            $args['atts']['id'] = isset( $args['atts']['id'] ) ? $args['atts']['id'] : $this->build_id( $field );

            $args = wp_parse_args( $args, $default );
            $atts = self::build_atts( $args['atts'] );
            
            $ret = apply_filters(
                    'sm_form_' . $type,
                    sprintf( '<input type="' . $type . '" value="%s" name="%s" %s />', esc_attr( $args['value'] ), esc_attr( $args['name'] ),  $atts ),
                    $field,
                    $args,
                    $this
                );
        
            if( $echo ) {
                echo $ret;
            }else{
                return $ret;
            }
        }
        
        public function hidden( $field, $args = array(), $echo = true ) {
            $ret = $this->input( 'hidden', $field, $args, false );
            
            if( $echo ) {
                echo $ret;
            }else{
                return $ret;
            }
        }
        
        public function text( $field, $args = array(), $echo = true ) {
            $ret = $this->input( 'text', $field, $args, false );
        
            if( $echo ) {
                echo $ret;
            }else{
                return $ret;
            }
        }
        
        public function password( $field, $args = array(), $echo = true ) {
            $ret =  $this->input( 'text', $field, $args, false );
        
            if( $echo ) {
                echo $ret;
            }else{
                return $ret;
            }
        }
        
        public function email( $field, $args = array(), $echo = true ) {
            $ret = $this->input( 'email', $field, $args, false );
        
            if( $echo ) {
                echo $ret;
            }else{
                return $ret;
            }
        }
        
        public function number( $field, $args = array(), $echo = true ) {
            $ret = $this->input( 'number', $field, $args, false );
        
            if( $echo ) {
                echo $ret;
            }else{
                return $ret;
            }
        }
        
        public function file( $field, $args = array(), $echo = true ) {
            $ret = $this->input( 'file', $field, $args, false );
        
            if( $echo ) {
                echo $ret;
            }else{
                return $ret;
            }
        }
        
        public function submit( $field, $args = array(), $echo = true ) {
            $ret = $this->input( 'submit', $field, $args, false );
        
            if( $echo ) {
                echo $ret;
            }else{
                return $ret;
            }
        }
        
        public function textarea( $field, $args = array(), $echo = true ) {
            $default = array(
                'name' => '',
                'value' => '',
                'atts' => array()
            );
            
            $args['name'] = $this->build_name( $field );
            $args['value'] = ! isset( $args['value'] ) ? $this->data[$field] : $args['value'];
            $args['atts']['id'] = isset( $args['atts']['id'] ) ? $args['atts']['id'] : $this->build_id( $field );

            $args = wp_parse_args( $args, $default );
            $atts = self::build_atts( $args['atts'] );
            
            $ret = apply_filters(
                    'sm_form_textarea',
                    sprintf( '<textarea name="%s" %s >%s</textarea>', esc_attr( $args['name'] ), $atts, esc_attr( $args['value'] ) ),
                    $field,
                    $args,
                    $this
                );
            
            if( $echo ) {
                echo $ret;
            }else{
                return $ret;
            }
        }
        
        public function select( $field, $args = array(), $echo = true ){
            $default = array(
                'name' => '',
                'selected' => array(),
                'atts' => array(),
                'data' => array(),
                'nameless' => ''
            );
            
            $args['name'] = $this->build_name( $field );
            $args['atts']['id'] = isset( $args['atts']['id'] ) ? $args['atts']['id'] : $this->build_id( $field );
            
            $selected = ! isset( $args['value'] ) ? $this->data[$field] : $args['value'];
            if ( ! is_array( $selected ) ) {
                $selected = explode( ',', $selected );
            }
            $args['selected'] = $selected;
            
            if ( isset( $args['atts']['multiple'] ) && $args['atts']['multiple'] == 'multiple' ) {
                $args['name'] .= '[]';
            }
            
            $args = wp_parse_args( $args, $default );
            $atts = self::build_atts( $args['atts'] );
            
            $html = sprintf( '<select name="%s" %s>', $args['name'], $atts );
            if( $args['nameless'] ){
                $html .= sprintf( '<option value="">%s</option>', $args['nameless'] );
            }
            foreach( $args['data'] as $key => $val ){
                $checked = in_array( $key, $args['selected'] ) ? 'selected="selected"' : null;
                $html .= sprintf( '<option value="%s" %s >%s</option>', esc_attr( $key ), $checked, esc_html( $val ) );
            }
            $html .= '</select>';
            
            $ret = apply_filters(
                    'sm_form_select',
                    $html,
                    $field,
                    $args,
                    $this
                );
        
            if( $echo ) {
                echo $ret;
            }else{
                return $ret;
            }
        }
        
        public function radio( $field, $args = array(), $echo = true ){
            $default = array(
                'name' => '',
                'value' => '',
                'checked' => false,
                'atts' => array()
            );
            
            $args['name'] = $this->build_name( $field );
            $args['atts']['id'] = isset( $args['atts']['id'] ) ? $args['atts']['id'] : $this->build_id( $field );
            if( $this->data[$field] == $args['value'] ) {
                $args['checked'] = true;
            }

            $args = wp_parse_args( $args, $default );
            $atts = $this->build_attrs( $args['attributes'] );

            $ret = apply_filters(
                    'sm_form_radio',
                    sprintf( '<input type="radio" name="%s" value="%s" %s %s>', esc_attr( $args['name']) , esc_attr( $args['value'] ), $args['checked'] == true ? 'checked' : null, $atts ),
                    $field,
                    $args,
                    $this
                );
            
            if( $echo ) {
                echo $ret;
            }else{
                return $ret;
            }
        }
        
        public function checkbox( $field, $args = array(), $echo = true ){
            $default = array(
                'name' => '',
                'value' => '',
                'checked' => false,
                'atts' => array()
            );
            
            $args['name'] = $this->build_name( $field );
            $args['atts']['id'] = isset( $args['atts']['id'] ) ? $args['atts']['id'] : $this->build_id( $field );
            if( $this->data[$field] == $args['value'] ) {
                $args['checked'] = true;
            }

            $args = wp_parse_args( $args, $default );
            $atts = $this->build_attrs( $args['attributes'] );

            $ret = apply_filters(
                    'sm_form_checkbox',
                    sprintf( '<input type="checkbox" name="%s" value="%s" %s %s>', esc_attr( $args['name']) , esc_attr( $args['value'] ), $args['checked'] == true ? 'checked' : null, $atts ),
                    $field,
                    $args,
                    $this
                );
        
            if( $echo ) {
                echo $ret;
            }else{
                return $ret;
            }
        }
        
        public function build_atts( $data = array() ) {
            
            $atts = '';
            foreach( $data as $key => $val ) {
                $atts .= sprintf( ' %s="%s" ', esc_attr( $key ), esc_attr( $val ) );
            }
            
            return apply_filters(
                    'sm_form_build_atts',
                    $atts,
                    $data,
                    $this
                );
        }
        
        public function build_id( $id ) {
            return sanitize_title( $this->identifier . '-' . $id );
        }
        
        public function build_name( $name ) {
            return $this->identifier . "[" . sanitize_title( $name ) . "]";
        }
        
        public function posts( $field, $echo = true ) {
            $data = array();
            $posts = get_posts();
            foreach( $posts as $post ) {
                $data[$post->ID] = $post->post_title;
            }
            
            if( $echo ) {
                $this->select( $field, array( 'data' => $data ) );
            }else{
                return $this->select( $field, array( 'data' => $data ), false );
            }
        }
        
        public function pages( $field, $echo = true ) {
            $data = array();
            $posts = get_pages();
            foreach( $posts as $post ) {
                $data[$post->ID] = $post->post_title;
            }
            
            if( $echo ) {
                $this->select( $field, array( 'data' => $data ) );
            }else{
                return $this->select( $field, array( 'data' => $data ), false );
            }
        }
        
    }
}