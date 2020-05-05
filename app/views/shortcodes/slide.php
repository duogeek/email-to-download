<div class="ed_download_btn ed_slide">
    <a href="#"><?php _e( 'Download now', 'email-to-download' ) ?></a>
</div>
<div class="ed_slide_wrap">
    <div class="etd_dw_form">
        <?php if( $title != 'no' ) { ?>
        <h4><?php echo $post->post_title ?></h4>
        <?php } ?>
        <?php if( $content != 'no' ) { ?>
        <div class="etd_dw_con">
            <?php echo $post->post_content; ?>
        </div>
        <?php } ?>
        <form action="#" method="post">
            <!--input type="hidden" id="ed_attachment_id" value="<?php echo $ed_file_id ?>"-->
            <input type="hidden" class="ed_attachment_url" value="<?php echo $ed_file ?>">
            <input type="hidden" class="ed_download_id" value="<?php echo $id ?>">
            <p><?php echo $tagline; ?></p>
            <table cellpadding="5" cellspacing="5">
                <tr>
                    <!--td><?php _e( 'Email' ) ?></td-->
                    <td>
                        <input type="text" class="ed_email" placeholder="<?php echo $submit ?>">
                        <span class="ed_error">&nbsp;</span>
                    </td>
                </tr>
                <tr>
                    <!--td>&nbsp;</td-->
                    <td><input type="button" class="etd_submit" value="<?php _e( 'Get Download Link', 'email-to-download' ) ?>"></td>
                </tr>
            </table>
        </form>
    </div>
</div>