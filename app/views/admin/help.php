<div class="wrap">
    <h2><?php _e( 'M2 Download Addon', 'email-to-download' ) ?></h2>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="postbox">
                    <h3 class="hndle"><?php _e( 'ShortCodes', 'email-to-download' ) ?></h3>
                    <div class="inside">
                        <h4>[ed_download_file]</h4>
                        <p>
                            <?php _e( 'Options:' ); ?>
                            <ul>
                                <li>
                                    <b>id:</b> <?php _e( 'number: The post ID of the download product (REQUIRED)' ) ?>
                                </li>
                                <li>
                                    <b>title:</b> <?php _e( 'yes: to show download post title (default)' ) ?>, <?php _e( 'no: to hide download post title' ) ?>
                                </li>
                                <li>
                                    <b>show_content:</b> <?php _e( 'yes: to show download post content (default)' ) ?>, <?php _e( 'no: to hide download post content' ) ?>
                                </li>
                                <li>
                                    <b>style:</b> <?php _e( 'normal: Normal Style' ) ?>, <?php _e( 'popup: popup style' ) ?>, <?php _e( 'slide: slide style' ) ?>
                                </li>
                                <li>
                                    <b>tagline:</b> <?php _e( 'anything: To set a tagline. Default: Please provide an email address where we should send the download link.' ) ?>
                                </li>
                                <li>
                                    <b>email_placeholder:</b> <?php _e( 'anything: To set an email field placeholder. Default: Your email here.' ) ?>
                                </li>
                                <li>
                                    <b>submit:</b> <?php _e( 'anything: To set a value of submit button. Default: Get download link.' ) ?>
                                </li>
                            </ul>
                            <h4>Example: [ed_download_file id="75" title="no" show_content="no" style="normal"]</h4>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- This is sidebar -->
            
            <?php //echo ed_get_sidebar(); ?>
            
            <!-- End of sidebar -->
            
        </div>
    </div>

</div>