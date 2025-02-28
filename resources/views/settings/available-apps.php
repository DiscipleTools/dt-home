<?php
$this->layout( 'layouts/settings', compact( 'tab', 'link', 'page_title' ) )
?>

<form method="post">
    <?php wp_nonce_field( 'dt_admin_form', 'dt_admin_form_nonce' ) ?>

    <!-- Add a form -->
</form>

<div class="wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <span style="float:right;">
                    <a href="admin.php?page=dt_home&tab=app" class="button float-right">
                        <i class="fa fa-arrow-left"></i> <?php esc_html_e( 'Go Back', 'dt-home' ); ?>
                    </a>
                </span>

                <br><br>
                <div class="scrollable-table">
                    <table class="widefat striped" style="border-collapse: collapse; width: 100%;">
                        <thead>
                        <tr>
                            <th style="border: 1px solid #ddd;"><?php esc_html_e( 'Name', 'dt-home' ); ?></th>
                            <th style="border: 1px solid #ddd;"><?php esc_html_e( 'Type', 'dt-home' ); ?></th>
                            <th style="border: 1px solid #ddd;"><?php esc_html_e( 'Icon', 'dt-home' ); ?></th>
                            <th style="border: 1px solid #ddd;"><?php esc_html_e( 'Slug', 'dt-home' ); ?></th>
                            <th style="border: 1px solid #ddd;"><?php esc_html_e( 'Action', 'dt-home' ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ( empty( $data ) ) : ?>
                            <tr>
                                <td colspan="5"
                                    style="text-align: center;border: 1px solid #ddd;"><?php esc_html_e( 'No apps found', 'dt-home' ); ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ( $data

                                       as $app ) : ?>
                            <tr>
                                <td style="border: 1px solid #ddd;"><?php echo esc_html( $app['name'] ); ?></td>
                                <td style="border: 1px solid #ddd;"><?php echo esc_html( $app['type'] ); ?></td>
                                <td style="border: 1px solid #ddd; vertical-align: middle; text-align: center; align-content: center;">
                                    <?php if ( !empty( $app['icon'] ) ) : ?>
                                        <?php if ( filter_var( $app['icon'], FILTER_VALIDATE_URL ) || strpos( $app['icon'], '/wp-content/' ) === 0 ) : ?>
                                            <img src="<?php echo esc_url( $app['icon'] ); ?>"
                                                 alt="<?php esc_attr_e( 'Icon', 'dt-home' ); ?>"
                                                 style="width: 25px; height: 25px;">
                                        <?php elseif ( preg_match( '/^mdi\smdi-/', $app['icon'] ) ) : ?>
                                            <i class="<?php echo esc_attr( $app['icon'] ); ?>"
                                               style="font-size: 25px;"></i>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td style="border: 1px solid #ddd;"><?php echo esc_attr( $app['slug'] ); ?></td>

                                <td style="border: 1px solid #ddd;">
                                    <?php if ( isset( $app['creation_type'] ) && $app['creation_type'] == 'code' ) : ?>
                                        <div class="action-tooltip">
                                            <a href="#" onclick="restore_app('<?php echo esc_attr( $app['slug'] ); ?>')"
                                               class="delete-apps">
                                                <i class="fas fa-undo action-icon"></i>
                                            </a>
                                            <span
                                                class="action-tooltip-text"><?php esc_html_e( 'Restore', 'dt-home' ); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function restore_app(slug) {
        var confirmation = confirm(<?php echo json_encode( __( 'Are you sure you want to restore this app?', 'dt-home' ) ); ?>)
        if (confirmation) {
            // If the user confirms, redirect to the delete URL
            window.location.href = 'admin.php?page=dt_home&tab=app&action=restore_app/' + slug
        }
        // If the user cancels, do nothing
    }
</script>
<?php $this->start( 'right' ) ?>

<!-- Add some content to the right side -->

<?php $this->stop() ?>
