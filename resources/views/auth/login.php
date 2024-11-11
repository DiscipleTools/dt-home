<?php

use function DT\Home\config;
use function DT\Home\route_url;

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
                <?php if ( !empty( $custom_logo ) ) : ?>
                    <img
                        src="
                <?php echo esc_url( $custom_logo ) ?>"
                        alt="Disciple.Tools"
                        class="logo__image">
                <?php else : ?>
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="Layer_1"
                         data-name="Layer 1" viewBox="0 0 1079.65 346.35" style="width: 100%; max-width: 200px;">
                        <defs>
                            <style>
                                .cls-1 {
                                    fill: #404040;
                                }
                            </style>
                        </defs>
                        <g>
                            <path class="cls-1"
                                  d="M681.61,4c4.83,1.04,9.73,1.82,14.47,3.17,19.48,5.52,34.03,17.02,42.54,35.66,.4,.88,.65,1.83,1.34,3.8-6.56,0-12.65-.4-18.66,.11-5.91,.51-9.57-1.18-12.88-6.5-5.68-9.13-14.99-13.46-25.39-15.16-29.3-4.78-53.83,14.33-56.41,44.04-1.18,13.53,.49,26.6,8.02,38.4,13.52,21.16,45.19,27.99,65.23,13.65,4.21-3.01,7.64-7.37,10.72-11.62,1.8-2.49,3.4-3.5,6.39-3.42,7.49,.2,14.98,.06,23.11,.06-3.28,9.52-8.27,17.55-15.5,23.75-23.98,20.58-51.42,24.12-79.81,12.14-28.06-11.84-41.67-34.83-41.95-65.17-.34-36.54,22.44-64.22,58.01-71.38,2.58-.52,5.17-1.02,7.75-1.53,4.33,0,8.67,0,13,0Z" />
                            <path class="cls-1"
                                  d="M547.58,4c3.2,.71,6.41,1.4,9.6,2.15,18.58,4.36,30.95,18.27,31.71,36.04h-24.64c-5.36-15.86-21.46-22.97-36.9-16.13-6.39,2.83-10.22,7.67-11.03,14.86-.83,7.44,1.66,13.57,8.16,16.84,6.92,3.48,14.46,5.76,21.81,8.34,6.43,2.25,13.06,3.94,19.45,6.3,13,4.8,22.6,13.04,24.6,27.53,2.29,16.57-3.03,30.25-17.05,39.96-20.33,14.09-54.83,10.99-71.23-6.28-6.57-6.92-9.92-15.84-9.09-25.14,7.34,0,14.78-.08,22.21,.12,.78,.02,1.87,1.64,2.2,2.71,5.67,18.43,30.7,24.75,44.61,11.21,8.18-7.97,7.92-21.41-1.35-27.99-4.89-3.47-10.91-5.55-16.66-7.56-9.41-3.28-19.2-5.53-28.51-9.05-13.63-5.15-21.77-15.06-22.66-29.98-.93-15.57,4.48-28.36,18.4-36.13,5.82-3.25,12.73-4.54,19.16-6.68,1.38-.46,2.8-.76,4.21-1.13,4.33,0,8.67,0,13,0Z" />
                            <path class="cls-1"
                                  d="M314.58,146.87V7.04c.44-.32,.71-.7,.97-.69,21.12,.49,42.53-1.05,63.29,1.95,40.3,5.82,59.17,38.67,56.15,76.29-3.27,40.69-33.62,61.34-68.98,62.33-15.49,.43-30.99,.21-46.49,.27-1.47,0-2.93-.18-4.94-.31Zm23.01-18.91c11.31,0,22.2,.94,32.87-.2,19.66-2.11,33.38-12.65,38.86-32.17,3.72-13.26,3.47-26.72-.86-39.84-4.5-13.61-13.54-23.1-27.27-27.76-14.21-4.82-28.86-2.72-43.61-3.15V127.96Z" />
                            <path class="cls-1"
                                  d="M805.03,146.79V6.88c.44-.26,.71-.57,.99-.56,19.96,.25,40.03-.72,59.85,1.08,28.88,2.61,45.02,29.94,34.62,56.48-5.95,15.2-18.16,22.7-33.56,24.83-9.5,1.31-19.25,.85-28.88,1.14-3.14,.09-6.29,.01-10.1,.01v56.94h-22.92Zm23.03-75.85c10.81,0,21.15,.66,31.37-.17,12.24-.99,19.44-8.55,20.32-19.52,1.05-13.09-3.56-22.5-14.5-24.45-12.07-2.15-24.56-1.88-37.18-2.68v46.82Z" />
                            <path class="cls-1"
                                  d="M1000.09,146.71V6.4h77.19V24.67h-54.24v41.19h48.06v18.86c-1.52,.07-3.26,.23-5.01,.23-13,.02-26,.03-39,0-2.49,0-4.57-.09-4.52,3.51,.15,12.5,.07,25,.1,37.5,0,.63,.26,1.25,.51,2.42h54.12v18.34h-77.22Z" />
                            <path class="cls-1" d="M940.35,128.75h47.77v18.05h-70.04V6.57h22.28v122.18Z" />
                            <path class="cls-1" d="M450.79,6.43h22.3V146.88h-22.3V6.43Z" />
                            <path class="cls-1" d="M759.85,6.43h22.3V146.88h-22.3V6.43Z" />
                        </g>
                        <g>
                            <path class="cls-1"
                                  d="M488.55,249.1c.54-20,11.06-45.45,38.22-61.36,43.51-25.5,110.97-6.4,118.83,60.12,2.5,21.15-.5,41.32-12.05,59.44-17.67,27.72-43.93,38.17-75.79,35.1-41.46-3.99-69.93-38.41-69.21-93.3Zm147.27,7.81c-.45-3.55-.93-10.03-2.14-16.38-5.6-29.44-27.16-49.53-57.74-54.11-27.75-4.15-55.38,10-68.55,35.51-9.13,17.7-10.64,36.64-6.86,55.9,11.23,57.1,72.15,69.2,107.23,43.2,20.4-15.12,27.6-36.68,28.05-64.12Z" />
                            <path class="cls-1"
                                  d="M731.93,177.09c43.35-.02,76.13,31.2,78.74,74.73,1.12,18.67-1.46,36.46-10.82,52.95-15.99,28.16-45.79,41.45-76.91,37.66-40.14-4.9-66.06-32.95-69.46-73.45-1.48-17.63,.85-34.57,8.95-50.4,13.52-26.42,38.94-41.46,69.5-41.47Zm-69.24,82.7c1.32,8.18,1.92,16.55,4.07,24.5,7.72,28.45,29.32,46.49,59.73,49.31,25.26,2.35,50.9-8.43,64.76-34.48,9.63-18.1,11.19-37.53,7.34-57.28-4.82-24.77-18.37-43.27-42.66-51.72-24.66-8.58-48.15-5.25-68.39,12-17.53,14.94-23.78,35.1-24.86,57.66Z" />
                            <path class="cls-1"
                                  d="M1005.15,213.64c-2.81,0-5.62,.26-8.34-.13-1.09-.16-2.5-1.56-2.89-2.7-9.8-28.73-50.47-31.56-68.85-14.71-12.98,11.9-15.36,40.48,9.58,51.33,9.55,4.15,19.87,6.51,29.83,9.73,5.38,1.73,10.81,3.35,16.09,5.35,14.73,5.59,24.22,15.64,25.78,31.85,1.58,16.43-4.17,29.8-17.8,39.33-23.19,16.22-62.19,10.53-77.75-11.23-4.42-6.18-7.19-12.99-7.41-20.98h9.89c.23,.41,.67,.92,.84,1.51,5.75,20.26,17.92,30.01,38.88,30.89,12.15,.51,23.5-1.75,33.01-10.13,14.39-12.69,15.79-41.32-7.61-51.53-9.85-4.3-20.46-6.84-30.72-10.2-5.69-1.86-11.45-3.52-17.02-5.7-13.89-5.45-23.06-15-24.83-30.36-2.01-17.4,3.55-31.53,18.72-41.09,20.1-12.67,51.61-10.28,68.22,5.03,7.08,6.52,11.72,15.24,12.37,23.75Z" />
                            <path class="cls-1"
                                  d="M385.72,187.19v-8.48h102.37v8.09h-45.8v153.94h-10.45V187.19h-46.12Z" />
                            <path class="cls-1"
                                  d="M834.36,332.47h60.14v8.21h-70.48v-6.06c0-49.15,0-98.31,0-147.46q0-9.53,10.34-8.26v153.58Z" />
                        </g>
                        <image width="347" height="340" transform="translate(2.65 3)"
                               xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAVsAAAFUCAYAAACKmZ84AAAACXBIWXMAAAsTAAALEwEAmpwYAAAF8WlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNi4wLWMwMDIgNzkuMTY0NDYwLCAyMDIwLzA1LzEyLTE2OjA0OjE3ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgMjEuMiAoV2luZG93cykiIHhtcDpDcmVhdGVEYXRlPSIyMDI0LTExLTExVDExOjE5OjE5KzA1OjMwIiB4bXA6TW9kaWZ5RGF0ZT0iMjAyNC0xMS0xMVQxMToyMjoxNyswNTozMCIgeG1wOk1ldGFkYXRhRGF0ZT0iMjAyNC0xMS0xMVQxMToyMjoxNyswNTozMCIgZGM6Zm9ybWF0PSJpbWFnZS9wbmciIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo3NTdkZWEzNC1lNzNhLTcyNGMtOTI1NC1mZGJmMzNiZWQzMjciIHhtcE1NOkRvY3VtZW50SUQ9ImFkb2JlOmRvY2lkOnBob3Rvc2hvcDpjODQ5ZmNhZS1mNTQxLWE4NGUtOTM5YS0yNDY3ZDNiMGQ2MGUiIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpkZGJiYjk5NS05ZTJmLTE0NGUtYjUyNC1lMzRhZWY3Y2Q5MmQiPiA8eG1wTU06SGlzdG9yeT4gPHJkZjpTZXE+IDxyZGY6bGkgc3RFdnQ6YWN0aW9uPSJjcmVhdGVkIiBzdEV2dDppbnN0YW5jZUlEPSJ4bXAuaWlkOmRkYmJiOTk1LTllMmYtMTQ0ZS1iNTI0LWUzNGFlZjdjZDkyZCIgc3RFdnQ6d2hlbj0iMjAyNC0xMS0xMVQxMToxOToxOSswNTozMCIgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWRvYmUgUGhvdG9zaG9wIDIxLjIgKFdpbmRvd3MpIi8+IDxyZGY6bGkgc3RFdnQ6YWN0aW9uPSJzYXZlZCIgc3RFdnQ6aW5zdGFuY2VJRD0ieG1wLmlpZDo3NTdkZWEzNC1lNzNhLTcyNGMtOTI1NC1mZGJmMzNiZWQzMjciIHN0RXZ0OndoZW49IjIwMjQtMTEtMTFUMTE6MjI6MTcrMDU6MzAiIHN0RXZ0OnNvZnR3YXJlQWdlbnQ9IkFkb2JlIFBob3Rvc2hvcCAyMS4yIChXaW5kb3dzKSIgc3RFdnQ6Y2hhbmdlZD0iLyIvPiA8L3JkZjpTZXE+IDwveG1wTU06SGlzdG9yeT4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4vMPQtAAAZ6klEQVR4nO3d34+cV33H8e95drIKqtD6olIvd/kLkt6h0nS9d84SNctFK7haI6hEIiHWF4mwKppHKuoib4pHFRVRkcogeoFEKzZSlVpNqp14E+pWIV1I1DgBwhhKU5JAxgmO7d3ZOb3wPI7X3h/z45znnPM975eEVEhin0rxx+846/mKAIk4u7F0bO25xfXQ7wBGYa0Va60UoR8CDKvX2F4RKw+ubT5wPPRbgFE1Qj8AGMbZjaVjPdleufHf+qWIHA/3GqTAWhv6CXtQtkhCr7G9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" />
                    </svg>
                <?php endif; ?>

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
                               tabindex="`4"
                               class="cre-ac"
                               title="<?php esc_attr_e( 'Create Account', 'disciple-tools-autolink' ); ?>">
                        <span><?php esc_html_e( 'Create Account', 'disciple-tools-autolink' ) ?></span>
                    </sp-button>
                </sp-button-group>
            </form>
        </div>
    </dt-tile>
</div>
<div class="footer text-color">
    <?php if ( !empty( $custom_logo ) ) : ?>
        <p><?php echo esc_html__( 'Powered by', 'dt-home' ); ?> <a
                href="https://disciple.tools/"
                class="text-color"><?php echo esc_html__( 'Disciple.Tools', 'dt-home' ); ?></a></p>
    <?php endif; ?>
    <div class="login__footer">
        <sp-button href="<?php echo esc_url( $reset_url ); ?>"
                   variant="secondary"
                   treatment="link"
        >
            <?php esc_html_e( 'Forgot Password?', 'disciple-tools-autolink' ); ?>
        </sp-button>
    </div>
</div>
<br>
<?php do_shortcode( '[dt_firebase_login_ui redirect_to="' . route_url( '' ) . '"]' ) ?>



