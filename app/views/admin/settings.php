<div class="wrap">
    <h2><?php _e( 'M2 Download Addon', 'email-to-download' ) ?></h2>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <?php $form->open( array( 'url' => admin_url( '?action=ed_settings_save&noheader=true' ) ) ); ?>
                    <?php $form->nonce( Settings_Controller::NONCE ); ?>
                    <div class="postbox">
                        <h3 class="hndle"><?php _e( 'General Settings', 'email-to-download' ) ?></h3>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <td>
                                        <?php
                                            $form->label(
                                                    'dw_expire',
                                                    array(
                                                        'text' => __( 'Download link will expire at:', 'email-to-download' )
                                                    )
                                                );
                                        ?>
                                    </td>
                                    <td><?php $form->text( 'dw_expire', array( 'atts' => array( 'size' => 5 ) ) ); ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php
                                            $form->label(
                                                    'dw_page',
                                                    array(
                                                        'text' => __( 'Select a page for expire message:', 'email-to-download' )
                                                    )
                                                );
                                        ?>
                                    </td>
                                    <td><?php $form->pages( 'dw_page' ); ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php
                                            $form->label(
                                                    'notify_email_from_name',
                                                    array(
                                                        'text' => __( 'Notification email sender name:', 'email-to-download' )
                                                    )
                                                );
                                        ?>
                                    </td>
                                    <td><?php $form->text( 'notify_email_from_name', array( 'atts' => array( 'size' => 50 ) ) ); ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php
                                            $form->label(
                                                    'notify_email_from_email',
                                                    array(
                                                        'text' => __( 'Notification email sender email:', 'email-to-download' )
                                                    )
                                                );
                                        ?>
                                    </td>
                                    <td><?php $form->text( 'notify_email_from_email', array( 'atts' => array( 'size' => 50 ) ) ); ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php
                                            $form->label(
                                                    'notify_email_subject',
                                                    array(
                                                        'text' => __( 'Notification email Subject:', 'email-to-download' )
                                                    )
                                                );
                                        ?>
                                    </td>
                                    <td><?php $form->text( 'notify_email_subject', array( 'atts' => array( 'size' => 50 ) ) ); ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php
                                            $form->label(
                                                    'email_content',
                                                    array(
                                                        'text' => __( 'Notification email content:', 'email-to-download' )
                                                    )
                                                );
                                        ?>
                                    </td>
                                    <td><?php $form->textarea( 'email_content', array( 'atts' => array( 'cols' => 70, 'rows' => 10 ) ) ); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <?php $form->submit( 'etd_settings_submit', array( 'atts' => array( 'class' => 'button button-primary' ), 'value' => __( 'Save Settings', 'email-to-download' ) ) ); ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                <?php $form->close(); ?>
            </div>
            
            <!-- This is sidebar -->
            
            <?php //echo ed_get_sidebar(); ?>
            
            <!-- End of sidebar -->
            
        </div>
    </div>

</div>