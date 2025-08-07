<?php
// phpcs:ignoreFile

/**
 * @var string $tab
 * @var string $link
 * @var string $page_title
 * @var array $existing_data
 * @var string $svg_images
 */

use DT\Home\Services\RolesPermissions;
use function DT\Home\container;

$this->layout('layouts/settings', compact('tab', 'link', 'page_title'));
?>

<?php
get_template_part('dt-core/admin/menu/tabs/dialog-icon-selector');

require_once 'icons-functions.php';
?>

<form action="admin.php?page=dt_home&tab=app&action=edit/<?php echo esc_attr($existing_data['slug']); ?>"
      method="post"
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
                       pattern=".*\S+.*"
                       title="<?php esc_attr_e('The name cannot be empty or just whitespace.', 'dt-home'); ?>"
                       value="<?php echo esc_attr($existing_data['name']); ?>" required>
            </td>
        </tr>
        <tr>
            <td style="vertical-align: middle;"><?php esc_html_e('Slug', 'dt-home') ?>
                <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Enter a slug for the app.', 'dt-home') ?></span>
            </span>
            </td>
            <td colspan="4">
                <input style="min-width: 100%;" type="text" name="slug" id="slug" readonly
                       value="<?php echo esc_attr(isset($existing_data['slug']) ? $existing_data['slug'] : ''); ?>"
                       required />
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
                    <option
                        value="Native App Link" <?php echo ($existing_data['type'] === 'Native App Link') ? 'selected' : ''; ?>>
                        <?php esc_html_e('Native App Link', 'dt-home') ?>
                    </option>
                </select>
                <input name="creation_type" id="creation_type" type="hidden"
                       value="<?php echo esc_attr($existing_data['creation_type'] ?? '') ?>" />
            </td>
        </tr>

        <?php if ($existing_data['type'] === 'Web View' || $existing_data['type'] === 'Link' || $existing_data['type'] === 'Native App Link') { ?>
            <tr>
                <td style="vertical-align: middle;"><?php esc_html_e('URL', 'dt-home') ?>
                    <span class="tooltip">[?]
                    <span class="tooltiptext"><?php esc_html_e('Enter the URL for the app.', 'dt-home') ?></span>
                </span>
                </td>
                <td colspan="4">
                    <input style="min-width: 100%;" type="text" name="url" id="url" class="form-control"
                           value="<?php echo esc_attr(isset($existing_data['url']) ? $existing_data['url'] : ''); ?>"
                           pattern=".*\S+.*"
                           title="<?php esc_attr_e('The name cannot be empty or just whitespace.', 'dt-home'); ?>"
                           required>
                </td>
            </tr>
        <?php } ?>

        <tr id="fallback_url_ios_row">
            <td style="vertical-align: middle;"><?php esc_html_e('Fallback URL IOS', 'dt-home') ?>
                <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Enter the URL for the IOS app.', 'dt-home') ?></span>
            </span>
            </td>
            <td colspan="4">
                <input style="min-width: 100%;" type="text" name="fallback_url_ios" id="fallback_url_ios"
                       value="<?php echo esc_attr(isset($existing_data['fallback_url_ios']) ? $existing_data['fallback_url_ios'] : ''); ?>" />
            </td>
        </tr>
        <tr id="fallback_url_android_row">
            <td style="vertical-align: middle;"><?php esc_html_e('Fallback URL Android', 'dt-home') ?>
                <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Enter the URL for the android app.', 'dt-home') ?></span>
            </span>
            </td>
            <td colspan="4">
                <input style="min-width: 100%;" type="text" name="fallback_url_android" id="fallback_url_android"
                       value="<?php echo esc_attr(isset($existing_data['fallback_url_android']) ? $existing_data['fallback_url_android'] : ''); ?>" />
            </td>
        </tr>
        <tr id="fallback_url_others_row">
            <td style="vertical-align: middle;"><?php esc_html_e('Fallback URL Others', 'dt-home') ?>
                <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Enter the URL for the other app.', 'dt-home') ?></span>
            </span>
            </td>
            <td colspan="4">
                <input style="min-width: 100%;" type="text" name="fallback_url_others" id="fallback_url_others"
                       value="<?php echo esc_attr(isset($existing_data['fallback_url_others']) ? $existing_data['fallback_url_others'] : ''); ?>" />
            </td>
        </tr>
        <tr>
            <td style="vertical-align: middle;"><?php esc_html_e('Icons', 'dt-home') ?>
                <span class="tooltip">[?]
                    <span
                        class="tooltiptext"><?php esc_html_e('Upload an icon for the app and specify theme colors to be adopted during light and dark modes.', 'dt-home') ?></span>
                </span>
            </td>
            <td colspan="4">
                <h3 class="nav-tab-wrapper">
                    <a href="#" class="nav-tab nav-tab-active app-icon-tab"
                       data-tab="app-icon-tab-light"><?php esc_html_e('Light Mode', 'dt-home') ?></a>
                    <a href="#" class="nav-tab app-icon-tab"
                       data-tab="app-icon-tab-dark"><?php esc_html_e('Dark Mode', 'dt-home') ?></a>
                </h3>

                <div class="app-icon-tab-content" style="margin-top: 15px; margin-bottom: 15px;">
                    <div class="app-icon-tab-light">
                        <?php build_icon_tab_html([
                            'existing_icon' => $existing_data['icon'] ?? '',
                            'existing_svg_img' => $svg_images ?? '',
                            'existing_color' => $existing_data['icon_color'] ?? '#000000',
                            'icon_input_name' => 'icon',
                            'selected_icon_placeholder_name' => 'icon_selected_placeholder',
                            'color_input_name' => 'icon_color',
                            'icon_input_required' => true
                        ]); ?>
                    </div>
                    <div class="app-icon-tab-dark" style="display: none;">
                        <?php build_icon_tab_html([
                            'existing_icon' => $existing_data['icon_dark'] ?? ($existing_data['icon'] ?? ''),
                            'existing_svg_img' => $svg_images ?? '',
                            'existing_color' => $existing_data['icon_dark_color'] ?? '#F5F5F5',
                            'icon_input_name' => 'icon_dark',
                            'selected_icon_placeholder_name' => 'icon_dark_selected_placeholder',
                            'color_input_name' => 'icon_dark_color',
                            'icon_input_required' => false
                        ]); ?>
                    </div>
                </div>
            </td>
        </tr>

        <tr>
            <td style="vertical-align: middle;"><?php esc_html_e('Open link in new tab', 'dt-home') ?>
                <span class="tooltip">[?]
                <span
                    class="tooltiptext"><?php esc_html_e('Check this box to open the link in a new tab.', 'dt-home') ?></span>
            </span>
            </td>
            <td colspan="4">
                <input type="checkbox" name="open_in_new_tab" id="open_in_new_tab" value="1"
                    <?php checked($existing_data['open_in_new_tab'] ?? 0, 1); ?>>
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
            <td style="vertical-align: middle;"><?php esc_html_e('Is Exportable', 'dt-home') ?>
                <span class="tooltip">[?]
                <span
                    class="tooltiptext"><?php esc_html_e('Check this box to ensure app is also included within json magic link endpoint output.', 'dt-home') ?></span>
            </span>
            </td>
            <td colspan="4">
                <input type="checkbox" name="is_exportable" id="is_exportable"
                       value="1" <?php checked($existing_data['is_exportable'] ?? false, 1); ?>>
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top;"><?php esc_html_e('Roles', 'dt-home') ?>
                <span class="tooltip">[?]
                <span
                    class="tooltiptext"><?php esc_html_e('Select which user roles can access app.', 'dt-home') ?></span>
            </span>
            </td>
            <td colspan="4">
                <?php
                $counter = 0;
                $max_row_count = 3;
                ?>
                <table>
                    <tbody>
                    <tr>
                        <td style="padding-left: 0;" colspan="<?php echo esc_attr($max_row_count); ?>">
                            <div>
                                <label>
                                    <input type="checkbox" id="select_all_user_roles" />
                                    <?php esc_html_e('Select all roles?', 'dt-home'); ?>
                                </label>
                                <hr>
                            </div>
                        </td>
                    </tr>
                    <?php
                    $roles_permissions_srv = container()->get(RolesPermissions::class);
                    $roles = Disciple_Tools_Roles::get_dt_roles_and_permissions(false);
                    ksort($roles);
                    foreach ($roles as $key => $role) {

                        /**
                         * Determine if role should be checked; ensuring globally set custom
                         * roles and permissions take priority.
                         */
                        $is_checked = false;
                        $permission = $roles_permissions_srv->generate_permission_key($existing_data['slug'] ?? '');


                        if (isset($role['permissions'][$permission])) {
                            $is_checked = $role['permissions'][$permission];
                        } else {
                            $is_checked = in_array($key, $existing_data['roles'] ?? []);
                        }

                        // Determine if a new row should be started.
                        if ($counter === 0) {
                            ?>
                            <tr>
                            <?php
                        }
                        ?>

                        <td style="padding-left: 0;">
                            <div>
                                <label>
                                    <input type="checkbox" name="roles[]" class="apps-user-role"
                                           value="<?php echo esc_attr($key); ?>" <?php echo($is_checked ? 'checked' : ''); ?> />
                                    <?php echo esc_html($role['label'] ?? $key); ?>
                                </label>
                            </div>
                        </td>

                        <?php

                        // Determine if row should be closed.
                        if (++$counter >= $max_row_count) {
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
<script>

    function toggleURLField() {
        const typeSelect = document.getElementById('type')
        const urlFieldRow = document.getElementById('urlFieldRow')
        const fallbackUrlIosRow = document.getElementById('fallback_url_ios_row')
        const fallbackUrlAndroidRow = document.getElementById('fallback_url_android_row')
        const fallbackUrlOthersRow = document.getElementById('fallback_url_others_row')

        const fallbackUrlIos = document.getElementById('fallback_url_ios')
        const fallbackUrlAndroid = document.getElementById('fallback_url_android')
        const fallbackUrlOthers = document.getElementById('fallback_url_others')

        if (!typeSelect) return

        // Hide all fallback rows and URL field by default
        if (urlFieldRow) urlFieldRow.style.display = 'none'
        if (fallbackUrlIosRow) fallbackUrlIosRow.style.display = 'none'
        if (fallbackUrlAndroidRow) fallbackUrlAndroidRow.style.display = 'none'
        if (fallbackUrlOthersRow) fallbackUrlOthersRow.style.display = 'none'

        if (fallbackUrlIos) fallbackUrlIos.required = false
        if (fallbackUrlAndroid) fallbackUrlAndroid.required = false
        if (fallbackUrlOthers) fallbackUrlOthers.required = false

        // Show only when "Native App Link" is selected
        if (typeSelect.value === 'Native App Link') {
            if (fallbackUrlIosRow) fallbackUrlIosRow.style.display = ''
            if (fallbackUrlAndroidRow) fallbackUrlAndroidRow.style.display = ''
            if (fallbackUrlOthersRow) fallbackUrlOthersRow.style.display = ''

            if (fallbackUrlIos) fallbackUrlIos.required = true
            if (fallbackUrlAndroid) fallbackUrlAndroid.required = true
            if (fallbackUrlOthers) fallbackUrlOthers.required = true
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleURLField()
        document.getElementById('type').addEventListener('change', toggleURLField)
    })

</script>
