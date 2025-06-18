<?php
/**
 * @var string $logo_path
 * @var string $form_action
 * @var string $error
 * @var string $username
 * @var string $email
 * @var string $password
 * @var string $login_url
 */
$this->layout( 'layouts/auth' );
?>
<div class="container register">
    <dt-tile class="register__background">
        <div class="section__inner">
            <div class="logo">
                <img
                    src="<?php echo esc_url( $logo_path ) ?>"
                    alt="Disciple.Tools"
                    class="logo__image">
            </div>

            <?php if ( ! get_option( 'users_can_register' ) ) : ?>
                <dt-alert context="alert">
                    <?php esc_html_e( 'Registration is currently disabled. Please contact the site administrator for more information.', 'dt-home' ); ?>
                </dt-alert>
            <?php endif; ?>
            <form action="<?php echo esc_attr( $form_action ) ?>" method="POST">
                <?php wp_nonce_field( 'dt_home' ) ?>

                <?php if ( !empty( $error ) ) : ?>
                    <dt-alert context="alert"
                              dismissable>
                        <?php echo esc_html( $error ) ?>
                    </dt-alert>
                <?php endif; ?>

                <dt-text name="username"
                         placeholder="<?php esc_attr_e( 'Username', 'disciple-tools-autolink' ); ?>"
                         value="<?php echo esc_attr( $username ); ?>"
                         required></dt-text>
                <dt-text name="email"
                         placeholder="<?php esc_attr_e( 'Email', 'disciple-tools-autolink' ); ?>"
                         value="<?php echo esc_attr( $email ); ?>"
                         required></dt-text>
                <dt-text name="password"
                         placeholder="<?php esc_attr_e( 'Password', 'disciple-tools-autolink' ); ?>"
                         value="<?php echo esc_attr( $password ); ?>"
                         type="password"
                         required></dt-text>
                <dt-text name="confirm_password"
                         placeholder="<?php esc_attr_e( 'Confirm Password', 'disciple-tools-autolink' ); ?>"
                         value=""
                         type="password"
                         required></dt-text>

                <sp-button class="register__button login-sp-button-radius cre-ac"
                           tabindex="3"
                           treatment="fill"
                           type="submit">
                    <span><?php esc_html_e( 'Register', 'dt-home' ) ?></span>
                </sp-button>
            </form>
        </div>
    </dt-tile>
    <div class="login__footer">
        <sp-button href="<?php echo esc_url( $login_url ); ?>"
                   variant="secondary"
                   treatment="link"
        >
            <span><?php esc_html_e( 'Back to Login', 'dt-home' ); ?></span>
        </sp-button>
    </div>
</div>
