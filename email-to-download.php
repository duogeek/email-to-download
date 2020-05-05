<?php
/**
Plugin Name: Email to Download
Plugin URI:  https://wordpress.org/plugins/email-to-download/
Version:     3.0.4
Description: A plugin that helps you to collect email before download
Author:      duogeek
Author URI:  http://duogeek.ca
License:     GNU General Public License (Version 2 - GPLv2)
Text Domain: email-to-download
*/

if( ! defined( 'ED_HACK_MSG' ) ) define( 'ED_HACK_MSG', __( 'Sorry cowboy! This is not your place', 'email-to-download' ) );

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( ED_HACK_MSG );

/**
 * Defining constants
 */
if( ! defined( 'ED_VERSION' ) ) define( 'ED_VERSION', '3.0.0' );
if( ! defined( 'ED_MENU_POSITION' ) ) define( 'ED_MENU_POSITION', 75 );
if( ! defined( 'ED_PLUGIN_DIR' ) ) define( 'ED_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
if( ! defined( 'ED_FILES_DIR' ) ) define( 'ED_FILES_DIR', ED_PLUGIN_DIR . 'app' );
if( ! defined( 'ED_PLUGIN_URI' ) ) define( 'ED_PLUGIN_URI', plugins_url( '', __FILE__ ) );
if( ! defined( 'ED_FILES_URI' ) ) define( 'ED_FILES_URI', ED_PLUGIN_URI . '/app' );

require_once ED_FILES_DIR . '/lib/dg-builder/builder_loader.php';
require_once ED_FILES_DIR . '/models/download_model.php';
require_once ED_FILES_DIR . '/controllers/shortcodes_controller.php';

if ( ! class_exists( 'ETD_Plugin' ) ) {
    
    class ETD_Plugin{
        
        const OPTIONS_KEY = 'etd_settings_option_key';
        
        const AJAX_ACTION = 'etd_ajax_dw_submit';
        
        const ETD_MANAGER_TBL = 'etd_manager';
        
        static public $defaults = array();
        
        private $_options;
        
        private $_db;
        
        private $_tbl_name;
        
        private $_slug;
        
        private function __construct() {
            global $wpdb;
            $this->_db = $wpdb;
            $this->_tbl_name = $this->_db->prefix . self::ETD_MANAGER_TBL;
            
            if ( ! defined( 'ED_DOWNLOAD_PAGE_SLUG' ) ) {
                $page = get_page_by_path( 'download' );
                
                if ( $page ) {
                    $this->_slug = 'etd-download';
                } else {
                    $this->_slug = 'download';
                }
            } else {
                $this->_slug = ED_DOWNLOAD_PAGE_SLUG;
            }
            
            self::$defaults = array(
                            'dw_expire' => 5,
                            'dw_page' => 1,
                            'notify_email_subject' => __( 'Here is your download link', 'email-to-download' ),
                            'email_content' => __( 'Dear User,

Thank you for downloading our product. We have generated the download link for you. Please click on the following link to download the product. Please note that, this link will be active for {{download_expire}} hour(s).

Download here: {{download_link}}', 'email-to-download' ),
                            'notify_email_from_email' => get_bloginfo( 'admin_email' ),
                            'notify_email_from_name' => get_bloginfo( 'name' )
                        );
            $this->_options = Builder_Options::get_instance( self::OPTIONS_KEY, self::$defaults );
            
            add_action( 'init', array( &$this, 'init' ) );
            add_action( 'init', array( &$this, 'register_etd_post_type' ) );
            add_action( 'init', array( &$this, 'etd_load_textdomain' ), 999 );
            add_action( 'init', array( &$this, 'add_dw_rewrite_tag' ) );
            add_action( 'admin_footer', array( &$this, 'print_js' ) );
            add_action( 'add_meta_boxes', array( &$this, 'etd_meta_box' ), 20 );
            add_filter( 'media_buttons', array( &$this, 'shortcode_buttons' ) );
            add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts_cb' ) );
            add_action( 'wp_enqueue_scripts', array( &$this, 'wp_enqueue_scripts_cb' ) );
            add_action( 'save_post_' . self::get_etd_post_type(), array( &$this, 'etd_save_meta_box_data' ) );
            add_action( 'wp_ajax_' . self::AJAX_ACTION, array( &$this, 'etd_ajax_dw_submit_cb' ) );
            add_action( 'wp_ajax_nopriv_' . self::AJAX_ACTION, array( &$this, 'etd_ajax_dw_submit_cb' ) );
            add_action( 'wp_ajax_etd_ajax_notice', array( &$this, 'etd_ajax_notice' ) );
            add_action( 'template_redirect', array( &$this, 'ed_check_dw_link' ) );
            add_action( 'admin_notices', array( &$this, 'mc_admin_notices' ) );
            
            register_activation_hook( __FILE__, array( &$this, 'install_required_tables' ) );
        }
        
        static public function get_instance() {
            static $Inst = null;
            if ( $Inst == null ) {
                $Inst = new self();
            }
            
            return $Inst;
        }
        
        public function mc_admin_notices() {
            $mc_cookie = isset( $_COOKIE['mc_cookie'] ) ? $_COOKIE['mc_cookie'] : false;
            $mp_cookie = isset( $_COOKIE['mp_cookie'] ) ? $_COOKIE['mp_cookie'] : false;
            
            if ( ! $mc_cookie )
            {
                ?>
                <div class="updated notice is-dismissible etd_notice etd_mc">
                    <p><?php _e( 'Want to integrate mailchimp with Email to Download plugin? <a href="https://duogeek.com/products/plugins/mailchimp-integration-email-to-download/" target="_blank">Build your email list in Mailchimp now!</a>', 'my-text-domain' ); ?></p>
                </div>
            <?php } ?>
            <?php if ( ! $mp_cookie ) { ?>
                <div class="updated notice is-dismissible etd_notice etd_mp">
                    <p><?php _e( 'Want to integrate mailpoet with Email to Download plugin? <a href="https://duogeek.com/products/plugins/mailpoet-integration-email-to-download/" target="_blank">Build your email list in mailpoet now!</a>', 'my-text-domain' ); ?></p>
                </div>
                <?php
            }
        }
        
        public function print_js()
        {
            ?>
            <script type="text/javascript">
            jQuery ( function( $ ) {
                $( document ).on( 'click', '.etd_notice .notice-dismiss', function() {
                    var target = $( this ).closest( '.etd_notice' );
                    var option = '';
                    
                    if ( target.hasClass( 'etd_mc' ) )
                    {
                        option = 'mc';
                    }
                    else if ( target.hasClass( 'etd_mp' ) )
                    {
                        option = 'mp';
                    }
                    
                    $.post(
                        '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                        {
                            action: 'etd_ajax_notice',
                            option: option
                        },
                        function( response ){}
                    );
                } );
            } );
            </script>
            <?php
        }
        
        public function etd_ajax_notice()
        {
            $time = defined( ETD_COOKIE_TIME ) && ETD_COOKIE_TIME && ETD_COOKIE_TIME < 8 ? ETD_COOKIE_TIME : 7;
            
            if ( 'mc' == $_POST['option'] )
            {
                setcookie( 'mc_cookie', 1, time() + ( 86400 * $time ), '/' );
            }
            elseif ( 'mp' == $_POST['option'] )
            {
                setcookie( 'mp_cookie', 1, time() + ( 86400 * $time ), '/' );
            }
            
            exit;
        }
        
        public function add_dw_rewrite_tag() {
            add_rewrite_tag( "%{$this->_slug}%", '(\w+)' );
            add_rewrite_rule(
		        "^{$this->_slug}/(\w+)",
		        'index.php?' . $this->_slug . '=$matches[1]',
		        'top'
            );
            
            //flush_rewrite_rules();
        }
        
        public function admin_enqueue_scripts_cb() {
            wp_enqueue_script( 'etd_admin', ED_FILES_URI . '/assets/js/admin/ed-admin.js' );
        }
        
        public function wp_enqueue_scripts_cb() {
            wp_register_script( 'etd_colobor_js', ED_FILES_URI . '/assets/colorbox/jquery.colorbox-min.js', array( 'jquery' ) );
            wp_enqueue_script( 'etd_colobor_js' );
            
            wp_enqueue_style( 'etd_colobor_css', ED_FILES_URI . '/assets/colorbox/colorbox.css' );
            
            wp_register_script( 'etd_front_js', ED_FILES_URI . '/assets/js/public/ed-front.js', array( 'jquery' ) );
            wp_localize_script( 'etd_front_js', 'obj', array(
                                                            'adminAjax' => admin_url( 'admin-ajax.php' ),
                                                            'ajaxNonce' => wp_create_nonce( 'ed-ajax-form-submit' ),
                                                            'emailError' => __( 'Please provide a correct email address', 'email-to-download' ),
                                                            'successMSG' => sprintf( __( 'Thank you! Please check your inbox (or spam) for download link email. This link will be expired for %d hour(s).', 'email-to-download' ), $this->_options->get_option( 'dw_expire' ) )
                                                        ) );
            wp_enqueue_script( 'etd_front_js' );
            wp_enqueue_style( 'etd_style_js', ED_FILES_URI . '/assets/css/public/ed-front.css' );
        }
        
        public function install_required_tables() {
            $charset_collate = $this->_db->get_charset_collate();

            $sql = "CREATE TABLE IF NOT EXISTS {$this->_tbl_name} (
              id INT(200) NOT NULL AUTO_INCREMENT,
              file_url TEXT,
              primary_time DATETIME,
              email VARCHAR(200),
              download_id INT(200),
              UNIQUE KEY id (id)
            )";
            
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
        }
        
        public function init() {
            add_thickbox();
            
            require_once ED_FILES_DIR . '/controllers/settings_controller.php';
            require_once ED_FILES_DIR . '/controllers/subscribers_controller.php';
            require_once ED_FILES_DIR . '/controllers/help_controller.php';
        }
        
        public function etd_load_textdomain() {
            load_plugin_textdomain( 'email-to-download', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        }
        
        static public function get_etd_post_type() {
            return apply_filters(
                            'ed_post_type',
                            'ed_download'
                        );
        }
        
        public function register_etd_post_type() {
            $labels = array(
		        'name'               => _x( 'ETD Downloads', 'post type general name', 'email-to-download' ),
		        'singular_name'      => _x( 'ETD Download', 'post type singular name', 'email-to-download' ),
		        'menu_name'          => _x( 'ETD Downloads', 'admin menu', 'email-to-download' ),
		        'name_admin_bar'     => _x( 'ETD Download', 'add new on admin bar', 'email-to-download' ),
                'add_new'            => _x( 'Add New', 'download', 'email-to-download' ),
                'add_new_item'       => __( 'Add New Download', 'email-to-download' ),
                'new_item'           => __( 'New Download', 'email-to-download' ),
                'edit_item'          => __( 'Edit Download', 'email-to-download' ),
                'view_item'          => __( 'View Download', 'email-to-download' ),
                'all_items'          => __( 'All Downloads', 'email-to-download' ),
                'search_items'       => __( 'Search Downloads', 'email-to-download' ),
                'parent_item_colon'  => __( 'Parent Downloads:', 'email-to-download' ),
                'not_found'          => __( 'No Downloads found.', 'email-to-download' ),
                'not_found_in_trash' => __( 'No Downloads found in Trash.', 'email-to-download' )
            );
            
            $labels = apply_filters( 'ed_post_type_label', $labels );
            
            $args = array(
                'labels'             => $labels,
                'description'        => __( 'Description.', 'email-to-download' ),
                'exclude_from_search'=> true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'show_in_nav_menus'  => false,
                'show_in_admin_bar'  => false,
                'query_var'          => true,
                'rewrite'            => array( 'slug' => 'ed_download' ),
                'capability_type'    => 'post',
                'has_archive'        => false,
                'hierarchical'       => false,
                'menu_position'      => null,
                'supports'           => array( 'title', 'editor' )
            );
            
            $args = apply_filters( 'ed_post_type_args', $args );
            
            register_post_type( self::get_etd_post_type(), $args );
        }
        
        public function etd_meta_box() {
            add_meta_box( 'etd_meta', __( 'Upload your file', 'email-to-downlaod' ), array( &$this, 'etd_meta_box_cb' ), self::get_etd_post_type() );   
        }
        
        public function etd_meta_box_cb( $post ) {
            $etd_meta = ( array ) get_post_meta( $post->ID, 'etd_meta', true );
            
            $data = array(
                        'etd_uploader' => isset( $etd_meta['etd_uploader'] ) ? $etd_meta['etd_uploader'] : ''
                    );
            $form = new Builder_Form( $data, 'etd_meta' );
            
            $form->nonce( 'ed_meta_box_nonce' );
            ?>
            <table cellpadding="5" cellspacing="5">
                <tr>
                    <td>
                        <?php
                            $form->label(
                                        'etd_uploader',
                                        array(
                                            'text' => __( 'Upload your file', 'email-to-download' )
                                        )
                                    );
                        ?>
                    </td>
                    <td>
                        <?php $form->text( 'etd_uploader', array( 'atts' => array( 'size' => 20, 'id' => 'ed_item' ) ) ); ?>
                        <button type="button" class="button button-secondary" id="ed_uploader_btn"><?php _e( 'Upload File' ) ?></button>
                    </td>
                </tr>
            </table>
            <?php
        }
        
        public function etd_save_meta_box_data( $post_id ) {
            if ( ! Builder_Helper::check_nonce( 'ed_meta_box_nonce' ) ) {
                return;
            }
            
            // If this is an autosave, our form has not been submitted, so we don't want to do anything.
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
            }
            
            if ( ! isset( $_POST['etd_meta'] ) ) {echo 236; exit;
		return;
            }
            
            update_post_meta( $post_id, 'etd_meta', $_POST['etd_meta'] );
        }
        
        public function etd_ajax_dw_submit_cb() {
            check_ajax_referer( 'ed-ajax-form-submit', '_wpnonce' );
            
            $ed_attachment_url = sanitize_text_field( $_POST['ed_attachment_url'] );
            $ed_download_id = sanitize_text_field( $_POST['ed_download_id'] );
            $ed_email = sanitize_text_field( $_POST['ed_email'] );
            
            $sql = "SELECT * FROM {$this->_tbl_name} ORDER BY id DESC LIMIT 1";
            $q = $this->_db->get_results( $sql, ARRAY_A );
            if( count( $q ) < 1 ) $identifier = 1;
            else $identifier = $q[0]['id'];
            
            $link = site_url( '/' . $this->_slug . '/' ) . $this->mask_download_link( $ed_attachment_url, $identifier );
            $this->_db->insert(
		        $this->_tbl_name,
                array(
                    'file_url' => $link,
                    'primary_time' => current_time( 'mysql' ),
                                'email' => $ed_email,
                    'download_id' => $ed_download_id
                )
            );
            
            $post = get_post( $ed_download_id );
            
            wp_mail(
                $ed_email,
                str_replace( '{{download_name}}', $post->post_title, $this->_options->get_option( 'notify_email_subject' ) ),
                str_replace( array( '{{download_expire}}', '{{download_link}}' ), array( $this->_options->get_option( 'dw_expire' ), $link ), $this->_options->get_option( 'email_content' ) )
                );
            
            /**
             * Action when the email is sent to the user
             * including download link and other set message
             * @since 1.0.1
             *
             * @param   $ed_attachment_url      sting   The attachment URL
             * @param   $ed_download_id         int     The download page ID
             * @param   $ed_email               string  The email of the user
             * @param   $link                   string  The download link sent to the user
             */
            do_action(
                'ed_subscribed_to_download',
                $ed_attachment_url,
                $ed_download_id,
                $ed_email,
                $link
            );
            
            do_action(
                'etd_subscribed_to_download',
                $ed_attachment_url,
                $ed_download_id,
                $ed_email,
                $link
            );
            
            die();
        }
        
        public function shortcode_buttons( $buttons ) {
            ?>
            <div id="etd_sc_details_wrap" style="display: none">
                <div class="etd_sc_details">
                    <div class="sc_selection_wrapper">
                        <p>
                            <label><?php _e( 'Select a product', 'email-to-download' ) ?></label>
                            <?php
                                $posts = Download_Model::get_all();
                            ?>
                            <select id="etd_product">
                                <option value=""><?php _e( 'Select a product' ) ?></option>
                                <?php foreach( $posts as $post ) { ?>
                                <option value="<?php echo $post->ID ?>"><?php echo $post->post_title ?></option>
                                <?php } ?>
                            </select>
                        </p>
                        <p>
                            <label><?php _e( 'Title', 'email-to-download' ) ?></label>
                            <select id="etd_title">
                                <option value="yes"><?php _e( 'Yes', 'email-to-download' ) ?></option>
                                <option value="no"><?php _e( 'No', 'email-to-download' ) ?></option>
                            </select>
                        </p>
                        <p>
                            <label><?php _e( 'Show Content', 'email-to-download' ) ?></label>
                            <select id="etd_content">
                                <option value="yes"><?php _e( 'Yes', 'email-to-download' ) ?></option>
                                <option value="no"><?php _e( 'No', 'email-to-download' ) ?></option>
                            </select>
                        </p>
                        <p>
                            <label><?php _e( 'Style', 'email-to-download' ) ?></label>
                            <select id="etd_style">
                                <option value="normal"><?php _e( 'Normal', 'email-to-download' ) ?></option>
                                <option value="popup"><?php _e( 'Popup', 'email-to-download' ) ?></option>
                                <option value="slide"><?php _e( 'Slide', 'email-to-download' ) ?></option>
                            </select>
                        </p>
                        <p>
                            <label><?php _e( 'Tagline', 'email-to-download' ) ?></label>
                            <input type="text" id="etd_tag" value="<?php _e( 'Please provide an email address where we should send the download link.', 'email-to-download' ) ?>">
                        </p>
                        <p>
                            <label><?php _e( 'Email Placeholder', 'email-to-download' ) ?></label>
                            <input type="text" id="etd_email" value="<?php _e( 'Your email here', 'email-to-download' ) ?>">
                        </p>
                        <p>
                            <label><?php _e( 'Submit Button Text', 'email-to-download' ) ?></label>
                            <input type="text" id="etd_submit_btn_txt" value="<?php _e( 'Get download link', 'email-to-download' ) ?>">
                        </p>
                    </div>
                    <input type="button" id="etd_sc_btn" value="<?php _e( 'Insert ShortCode', 'email-to-download' ) ?>">
                </div>
            </div>
            <style>
                .sc_selection_wrapper label{width: 120px !important; display: inline-block !important}
                .sc_selection_wrapper input{width: 400px;}
            </style>
            <a data-src="etd_sc_details_wrap" href="#TB_inline?width=600&height=550&inlineId=etd_sc_details_wrap" class="button button-secondary thickbox" title="<?php _e( 'Email to Download Shortcode', 'email-to-download' ) ?>"><span style="position: relative; top: 3px;" class="dashicons dashicons-arrow-right-alt2"></span> <?php _e( 'Email to Download ShortCode', 'email-to-download' ) ?></a>
            <?php
        }
        
        public function ed_check_dw_link() {
            
            $dw = get_query_var( $this->_slug );
            if ( ! empty( $dw ) ) {
                $file_url = site_url( '/' . $this->_slug . '/' ) . $dw;
                
                $sql = "SELECT * FROM {$this->_tbl_name} WHERE file_url='{$file_url}'";
                $q = $this->_db->get_results( $sql, ARRAY_A );
                
                $primary_time = $q[0]['primary_time'];
                $post_id = $q[0]['download_id'];
                $current_time = current_time( 'mysql' );
                
                // Check if expiration time exists
                $diff = strtotime( $current_time ) - strtotime( $primary_time );
                
                if ( $diff < $this->_options->get_option( 'dw_expire' ) * 60 * 24 ){
                    $counter = get_post_meta( $post_id, 'ed_product_count', true );
                    if ( ! $counter ) $counter = 0;
                    $counter++;
                    update_post_meta( $post_id, 'ed_product_count', $counter );
                    
                    $url = explode( 'a0o0a', $dw );
                    $url = etd_base64_url_decode( $url[1] );
                    
                    header('Content-Type: application/octet-stream');
                    header("Content-Transfer-Encoding: Binary"); 
		            header('HTTP/1.0 200 OK', true, 200);
                    header("Content-disposition: attachment; filename=\"" . basename( $url ) . "\""); 
                    readfile( $url );
                } else {
                    wp_redirect( get_permalink( $this->_options->get_option( 'dw_page' ) ) );
                    exit;
                }
            }
        }
        
        public function mask_download_link( $link, $identifier = '' ) {
            $salt = $identifier . 'a0o0a';
            $random = $this->random_string();
            
            /**
             * Filter the output of masked URL
             * @since 1.0.1
             *
             * @param       $random . $salt . base64_encode( $link )    string  The masked download URL
             * @param       $random                                     string  A random number
             * @param       $salt                                       string  An unique salt
             * @param       $link                                       string  Download link before masking
             * @param       $identifier                                 string  An unique identifier
             */
            return apply_filters(
                                'mask_download_link',
                                $random . $salt . etd_base64_url_encode( $link ),
                                $random,
                                $salt,
                                $link,
                                $identifier
                                );
        }
        
        /**
         * Generate random string
         * @since 1.0.1
         *
         * @param   $length     int    Length of random number
         *
         * @access public
         */
        public function random_string( $length = 5 ) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen( $characters );
            $randomString = '';
            for ( $i = 0; $i < $length; $i++ ) {
                $randomString .= $characters[rand( 0, $charactersLength - 1 )];
            }
            return $randomString;
        }
    }

    ETD_Plugin::get_instance();
}

function etd_base64_url_encode($input) {
    return str_replace(array('+','/','='), array('-', '_', 'UUUU'), base64_encode($input));
}

function etd_base64_url_decode($input) {
    return base64_decode(str_replace(array('-', '_', 'UUUU'), array('+','/','='), $input));
}