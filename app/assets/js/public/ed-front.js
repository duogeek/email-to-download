/**
 * JS File for front end handler
 */

;jQuery(function($) {
    
    var popupRun = false;
    
    var ED_Plugin = {
        
        init: function() {
            this.processAjaxForm();
            this.runPopUp();
            this.runSlide();
        },
        
        processAjaxForm: function() {
            
            $(document).on( 'click', '.etd_submit', function(e) {
                e.preventDefault();
                
                var _this = $(this).closest('.etd_dw_form');
                
                _this.find('.ed_error').html('&nbsp;');
                if( ! validateEmail( _this.find('.ed_email').val() ) ){
                    _this.find('.ed_error').html( obj.emailError );
                    return false;
                }
                
                var loading = '<div class="spin_wrap"><div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div></div>';
                _this.prepend(loading);
                
                var success = '<div class="ed_success">' + obj.successMSG + '</div>';
                
                var data = {
                        ed_attachment_url: _this.find('.ed_attachment_url').val(),
                        ed_download_id: _this.find('.ed_download_id').val(),
                        ed_email: _this.find('.ed_email').val(),
                        action: 'etd_ajax_dw_submit',
                        _wpnonce: obj.ajaxNonce
                    };
                    
                $.post( obj.adminAjax, data, function( response ) {
                    _this.find('.spin_wrap').remove();
                    _this.html(success);
                    if( popupRun ) $.colorbox.resize();
                });
                
                return false;
            });
            
        },
        
        runPopUp: function() {
            
            $( '.ed_pop a' ).click(function(e) {
                e.preventDefault();
                
                var html = $(this).closest('.ed_pop').next('.ed_pop_wrap').html();
                
                $.colorbox({
                    width: 800,
                    height: 600,
                    html: html
                });
                
                $.colorbox.resize();
                
                popupRun = true;
                
                return false;
            })
        },
        
        runSlide: function() {
            $('.ed_slide a').click(function(e){
                e.preventDefault();
                
                var _slider = $(this).parent().next('.ed_slide_wrap');
                _slider.slideToggle();
                
                return false;
            });
        }
        
    };
    
    ED_Plugin.init();
    
});

function validateEmail(email) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email);
}