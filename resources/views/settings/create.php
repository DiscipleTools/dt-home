<?php
// phpcs:ignoreFile
/**
 * @var string $tab
 * @var string $link
 * @var string $page_title
 */
$this->layout('layouts/settings', compact('tab', 'link', 'page_title'));
?>
<?php
// Include the dialog-icon-selector.php template
get_template_part('dt-core/admin/menu/tabs/dialog-icon-selector');
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
            <input style="min-width: 100%;" class="form-control" type="text" name="name" id="name" pattern=".*\S+.*" title="<?php esc_attr_e('The name cannot be empty or just whitespace.', 'dt-home'); ?>" required/>
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
            </select>
            <input name="creation_type" id="creation_type" type="hidden" value="custom" />
        </td>
    </tr>
    <tr>
        <td style="vertical-align: middle;"><?php esc_html_e('Open link in new tab', 'dt-home') ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Check this box to open the link in a new tab.', 'dt-home') ?></span>
            </span>
        </td>
        <td colspan="3">
            <input type="checkbox" name="open_in_new_tab" id="open_in_new_tab" value="1">
        </td>
    </tr>
    <tr>
        <td style="vertical-align: middle;"><?php esc_html_e('Icon (File Upload)', 'dt-home') ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Upload an icon for the app.', 'dt-home') ?></span>
            </span>
        </td>
        <td style="vertical-align: middle;"><input style="min-width: 100%;" type="text" id="app_icon"  name="icon" pattern=".*\S+.*" title="<?php esc_attr_e('The name cannot be empty or just whitespace.', 'dt-home'); ?>" required/></td>
        <td style="vertical-align: middle;"><span id="app_icon_show"></span></td>
        <td style="vertical-align: middle;">
            <a href="#" class="button change-icon-button">
                <?php esc_html_e('Change Icon', 'dt-home'); ?>
            </a>
        </td>
    </tr>
    <tr id="urlFieldRow">
        <td style="vertical-align: middle;"><?php esc_html_e('URL', 'dt-home') ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Enter the URL for the app.', 'dt-home') ?></span>
            </span>
        </td>
        <td colspan="3">
            <input style="min-width: 100%;" type="text" name="url" id="url"/>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: middle;"><?php esc_html_e('Slug', 'dt-home') ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Enter a slug for the app.', 'dt-home') ?></span>
            </span>
        </td>
        <td colspan="3">
            <input style="min-width: 100%;" type="text" name="slug" id="slug" pattern=".*\S+.*" title="<?php esc_attr_e('The name cannot be empty or just whitespace.', 'dt-home'); ?>" required/>
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
    </tbody>
</table>

    <br>
    <span id="ml_email_main_col_update_msg" style="font-weight: bold; color: red;"></span>
    <span style="float:right;">
        <a href="admin.php?page=dt_home&tab=app"
           class="button float-right"><?php esc_html_e('Cancel', 'dt-home') ?></a>
        <button type="submit" id="ml_email_main_col_update_but"
                class="button float-right"><?php esc_html_e('Submit', 'dt-home') ?></button>
    </span>
</form>

<div id="popup" class="popup">
    <input type="text" id="searchInput" onkeyup="filterIcons()" placeholder="<?php esc_attr_e('Search for icons...', 'dt-home'); ?>"
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
