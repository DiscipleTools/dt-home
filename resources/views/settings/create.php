<?php
// phpcs:ignoreFile

/**
 * @var string $tab
 * @var string $link
 * @var string $page_title
 * @var string $svg_images
 */

use DT\Home\Services\RolesPermissions;
use function DT\Home\container;

$this->layout('layouts/settings', compact('tab', 'link', 'page_title'));
?>
<?php
// Include the dialog-icon-selector.php template
get_template_part('dt-core/admin/menu/tabs/dialog-icon-selector');

require_once 'icons-functions.php';
?>

<!-- Rest of your code -->

<form action="admin.php?page=dt_home&tab=app&action=create" id="app_form" name="app_form" method="post"
      enctype="multipart/form-data">
    <?php wp_nonce_field('dt_admin_form_nonce') ?>

    <table class="widefat striped" id="ml_email_main_col_config">
        <thead>
        <tr>
            <th><?php esc_html_e('Apps', 'dt-home') ?></th>
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
            <td colspan="3">
                <input style="min-width: 100%;" class="form-control" type="text" name="name" id="name" pattern=".*\S+.*"
                       title="<?php esc_attr_e('The name cannot be empty or just whitespace.', 'dt-home'); ?>"
                       required />
            </td>
        </tr>
        <tr>
            <td style="vertical-align: middle;"><?php esc_html_e('Slug', 'dt-home') ?>
                <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Enter a slug for the app.', 'dt-home') ?></span>
            </span>
            </td>
            <td colspan="3">
                <input style="min-width: 100%;" type="text" name="slug" id="slug" pattern=".*\S+.*"
                       title="<?php esc_attr_e('The name cannot be empty or just whitespace.', 'dt-home'); ?>"
                       required />
            </td>
        </tr>
        <tr>
            <td style="vertical-align: middle;"><?php esc_html_e('Type', 'dt-home') ?>
                <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Select the type of the app.', 'dt-home') ?></span>
            </span>
            </td>
            <td colspan="3">
                <select style="min-width: 100%;" name="type" id="type" required onchange="toggleURLField()">
                    <option value=""><?php esc_html_e('Please select', 'dt-home') ?></option>
                    <option value="Web View"><?php esc_html_e('Web View', 'dt-home') ?></option>
                    <option value="Link"><?php esc_html_e('Link', 'dt-home') ?></option>
                    <option value="Native App Link"><?php esc_html_e('Native App Link', 'dt-home') ?></option>
                </select>
                <input name="creation_type" id="creation_type" type="hidden" value="custom" />
            </td>
        </tr>

        <tr id="urlFieldRow">
            <td style="vertical-align: middle;"><?php esc_html_e('URL', 'dt-home') ?>
                <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Enter the URL for the app.', 'dt-home') ?></span>
            </span>
            </td>
            <td colspan="3">
                <input style="min-width: 100%;" type="text" name="url" id="url" pattern=".*\S+.*"
                       title="<?php esc_attr_e('The name cannot be empty or just whitespace.', 'dt-home'); ?>"
                       required />
            </td>
        </tr>
        <tr id="fallback_url_ios_row">
            <td style="vertical-align: middle;"><?php esc_html_e('Fallback URL IOS', 'dt-home') ?>
                <span class="tooltip">[?]
        <span class="tooltiptext"><?php esc_html_e('Enter the URL for the ios app.', 'dt-home') ?></span>
    </span>
            </td>
            <td colspan="3">
                <input style="min-width: 100%;" type="text" name="fallback_url_ios" id="fallback_url_ios" />
            </td>
        </tr>
        <tr id="fallback_url_android_row">
            <td style="vertical-align: middle;"><?php esc_html_e('Fallback URL Android', 'dt-home') ?>
                <span class="tooltip">[?]
        <span class="tooltiptext"><?php esc_html_e('Enter the URL for the android app.', 'dt-home') ?></span>
    </span>
            </td>
            <td colspan="3">
                <input style="min-width: 100%;" type="text" name="fallback_url_android" id="fallback_url_android" />
            </td>
        </tr>
        <tr id="fallback_url_others_row">
            <td style="vertical-align: middle;"><?php esc_html_e('Fallback URL Others', 'dt-home') ?>
                <span class="tooltip">[?]
        <span class="tooltiptext"><?php esc_html_e('Enter the URL for the other app.', 'dt-home') ?></span>
    </span>
            </td>
            <td colspan="3">
                <input style="min-width: 100%;" type="text" name="fallback_url_others" id="fallback_url_others" />
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
                            'existing_svg_img' => $svg_images ?? '',
                            'existing_color' => '#000000',
                            'icon_input_name' => 'icon',
                            'selected_icon_placeholder_name' => 'icon_selected_placeholder',
                            'color_input_name' => 'icon_color',
                            'icon_input_required' => true
                        ]); ?>
                    </div>
                    <div class="app-icon-tab-dark" style="display: none;">
                        <?php build_icon_tab_html([
                            'existing_svg_img' => $svg_images ?? '',
                            'existing_color' => '#F5F5F5',
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
            <td colspan="3">
                <input type="checkbox" name="open_in_new_tab" id="open_in_new_tab" value="1">
            </td>
        </tr>
        <tr>
            <td style="vertical-align: middle;"><?php esc_html_e('Is Hidden', 'dt-home') ?>
                <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Check this box to hide the app.', 'dt-home') ?></span>
            </span>
            </td>
            <td colspan="3">
                <input type="checkbox" name="is_hidden" id="is_hidden" value="1">
            </td>
        </tr>
        <tr>
            <td style="vertical-align: middle;"><?php esc_html_e('Is Exportable', 'dt-home') ?>
                <span class="tooltip">[?]
                <span
                    class="tooltiptext"><?php esc_html_e('Check this box to ensure app is also included within json magic link endpoint output.', 'dt-home') ?></span>
            </span>
            </td>
            <td colspan="3">
                <input type="checkbox" name="is_exportable" id="is_exportable" value="1">
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top;"><?php esc_html_e('Roles', 'dt-home') ?>
                <span class="tooltip">[?]
                <span
                    class="tooltiptext"><?php esc_html_e('Select which user roles can access app.', 'dt-home') ?></span>
            </span>
            </td>
            <td colspan="3">
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
                                    <input type="checkbox" id="select_all_user_roles" checked />
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
                                           value="<?php echo esc_attr($key); ?>" checked />
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
            <td colspan="4">
                    <span style="float:right;">
                        <a href="admin.php?page=dt_home&tab=app"
                           class="button float-right"><?php esc_html_e('Cancel', 'dt-home') ?></a>
                        <button type="submit" id="ml_email_main_col_update_but"
                                class="button float-right"><?php esc_html_e('Submit', 'dt-home') ?></button>
                    </span>
            </td>
        </tr>
        </tfoot>
    </table>

    <br>
    <span id="ml_email_main_col_update_msg" style="font-weight: bold; color: red;"></span>
</form>

<div id="popup" class="popup">
    <input type="text" id="searchInput" onkeyup="filterIcons()"
           placeholder="<?php esc_attr_e('Search for icons...', 'dt-home'); ?>"
           style="width: 100%; padding: 10px; margin-bottom: 10px;">
    <div class="svg-container" id="svgContainer">
        <!-- SVG icons will be dynamically inserted here -->
    </div>
    <br>
    <button class="btn btn-secondary" onclick="hidePopup()"><?php esc_html_e('Close', 'dt-home') ?></button>
</div>

<?php $this->start('right') ?>

<!-- Add some content to the right side -->

<?php $this->stop() ?>

<script>
    function toggleURLField() {
        var typeSelect = document.getElementById('type')
        var urlFieldRow = document.getElementById('urlFieldRow')

        var fallbackUrlIosRow = document.getElementById('fallback_url_ios_row')
        var fallbackUrlAndroidRow = document.getElementById('fallback_url_android_row')
        var fallbackUrlOthersRow = document.getElementById('fallback_url_others_row')

        var fallbackUrlIos = document.getElementById('fallback_url_ios')
        var fallbackUrlAndroid = document.getElementById('fallback_url_android')
        var fallbackUrlOthers = document.getElementById('fallback_url_others')

        if (!typeSelect || !urlFieldRow) {
            return
        }

        if (typeSelect.value === 'Custom') {
            urlFieldRow.style.display = 'none'
        } else {
            urlFieldRow.style.display = ''
        }

        if (typeSelect.value === 'Native App Link') {
            fallbackUrlIosRow.style.display = ''
            fallbackUrlAndroidRow.style.display = ''
            fallbackUrlOthersRow.style.display = ''
            fallbackUrlIos.required = true
            fallbackUrlAndroid.required = true
            fallbackUrlOthers.required = true
        } else {
            fallbackUrlIosRow.style.display = 'none'
            fallbackUrlAndroidRow.style.display = 'none'
            fallbackUrlOthersRow.style.display = 'none'
            fallbackUrlIos.required = false
            fallbackUrlAndroid.required = false
            fallbackUrlOthers.required = false
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleURLField()
        document.getElementById('type').addEventListener('change', toggleURLField)
    })

</script>



