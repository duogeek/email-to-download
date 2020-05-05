<?php
/**
 * ED ShortCodes
 *
 * @package Email to Download
 */
if( ! class_exists( 'Shortcodes_Controller' ) ) {
    
    class Shortcodes_Controller extends Builder_Controller{
        
        public function __construct() {
            add_shortcode( 'ed_download_file', array( &$this, 'process_shortcode' ) );
        }
        
        static public function get_instance() {
            static $Inst = null;
            if( $Inst == null ) {
                $Inst = new self();
            }
            
            return $Inst;
        }
        
        public function process_shortcode( $atts ) {
            $atts = shortcode_atts( array(
		'title' => 'yes',
                'show_content' => 'yes',
		'id' => '',
                'style' => 'normal',
                'tagline' => __( 'Please provide an email address where we should send the download link.', 'email-to-download' ),
                'email_placeholder' => __( 'Your email here', 'email-to-download' ),
                'submit' => __( 'Get Download Link', 'email-to-download' )
            ), $atts, 'ed_download_file' );
            
            extract( $atts );
            
            $data = $atts;
            $data['content'] = $show_content;
            
            if( $id == '' ) return __( 'Please provie an ID of your file.', 'email-to-download' );
            
	    $data['post'] = Download_Model::get_instance( $id )->post;
	    $data['ed_file'] = Download_Model::get_instance( $id )->ed_file;
            $data['id'] = $id;
	    
            return apply_filters(
                        'etd_dw_sc_form_' . $style,
                        $this->view( 'shortcodes/' . $style, $data, false ),
                        $atts
                    );
        }
    }
    
    Shortcodes_Controller::get_instance();
}