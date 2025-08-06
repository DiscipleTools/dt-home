<?php
/**
 * @var string $tab
 * @var string $link
 * @var string $page_title
 * @var array $data
 */
$this->layout( 'layouts/settings', compact( 'tab', 'link', 'page_title' ) )
?>

<form method="post">
    <?php wp_nonce_field( 'dt_admin_form_nonce' ) ?>

    <!-- Add a form -->
</form>

<div class="wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <span>
                    <a href="admin.php?page=dt_home&tab=training&action=create" class="button" style="float:right;">
                        <i class="fa fa-plus"></i> <?php esc_html_e( 'Add Training', 'dt-home' ); ?>
                    </a>
                </span>

                <br><br><br>
                <div class="scrollable-table">
                    <table class="widefat striped" style="border-collapse: collapse; width: 100%; ">
                        <thead>
                        <tr>
                            <th style="border: 1px solid #ddd;"><?php esc_html_e( 'Name', 'dt-home' ); ?></th>
                            <th style="border: 1px solid #ddd;"><?php esc_html_e( 'Embed Video', 'dt-home' ); ?></th>
                            <th style="border: 1px solid #ddd;"><?php esc_html_e( 'Anchor', 'dt-home' ); ?></th>
                            <th class="training_action"
                                style="border: 1px solid #ddd; width: 100%;"><?php esc_html_e( 'Action', 'dt-home' ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ( $data as $training ) : ?>
                            <tr>
                                <td style="border: 1px solid #ddd;"><?php echo esc_html( $training['name'] ); ?></td>
                                <td style="border: 1px solid #ddd;"><?php echo stripslashes($training['embed_video']); //phpcs:ignore ?></td>
                                <td style="border: 1px solid #ddd;"><?php echo esc_html( $training['anchor'] ); ?></td>
                                <td style="border: 1px solid #ddd; white-space: nowrap;">
                                    <div class="action-tooltip">
                                        <a href="admin.php?page=dt_home&tab=training&action=edit/<?php echo esc_attr( $training['id'] ); ?>">
                                            <i class="fas fa-edit action-icon"></i>
                                        </a>
                                        <span class="action-tooltip-text"><?php esc_html_e( 'Edit', 'dt-home' ); ?></span>
                                    </div>
                                    &nbsp;|&nbsp;
                                    <div class="action-tooltip">
                                        <a href="#" onclick="confirmDelete(<?php echo esc_attr( $training['id'] ); ?>)"
                                           class="delete-apps">
                                            <i class="fas fa-trash action-icon"></i>
                                        </a>
                                        <span
                                            class="action-tooltip-text"><?php esc_html_e( 'Delete', 'dt-home' ); ?></span>
                                    </div>
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
    function confirmDelete(trainingId) {
        var confirmation = confirm(<?php echo json_encode( __( 'Are you sure you want to delete this entry?', 'dt-home' ) ); ?>)
        if (confirmation) {
            // If the user confirms, redirect to the delete URL
            window.location.href = 'admin.php?page=dt_home&tab=training&action=delete/' + trainingId
        }
        // If the user cancels, do nothing
    }
</script>
<?php $this->start( 'right' ) ?>

<!-- Add some content to the right side -->

<?php $this->stop() ?>
