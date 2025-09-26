<?php
$this->layout( 'layouts/settings', compact( 'tab', 'link', 'page_title' ) )
?>

<form action="admin.php?page=dt_home&tab=training&action=create" method="post" enctype="multipart/form-data">
    <?php wp_nonce_field( 'dt_admin_form_nonce' ) ?>
    <table class="widefat striped" id="ml_email_main_col_config">
        <thead>
        <tr>
            <th><?php esc_html_e( 'Training Videos', 'dt-home' ) ?></th>
            <th></th>
            <th></th>
        </tr>
        </thead>
      <tbody>
    <tr>
        <td style="vertical-align: middle;">
            <?php esc_html_e( 'Name', 'dt-home' ) ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e( 'Enter the name of the training video.', 'dt-home' ) ?></span>
            </span>
        </td>
        <td colspan="2">
            <input style="min-width: 100%;" class="form-control" type="text" name="name" id="name" pattern=".*\S+.*" title="<?php esc_attr_e( 'The name cannot be empty or just whitespace.', 'dt-home' ); ?>" required/>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: middle;">
            <?php esc_html_e( 'Embed Video', 'dt-home' ) ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e( 'Paste the embed code for the video.', 'dt-home' ) ?></span>
            </span>
        </td>
        <td colspan="2">
            <textarea style="min-width: 100%;" class="form-control" name="embed_video" id="embed_video"
                      oninput="this.setCustomValidity(this.value.trim() === '' ? '<?php esc_attr_e( 'The embed Video cannot be empty or just whitespace.', 'dt-home' ); ?>' : '')" required></textarea>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: middle;">
            <?php esc_html_e( 'Anchor', 'dt-home' ) ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e( 'Specify the anchor text for the video.', 'dt-home' ) ?></span>
            </span>
        </td>
        <td colspan="2">
            <input style="min-width: 100%;" class="form-control" type="text" name="anchor" id="anchor"
                   pattern=".*\S+.*" title="<?php esc_attr_e( 'The anchor text cannot be empty or just whitespace.', 'dt-home' ); ?>" required/>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: middle;">
            <?php esc_html_e( 'Sort', 'dt-home' ) ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e( 'Set the sort order for the video.', 'dt-home' ) ?></span>
            </span>
        </td>
        <td colspan="2">
            <input style="min-width: 100%;" type="number" name="sort" id="sort" required/>
        </td>
    </tr>
</tbody>
        <tfoot>
        <tr>
            <td colspan="3">
                <span style="float:right;">
                    <a href="admin.php?page=dt_home&tab=training"
                       class="button float-right"><?php esc_html_e( 'Cancel', 'dt-home' ) ?></a>
                    <button type="submit" id="ml_email_main_col_update_but"
                            class="button float-right"><?php esc_html_e( 'Submit', 'dt-home' ) ?></button>
                </span>
            </td>
        </tr>
        </tfoot>
    </table>

    <br>
    <span id="ml_email_main_col_update_msg" style="font-weight: bold; color: red;"></span>
</form>

<?php $this->start( 'right' ) ?>

<!-- Add some content to the right side -->

<?php $this->stop() ?>
