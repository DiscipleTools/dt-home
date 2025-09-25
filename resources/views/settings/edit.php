<?php
// phpcs:ignoreFile
/**
 * @var string $tab
 * @var string $link
 * @var string $page_title
 * @var array $existing_data
 */

use DT\Home\Services\RolesPermissions;
use function DT\Home\container;

$this->layout('layouts/settings', compact('tab', 'link', 'page_title'));
?>

<?php
get_template_part('dt-core/admin/menu/tabs/dialog-icon-selector');
?>

<form action="admin.php?page=dt_home&tab=app&action=edit/<?php echo esc_attr($existing_data['slug']); ?>" method="post"
      enctype="multipart/form-data">
    <?php wp_nonce_field('dt_admin_form_nonce') ?>

    <table class="widefat striped" id="ml_email_main_col_config">
    <thead>
    <tr>
        <th><?php esc_html_e('Apps', 'dt-home') ?></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
    </thead>
   <tbody>
    <tr>
        <td style="vertical-align: middle;"><?php esc_html_e('Name', 'dt-home') ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Enter the name of the app.', 'dt-home') ?></span>
            </span>
        </td>
        <td colspan="4">
            <input style="min-width: 100%;" type="text" name="name" id="name" class="form-control"
                   pattern=".*\S+.*" title="<?php esc_attr_e('The name cannot be empty or just whitespace.', 'dt-home'); ?>"
                   value="<?php echo esc_attr($existing_data['name']); ?>" required>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: middle;"><?php esc_html_e('Type', 'dt-home') ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Select the type of the app.', 'dt-home') ?></span>
            </span>
        </td>
        <td colspan="4">
            <select style="min-width: 100%;" id="type" name="type" required onchange="toggleURLField()">
                <option value="" <?php echo empty($existing_data['type']) ? 'selected' : ''; ?>>
                    <?php esc_html_e('Please select', 'dt-home') ?>
                </option>
                <option value="Web View" <?php echo ($existing_data['type'] === 'Web View') ? 'selected' : ''; ?>>
                    <?php esc_html_e('Web View', 'dt-home') ?>
                </option>
                <option value="Link" <?php echo ($existing_data['type'] === 'Link') ? 'selected' : ''; ?>>
                    <?php esc_html_e('Link', 'dt-home') ?>
                </option>
            </select>
            <input name="creation_type" id="creation_type" type="hidden" value="<?php echo esc_attr( $existing_data['creation_type'] ?? '' ) ?>" />
        </td>
    </tr>
    <tr>
        <td style="vertical-align: middle;"><?php esc_html_e('Open link in new tab', 'dt-home') ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Check this box to open the link in a new tab.', 'dt-home') ?></span>
            </span>
        </td>
        <td colspan="4">
            <input type="checkbox" name="open_in_new_tab" id="open_in_new_tab" value="1"
                <?php checked($existing_data['open_in_new_tab'] ?? 0, 1); ?>>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: middle;"><?php esc_html_e('Icon (File Upload)', 'dt-home') ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Upload an icon for the app.', 'dt-home') ?></span>
            </span>
        </td>
        <td style="vertical-align: middle; text-align: center; padding: 8px;">
            <?php if (!empty($existing_data['icon'])) : ?>
                <?php if (filter_var($existing_data['icon'], FILTER_VALIDATE_URL) || strpos($existing_data['icon'], '/wp-content/') === 0) : ?>
                    <img src="<?php echo esc_url($existing_data['icon']); ?>" alt="<?php esc_attr_e('Icon', 'dt-home'); ?>"
                         style="width: 32px; height: 32px; object-fit: contain;">
                <?php elseif (preg_match('/^mdi\smdi-/', $existing_data['icon'])) : ?>
                    <i class="<?php echo esc_attr($existing_data['icon']); ?>" style="font-size: 32px;"></i>
                <?php endif; ?>
            <?php endif; ?>
        </td>
        <td style="vertical-align: middle;">
            <input style="min-width: 100%;" type="text" id="app_icon" name="icon"
                   pattern=".*\S+.*" title="<?php esc_attr_e('The name cannot be empty or just whitespace.', 'dt-home'); ?>" required
                   value="<?php if (filter_var($existing_data['icon'], FILTER_VALIDATE_URL) || strpos($existing_data['icon'], '/wp-content/') === 0) : echo esc_url(isset($existing_data['icon']) ? $existing_data['icon'] : ''); elseif (preg_match('/^mdi\smdi-/', $existing_data['icon'])) : echo esc_attr($existing_data['icon']); endif; ?>"/>
        </td>
        <td style="vertical-align: middle;"><span id="app_icon_show"></span></td>
        <td style="vertical-align: middle;">
            <a href="#" class="button change-icon-button">
                <?php esc_html_e('Change Icon', 'dt-home'); ?>
            </a>
        </td>
    </tr>

    <?php if ($existing_data['type'] === 'Web View' || $existing_data['type'] === 'Link') { ?>
        <tr>
            <td style="vertical-align: middle;"><?php esc_html_e('URL', 'dt-home') ?>
                <span class="tooltip">[?]
                    <span class="tooltiptext"><?php esc_html_e('Enter the URL for the app.', 'dt-home') ?></span>
                </span>
            </td>
            <td colspan="4">
                <input style="min-width: 100%;" type="text" name="url" id="url" class="form-control"
                       value="<?php echo esc_url(isset($existing_data['url']) ? $existing_data['url'] : ''); ?>">
            </td>
        </tr>
    <?php } ?>

    <tr>
        <td style="vertical-align: middle;"><?php esc_html_e('Slug', 'dt-home') ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Enter a slug for the app.', 'dt-home') ?></span>
            </span>
        </td>
        <td colspan="4">
            <input style="min-width: 100%;" type="text" name="slug" id="slug" readonly
                   value="<?php echo esc_attr(isset($existing_data['slug']) ? $existing_data['slug'] : ''); ?>"
                   required/>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: middle;"><?php esc_html_e('Is Hidden', 'dt-home') ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Check this box to hide the app.', 'dt-home') ?></span>
            </span>
        </td>
        <td colspan="4">
            <input type="checkbox" name="is_hidden" id="is_hidden"
                   value="1" <?php checked($existing_data['is_hidden'], 1); ?>>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top;"><?php esc_html_e('Roles', 'dt-home') ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Select which user roles can access app.', 'dt-home') ?></span>
            </span>
        </td>
        <td colspan="4">
            <?php
            $counter = 0;
            $max_row_count = 3;
            $support_specific_roles = isset( $existing_data['user_roles_type'] ) && $existing_data['user_roles_type'] === 'support_specific_roles';
            ?>
            <table style="min-width: 100%;">
                <tbody>
                <tr>
                    <td style="padding-left: 0;" colspan="<?php echo esc_attr( $max_row_count ); ?>">
                        <div>
                            <table>
                                <tr>
                                    <td style="padding-left: 0 !important;">
                                        <label>
                                            <input type="radio" id="select_all_user_roles" name="user_roles_type" value="support_all_roles" <?php echo ( $support_specific_roles === false ? 'checked' : '' ) ?> />
                                            <?php esc_html_e('Select all roles', 'dt-home'); ?>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input type="radio" id="select_specific_user_roles" name="user_roles_type" value="support_specific_roles" <?php echo ( $support_specific_roles === true ? 'checked' : '' ) ?> />
                                            <?php esc_html_e('Select specific roles', 'dt-home'); ?>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                            <hr>
                        </div>
                    </td>
                </tr>
                <?php
                $roles_permissions_srv = container()->get( RolesPermissions::class );
                $dt_custom_roles = $roles_permissions_srv->get_dt_roles_and_permissions();
                ksort( $dt_custom_roles );
                foreach ( $dt_custom_roles as $key => $role ) {

                    /**
                     * Determine if role should be checked; ensuring globally set custom
                     * roles and permissions take priority.
                     */

                    $is_checked = false;
                    $permission = $roles_permissions_srv->generate_permission_key( $existing_data['slug'] ?? '' );

                    if ( isset( $role['capabilities'][ $permission ] ) ) {
                        $is_checked = $role['capabilities'][ $permission ];

                    } else {
                        $is_checked = in_array( $key, $existing_data['roles'] ?? [] );
                    }

                    // Determine if a new row should be started.
                    if ( $counter === 0 ) {
                        ?>
                        <tr class="user-roles-type-tr" <?php echo ( $support_specific_roles === false ? 'style="display: none;"' : '' ) ?> >
                        <?php
                    }
                    ?>

                    <td style="padding-left: 0;">
                        <div>
                            <label>
                                <input type="checkbox" name="roles[]" class="apps-user-role" value="<?php echo esc_attr( $key ); ?>" <?php echo ( $is_checked ? 'checked' : '' ); ?> />
                                <?php echo esc_html( $role['label'] ?? $key ); ?>
                            </label>
                        </div>
                    </td>

                    <?php

                    // Determine if row should be closed.
                    if ( ++$counter >= $max_row_count ) {
                        $counter = 0;
                        ?>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
            <input type="hidden" name="deleted_roles" id="deleted_roles" value="[]">
        </td>
    </tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5">
                    <span style="float:right;">
                        <a href="admin.php?page=dt_home&tab=app"
                           class="button float-right"><?php esc_html_e('Cancel', 'dt-home') ?></a>
                        <button type="submit" name="submit" id="submit"
                                class="button float-right"><?php esc_html_e('Update', 'dt-home') ?></button>
                    </span>
            </td>
        </tr>
    </tfoot>
</table>
</form>

<?php //phpcs:ignoreEnd ?>

<?php $this->start('right') ?>

<!-- Add some content to the right side -->

<?php $this->stop() ?>
