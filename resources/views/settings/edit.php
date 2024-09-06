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
get_template_part('dt-core/admin/menu/tabs/dialog-icon-selector');
?>

<form action="admin.php?page=dt_home&tab=app&action=edit/<?php echo esc_attr($existing_data['slug']); ?>" method="post"
      enctype="multipart/form-data">
    <?php wp_nonce_field('dt_admin_form_nonce') ?>

    <table class="widefat striped" id="ml_email_main_col_config">
    <thead>
    <tr>
        <th><?php esc_html_e('Apps', 'dt_home') ?></th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
    </thead>
   <tbody>
    <tr>
        <td style="vertical-align: middle;"><?php esc_html_e('Name', 'dt_home') ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Enter the name of the app.', 'dt_home') ?></span>
            </span>
        </td>
        <td colspan="3">
            <input style="min-width: 100%;" type="text" name="name" id="name" class="form-control"
                   pattern=".*\S+.*" title="<?php esc_attr_e('The name cannot be empty or just whitespace.', 'dt_home'); ?>"
                   value="<?php echo esc_attr($existing_data['name']); ?>" required>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: middle;"><?php esc_html_e('Type', 'dt_home') ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Select the type of the app.', 'dt_home') ?></span>
            </span>
        </td>
        <td colspan="3">
            <select style="min-width: 100%;" id="type" name="type" required onchange="toggleURLField()">
                <option value="" <?php echo empty($existing_data['type']) ? 'selected' : ''; ?>>
                    <?php esc_html_e('Please select', 'dt_home') ?>
                </option>
                <option value="Web View" <?php echo ($existing_data['type'] === 'Web View') ? 'selected' : ''; ?>>
                    <?php esc_html_e('Web View', 'dt_home') ?>
                </option>
                <option value="Link" <?php echo ($existing_data['type'] === 'Link') ? 'selected' : ''; ?>>
                    <?php esc_html_e('Link', 'dt_home') ?>
                </option>
            </select>
            <input name="creation_type" id="creation_type" type="hidden" value="<?php echo esc_attr( $existing_data['creation_type'] ?? '' ) ?>" />
        </td>
    </tr>
    <tr>
        <td style="vertical-align: middle;"><?php esc_html_e('Open link in new tab', 'dt_home') ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Check this box to open the link in a new tab.', 'dt_home') ?></span>
            </span>
        </td>
        <td colspan="2">
            <input type="checkbox" name="open_in_new_tab" id="open_in_new_tab" value="1"
                <?php checked($existing_data['open_in_new_tab'] ?? 0, 1); ?>>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: middle;"><?php esc_html_e('Icon (File Upload)', 'dt_home') ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Upload an icon for the app.', 'dt_home') ?></span>
            </span>
        </td>
        <td style="vertical-align: middle;">
            <?php if (!empty($existing_data['icon'])) : ?>
                <?php if (filter_var($existing_data['icon'], FILTER_VALIDATE_URL) || strpos($existing_data['icon'], '/wp-content/') === 0) : ?>
                    <img src="<?php echo esc_url($existing_data['icon']); ?>" alt="<?php esc_attr_e('Icon', 'dt_home'); ?>"
                         style="width: 50px; height: 50px;">
                <?php elseif (preg_match('/^mdi\smdi-/', $existing_data['icon'])) : ?>
                    <i class="<?php echo esc_attr($existing_data['icon']); ?>" style="font-size: 50px;"></i>
                <?php endif; ?>
            <?php endif; ?>
        </td>
        <td style="vertical-align: middle;">
            <input style="min-width: 100%;" type="text" id="app_icon" name="icon"
                   pattern=".*\S+.*" title="<?php esc_attr_e('The name cannot be empty or just whitespace.', 'dt_home'); ?>" required
                   value="<?php if (filter_var($existing_data['icon'], FILTER_VALIDATE_URL) || strpos($existing_data['icon'], '/wp-content/') === 0) : echo esc_url(isset($existing_data['icon']) ? $existing_data['icon'] : ''); elseif (preg_match('/^mdi\smdi-/', $existing_data['icon'])) : echo esc_attr($existing_data['icon']); endif; ?>"/>
        </td>
        <td style="vertical-align: middle;"><span id="app_icon_show"></span></td>
        <td style="vertical-align: middle;">
            <a href="#" class="button change-icon-button">
                <?php esc_html_e('Change Icon', 'dt_home'); ?>
            </a>
        </td>
    </tr>

    <?php if ($existing_data['type'] === 'Web View' || $existing_data['type'] === 'Link') { ?>
        <tr>
            <td style="vertical-align: middle;"><?php esc_html_e('URL', 'dt_home') ?>
                <span class="tooltip">[?]
                    <span class="tooltiptext"><?php esc_html_e('Enter the URL for the app.', 'dt_home') ?></span>
                </span>
            </td>
            <td colspan="3">
                <input style="min-width: 100%;" type="text" name="url" id="url" class="form-control"
                       value="<?php echo esc_url(isset($existing_data['url']) ? $existing_data['url'] : ''); ?>">
            </td>
        </tr>
    <?php } ?>

    <tr>
        <td style="vertical-align: middle;"><?php esc_html_e('Slug', 'dt_home') ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Enter a slug for the app.', 'dt_home') ?></span>
            </span>
        </td>
        <td colspan="2">
            <input style="min-width: 100%;" type="text" name="slug" id="slug" readonly
                   value="<?php echo esc_attr(isset($existing_data['slug']) ? $existing_data['slug'] : ''); ?>"
                   required/>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: middle;"><?php esc_html_e('Is Hidden', 'dt_home') ?>
            <span class="tooltip">[?]
                <span class="tooltiptext"><?php esc_html_e('Check this box to hide the app.', 'dt_home') ?></span>
            </span>
        </td>
        <td colspan="3">
            <input type="checkbox" name="is_hidden" id="is_hidden"
                   value="1" <?php checked($existing_data['is_hidden'], 1); ?>>
        </td>
    </tr>
</tbody>
</table>

    <br>
    <span style="float:right;">
        <a href="admin.php?page=dt_home&tab=app"
           class="button float-right"><?php esc_html_e('Cancel', 'dt_home') ?></a>
        <button type="submit" name="submit" id="submit"
                class="button float-right"><?php esc_html_e('Update', 'dt_home') ?></button>
    </span>
</form>

<?php //phpcs:ignoreEnd ?>

<?php $this->start('right') ?>

<!-- Add some content to the right side -->

<?php $this->stop() ?>
