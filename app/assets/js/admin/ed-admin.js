/**
 * ED Admin JS
 *
 * @package Email to Download
 */

;jQuery( function( $ ){
    
    var ED_Plugin = {
            
        init: function() {
            this.fireUploader();
            this.deleteConfirm();
        },
        
        fireUploader: function() {
            $(document).on( 'click', '#ed_uploader_btn', function(e) {
                e.preventDefault();
                
                var file_frame, image_data;
                
                if ( undefined !== file_frame ) {
                    file_frame.open();
                    return;
                }
                
                file_frame = wp.media.frames.file_frame = wp.media({
                    frame:    'post',
                    state:    'insert',
                    multiple: false
                });
                
                file_frame.on( 'insert', function() {

                    json = file_frame.state().get( 'selection' ).first().toJSON();
                    
                    if ( 0 > $.trim( json.url.length ) ) {
                        return;
                    }
                    
                    //console.log( json.url );
                    
                    $('#ed_item').val(json.url);
                    //$('#ed_id').val(json.id);
             
                });
             
                // Now display the actual file_frame
                file_frame.open();
                
                //tb_show( '', 'media-upload.php?type=image&amp;TB_iframe=true' );
                
                return false;
            });
        },
        
        deleteConfirm: function() {
            $( '.delete_subscriber' ).click(function(e) {
                e.preventDefault();
                
                var c = confirm( 'Are you sure want to delete?' );
                if( c ) {
                    window.location.href = $( this ).attr( 'href' );
                }
                
                return false;
            });
        }
    };
        
    ED_Plugin.init();
    
    
    var SC_Builder = {
        
        init: function() {
            $( document ).on( 'click', '#etd_sc_btn', this.insert_into_editor )
        },
        
        insert_into_editor: function( e ) {
            e.preventDefault();
            
            var id = $( '#etd_product' ).val();
            
            if( id == '' )
            {
                alert( 'Please select a download product.' );
                return;
            }
            
            var html = '[ed_download_file ';
            html += 'id="' + id + '" ';
            html += 'title="' + $( '#etd_title' ).val() + '" ';
            html += 'show_content="' + $( '#etd_content' ).val() + '" ';
            html += 'style="' + $( '#etd_style' ).val() + '" ';
            html += 'tagline="' + $( '#etd_tag' ).val() + '" ';
            html += 'email_placeholder="' + $( '#etd_email' ).val() + '" ';
            html += 'submit="' + $( '#etd_submit_btn_txt' ).val() + '"';
            html += ']';
            
            if( ! tinyMCE.activeEditor || tinyMCE.activeEditor.isHidden() ) {
                $( 'textarea#content' ).val( html );
            } else {
                tinyMCE.execCommand( 'mceInsertRawHTML', false, html );
            }

            tb_remove();
        }
        
    };
    
    SC_Builder.init();
    
});