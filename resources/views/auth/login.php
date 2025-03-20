<?php
use function DT\Home\config;

$this->layout( 'layouts/auth' );
/**
 * @var string $logo_path
 * @var string $form_action
 * @var string $error
 * @var string $username
 * @var string $password
 * @var string $register_url
 * @var string $reset_url
 */
?>

<div class="container login">
    <dt-tile class="login__background">
        <div class="section__inner">
            <div class="logo">
                <img
                    src="<?php echo esc_url( $logo_path ) ?>"
                    alt="Disciple.Tools"
                    class="logo__image">
            </div>
            <form action="<?php echo esc_attr( $form_action ) ?>"
                  method="POST">
                <?php wp_nonce_field( config( 'plugin.nonce_name' ) ) ?>

                <?php if ( !empty( $error ) ) : ?>
                    <dt-alert context="alert"
                              dismissable>
                        <?php echo esc_html( strip_tags( $error ) ) ?>
                    </dt-alert>
                <?php endif; ?>

                <dt-text name="username"
                         placeholder="<?php esc_attr_e( 'Username or Email Address', 'dt-home' ); ?>"
                         value="<?php echo esc_attr( $username ); ?>"
                         required
                         tabindex="1"
                ></dt-text>
                <dt-text name="password"
                         placeholder="<?php esc_attr_e( 'Password', 'dt-home' ); ?>"
                         value="<?php echo esc_attr( $password ); ?>"
                         type="password"
                         tabindex="2"
                         required></dt-text>

                <sp-button-group>
                    <sp-button tabindex="3" class="login-sp-button-radius"
                               type="submit">
                        <span><?php esc_html_e( 'Login', 'dt-home' ) ?></span>

                    </sp-button>

                    <sp-button href="<?php echo esc_url( $register_url ); ?>" class="cre-ac"
                               variant="secondary"
                               tabindex="4"
                               <?php echo !get_option( 'users_can_register' ) ? esc_attr( 'disabled' ) : ''; ?>
                               title="<?php echo !get_option( 'users_can_register' ) ? 
                                   esc_attr__( 'Registration is currently disabled', 'dt-home' ) : 
                                   esc_attr__( 'Create Account', 'disciple-tools-autolink' ); ?>">
                        <span><?php esc_html_e( 'Create Account', 'disciple-tools-autolink' ) ?></span>
                    </sp-button>
                </sp-button-group>
            </form>
        </div>
    </dt-tile>
</div>
<div class="login__footer">
    <sp-button href="<?php echo esc_url( $reset_url ); ?>"
               variant="secondary"
               treatment="link"
    >
        <?php esc_html_e( 'Forgot Password?', 'disciple-tools-autolink' ); ?>
    </sp-button>
</div>

