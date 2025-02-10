<?php

/**
 * @var string $tab
 * @var string $link
 * @var string $page_title
 * @var array $data
 */
$this->layout( 'layouts/settings', compact( 'tab', 'link', 'page_title' ) );

?>

<form id="reportFilterForm" method="get" action="admin.php?page=dt_home&tab=reports">
    <table class="widefat striped">
        <thead>
        <tr>
            <th><?php esc_html_e( 'Activity Logs', 'dt-home' ); ?></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr class="custom-style">
            <td>
                <label for="date_start">
                    <?php esc_html_e( 'Start Date', 'dt-home' ); ?>
                </label>
                <input type="date" id="date_start" name="date_start" required
                       value="<?php echo esc_attr( $_GET['date_start'] ?? '' ); ?>">
            </td>
            <td>
                <label for="date_end">
                    <?php esc_html_e( 'End Date', 'dt-home' ); ?>
                </label>
                <input type="date" id="date_end" name="date_end" required
                       value="<?php echo esc_attr( $_GET['date_end'] ?? '' ); ?>">
            </td>
            <td colspan="2">
                <div id="loader" class="loader" style="display: none;"></div>
                <button type="submit" id="update_filter" class="button float-right">
                    <?php esc_html_e( 'Filter', 'dt-home' ); ?>
                </button>
                &nbsp;&nbsp;
                <button type="reset" id="reset_button" onclick="window.location.reload()" class="button float-right">
                    <?php esc_html_e( 'Reset', 'dt-home' ); ?>
                </button>
            </td>
            <br />
        </tr>
        <tr>
            <td colspan="4">
                <dt-combined-chart></dt-combined-chart>
            </td>
        </tr>
        </tbody>
    </table>
</form>
