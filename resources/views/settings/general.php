<?php
/**
 * General settings page
 *
 * @var string $tab
 * @var string $link
 * @var string $page_title
 * @var string $dt_home_require_login
 * @var string $dt_home_reset_apps
 */
$this->layout( 'layouts/settings', compact( 'tab', 'link', 'page_title' ) )
?>

<form method="post" action="admin.php?page=dt_home&tab=general">
	<?php wp_nonce_field( 'dt_admin_form_nonce' ); ?>
	<div class="error-message" style="display:none;"></div>
	<table>
		<tr>
			<td>
				<label for="require_user">
					<input type="checkbox" id="dt_home_require_login"
							name="dt_home_require_login" <?php checked( $dt_home_require_login ); ?>
					>
					<?php esc_html_e( 'Require users to login to access the home screen magic link?', 'dt-home' ); ?>
				</label>
			</td>

		</tr>
		<tr>
			<td>
				<label for="reset_app">
					<input type="checkbox" id="dt_home_reset_apps"
							name="dt_home_reset_apps" <?php checked( $dt_home_reset_apps ); ?>
					>
					<?php esc_html_e( 'Allow users to reset their apps?', 'dt-home' ); ?>
				</label>
			</td>
		</tr>
		<!--For giving some space between the field and the button.-->
		<tr>
			<td></td>
		</tr>
		<tr>
			<td></td>
		</tr>
		<tr>
			<td></td>
		</tr>
		<tr>
			<td></td>
		</tr>
		<tr>
			<td>
				<button type="submit" id="ml_email_main_col_update_but" class="button float-right">
					<?php esc_html_e( 'Update', 'dt-home' ); ?>
				</button>
			</td>
		</tr>
	</table>
</form>

<div class="registration-status" style="margin-top: 20px; padding: 10px; background: #f8f9fa; border-left: 4px solid #007cba;">
    <p>
        <?php
        if ( is_multisite() ) {
            $settings_link = network_admin_url( 'settings.php' );
            $settings_text = __( 'Network Settings', 'dt-home' );
        } else {
			$settings_link = admin_url( 'options-general.php' );
            $settings_text = __( 'General Settings', 'dt-home' );
        }
		$registration_status = get_option( 'users_can_register' ) ? 'enabled' : 'disabled';
        ?>
        <strong><?php esc_html_e( 'Outside users can register and existing users can invite new users:', 'dt-home' ); ?></strong> 
        <?php echo esc_html( $registration_status === 'enabled' ? __( 'Yes', 'dt-home' ) : __( 'No', 'dt-home' ) ); ?>
        <br>
        <?php esc_html_e( 'To change registration settings, visit:', 'dt-home' ); ?> 
        <a href="<?php echo esc_url( $settings_link ); ?>"><?php echo esc_html( $settings_text ); ?></a>
    </p>
</div>

<?php $this->start( 'right' ); ?>

<!-- Add some content to the right side -->

<?php $this->stop(); ?>
