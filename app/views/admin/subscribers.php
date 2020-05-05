<div class="wrap">
    <h2><?php _e( 'Subscribers', 'email-to-download' ) ?></h2>
    <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <table class="wp-list-table widefat">
                        <thead>
                            <tr>
                                <th><?php _e( 'Email', 'email-to-download' ) ?></th>
                                <th><?php _e( 'Date', 'email-to-download' ) ?></th>
                                <th><?php _e( 'Delete', 'email-to-download' ) ?></th>
                                <th><?php _e( 'Items', 'email-to-download' ) ?></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><?php _e( 'Email', 'email-to-download' ) ?></th>
                                <th><?php _e( 'Date', 'email-to-download' ) ?></th>
                                <th><?php _e( 'Delete', 'email-to-download' ) ?></th>
                                <th><?php _e( 'Items', 'email-to-download' ) ?></th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php $i = 0; foreach( $emails as $email ) { $i++; ?>
                            <tr class="<?php echo $i % 2 == 0 ? 'alternate' : ''; ?>">
                                <td><?php echo $email->email; ?></td>
                                <td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $email->primary_time ) ); ?></td>
                                <td><a class="delete_subscriber" href="<?php echo esc_url( add_query_arg( 'delete_subscriber', $email->id, admin_url( 'edit.php?post_type=ed_download&page=email-to-download-Subscribers' ) ) ); ?>"><?php _e( 'Delete', 'email-to-download' ) ?></a></td>
                                <td valign="top">
                                    <?php echo implode( ', ', $email->items ) ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php //echo ed_get_sidebar(); ?>
            </div>
        </div>
</div>