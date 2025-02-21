<?php
function build_icon_tab_html( $params ): void
{
    $existing_icon = $params['existing_icon'] ?? '';
    $existing_svg_img = $params['existing_svg_img'] ?? '';
    $existing_color = $params['existing_color'] ?? '';
    $icon_input_name = $params['icon_input_name'] ?? '';
    $selected_icon_placeholder_name = $params['selected_icon_placeholder_name'] ?? '';
    $color_input_name = $params['color_input_name'] ?? '';
    $is_icon_input_required = $params['icon_input_required'] ?? true;
    ?>
    <table style="min-width: 100%;">
        <tr>
            <?php if ( !empty( $existing_icon ) ) : ?>
                <td style="vertical-align: middle;">
                    <?php if ( filter_var( $existing_icon, FILTER_VALIDATE_URL ) || strpos( $existing_icon, '/wp-content/' ) === 0 ) : ?>
                        <img src="<?php echo esc_url( $existing_icon ); ?>"
                             alt="<?php esc_attr_e( 'Icon', 'dt-home' ); ?>"
                             style="width: 25px; height: 25px;">
                    <?php elseif ( preg_match( '/^mdi\smdi-/', $existing_icon ) ) : ?>
                        <i class="<?php echo esc_attr( $existing_icon ); ?>" style="font-size: 25px;"></i>
                    <?php endif; ?>
                </td>
            <?php endif; ?>
            <td style="vertical-align: middle;">
                <input style="min-width: 100%;" type="text" id="<?php echo esc_attr( $icon_input_name ); ?>"
                       name="<?php echo esc_attr( $icon_input_name ); ?>"
                       pattern=".*\S+.*"
                       title="<?php esc_attr_e( 'The name cannot be empty or just whitespace.', 'dt-home' ); ?>"
                    <?php echo esc_attr( $is_icon_input_required ? 'required' : '' ); ?>
                       value="<?php
						if ( filter_var( $existing_icon, FILTER_VALIDATE_URL ) || strpos( $existing_icon, '/wp-content/' ) === 0 ) :
							echo esc_url( isset( $existing_icon ) ? $existing_icon : '' );
                       elseif ( preg_match( '/^mdi\smdi-/', $existing_icon ) ) :
                           echo esc_attr( $existing_icon );
                       endif;
						?>"
                       data-icon_selected_placeholder="<?php echo esc_attr( $selected_icon_placeholder_name ); ?>"
                />
            </td>
            <td style="vertical-align: middle;"><span id="<?php echo esc_attr( $selected_icon_placeholder_name ); ?>"
                                                      style="font-size: 25px; width: 25px; height: 25px;"></span></td>
            <td style="vertical-align: middle;">
    <span class="icon-button-container" style="float: right;">
        <a href="#" class="button change-icon-button-selector"
           data-item="<?php echo esc_attr( htmlspecialchars( $existing_svg_img ) ); ?>"
           data-icon="<?php echo esc_attr( $icon_input_name ); ?>">
            <?php esc_html_e( 'Change Icon', 'dt-home' ); ?>
        </a>
        <i class="button mdi mdi-invert-colors-off app-color-reset"
           data-color="<?php echo esc_attr( $color_input_name ); ?>"></i>
    </span>
            </td>
        </tr>
        <tr>
            <td colspan="<?php echo ( !empty( $existing_icon ) ) ? 4 : 3 ?>">
                <?php
                $color_input_name_hidden = $color_input_name . '_hidden';
                ?>

                <input type="color" name="<?php echo esc_attr( $color_input_name ); ?>"
                       id="<?php echo esc_attr( $color_input_name ); ?>"
                       style="min-width: 100%;" value="<?php echo esc_attr( $existing_color ); ?>"
                       onchange="document.getElementById('<?php echo( esc_attr( $color_input_name_hidden ) ); ?>').value = '';">
                <!-- Always remove color delete flag on new onchange selections. -->

                <input type="hidden" name="<?php echo( esc_attr( $color_input_name_hidden ) ); ?>"
                       id="<?php echo( esc_attr( $color_input_name_hidden ) ); ?>"/>
            </td>
        </tr>
    </table>
    <?php
}



