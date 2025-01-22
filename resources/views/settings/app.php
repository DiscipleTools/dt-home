<?php

use function DT\Home\get_magic_url;

$this->layout( 'layouts/settings', compact( 'tab', 'link', 'page_title' ) )
?>
<form method="post">
    <?php wp_nonce_field( 'dt_admin_form', 'dt_admin_form_nonce' ) ?>

    <!-- Add a form -->
</form>
<!-- Custom Popup Model-->
<div id="overlay" class="overlay"></div>
<div id="exportPopup" class="popup-model" style="display: none" data-apps='<?php echo json_encode( $data ); ?>'
     data-site-domain="<?php echo esc_url( get_site_url() ); ?>">
    <div class="popup-content">
        <div class="popup-header">
            <h2 style="float: left"><?php esc_html_e( 'Export Apps', 'dt-home' ); ?></h2>
            <span class="close close-button" style="float: right">&times;</span>
        </div>
        <div class="popup-tabs">
            <ul class="tabs">
                <li class="tab active" data-tab="shareContent"><?php esc_html_e( 'Share', 'dt-home' ); ?></li>
                <li class="tab" data-tab="copyContent"><?php esc_html_e( 'Copy', 'dt-home' ); ?></li>
            </ul>
        </div>

        <div class="tab-content-container">
            <div id="shareContent" class="tab-content active">
                <p><?php esc_html_e( 'Magic link URL:', 'dt-home' ); ?></p>

                <input type="text" id="exportLink" class="form-control styled-textbox" value="" readonly>&nbsp;&nbsp;&nbsp;&nbsp;
                <div class="qr-code-container">
                    <img
                        id="qrCodeImage"
                        src=""
                        title=""
                        alt="" />
                </div>
            </div>
            <div id="copyContent" class="tab-content">
                <textarea id="exportTextarea" rows="16" class="form-control text-area" readonly></textarea>
            </div>
        </div>
        <div class="popup-footer">
            <button id="copyButton" class="button"><i
                    class="fa fa-copy"></i>&nbsp;<?php esc_html_e( 'Copy', 'dt-home' ); ?></button>
            &nbsp;&nbsp;&nbsp;
            <button class="button close-button"><?php esc_html_e( 'Close', 'dt-home' ); ?></button>
        </div>
    </div>
</div>


<dialog id="apps_settings_dialog_placeholder"></dialog>

<div class="wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <span>
                    <a href="admin.php?page=dt_home&tab=app&action=create" class="button">
                        <i class="fa fa-plus"></i> <?php esc_html_e( 'Add App', 'dt-home' ); ?>
                    </a>
                </span>
                <span>
                    <a href="admin.php?page=dt_home&tab=app&action=available_app" class="button float-right">
                        <i class="fas fa-trash-restore"></i> <?php esc_html_e( 'Available Apps', 'dt-home' ); ?>
                    </a>
                </span>
                <div class="apps-btn">
                    <span>
                        <a id="import_apps_but" href="#" class="button float-right">
                            <i class="fas fa-file-import"></i> <?php esc_html_e( 'Import Apps', 'dt-home' ); ?>
                        </a>
                    </span>
                    <span>
                        <button class="button" id="exportButton" disabled>
                           <i class="fas fa-file-export"></i> <?php esc_html_e( 'Export Apps', 'dt-home' ); ?>
                        </button>
                    </span>
                </div>
                <br>
                <div style="overflow-x:auto;">
                    <table class="widefat striped" style="border-collapse: collapse; width: 100%;">
                        <thead>
                        <tr>
                            <th style="border: 1px solid #ddd;">
                                <input type="checkbox" id="select_all_checkbox" class="select-all custom-checkbox">
                            </th>
                            <th style="border: 1px solid #ddd;"><?php esc_html_e( 'Name', 'dt-home' ); ?></th>
                            <th style="border: 1px solid #ddd;"><?php esc_html_e( 'Type', 'dt-home' ); ?></th>
                            <th style="border: 1px solid #ddd;"><?php esc_html_e( 'Icon', 'dt-home' ); ?></th>
                            <th style="border: 1px solid #ddd;"><?php esc_html_e( 'Slug', 'dt-home' ); ?></th>
                            <th style="border: 1px solid #ddd; min-width: 100%;"><?php esc_html_e( 'Action', 'dt-home' ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ( $data as $app ) : ?>
                            <?php
                            $app_type_label_prefix = '';
                            switch ( $app['creation_type'] ?? '' ) {
                                case 'code':
                                    $app_type_label_prefix = 'Code / ';
                                    break;
                                case 'custom':
                                    $app_type_label_prefix = 'Custom / ';
                                    break;
                                default:
                                    break;
                            }
                            $app_type_label_prefix .= $app['type'];
                            ?>
                            <tr>
                                <td style="border: 1px solid #ddd;"><input type="checkbox"
                                                                           value="<?php echo esc_attr( $app['slug'] ); ?>"
                                                                           class="app-checkbox custom-checkbox">
                                </td>
                                <td style="border: 1px solid #ddd;"><?php echo esc_html( $app['name'] ); ?></td>
                                <td style="border: 1px solid #ddd;"><?php echo esc_html( $app_type_label_prefix ); ?></td>
                                <td style="border: 1px solid #ddd; vertical-align: middle;">
                                    <?php if ( !empty( $app['icon'] ) ) : ?>
                                        <?php if ( filter_var( $app['icon'], FILTER_VALIDATE_URL ) || strpos( $app['icon'], '/wp-content/' ) === 0 ) : ?>
                                            <img src="<?php echo esc_url( $app['icon'] ); ?>"
                                                 alt="<?php esc_attr_e( 'Icon', 'dt-home' ); ?>"
                                                 style="width: 25px; height: 25px;">
                                        <?php elseif ( preg_match( '/^mdi\smdi-/', $app['icon'] ) ) : ?>
                                            <i class="<?php echo esc_attr( $app['icon'] ); ?>"
                                               style="font-size: 25px;"></i>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td style="border: 1px solid #ddd;"><?php echo esc_attr( $app['slug'] ); ?></td>
                                <td style="border: 1px solid #ddd;">
                                    <div class="action-tooltip">
                                        <a href="admin.php?page=dt_home&tab=app&action=up/<?php echo esc_attr( $app['slug'] ); ?>">
                                            <i class="fas fa-arrow-up action-icon"></i>
                                        </a>
                                        <span
                                            class="action-tooltip-text"><?php esc_attr_e( 'Move Up', 'dt-home' ); ?></span>
                                    </div>
                                    &nbsp;|&nbsp;
                                    <div class="action-tooltip">
                                        <a href="admin.php?page=dt_home&tab=app&action=down/<?php echo esc_attr( $app['slug'] ); ?>">
                                            <i class="fas fa-arrow-down action-icon"></i>
                                        </a>
                                        <span
                                            class="action-tooltip-text"><?php esc_attr_e( 'Move Down', 'dt-home' ); ?></span>
                                    </div>
                                    &nbsp;|&nbsp;
                                    <div class="action-tooltip">
                                        <?php if ( $app['is_hidden'] == 1 ) { ?>
                                            <a href="admin.php?page=dt_home&tab=app&action=unhide/<?php echo esc_attr( $app['slug'] ); ?>">
                                                <i class="fas fa-eye action-icon"></i>
                                            </a>
                                            <span
                                                class="action-tooltip-text"><?php esc_attr_e( 'Unhide', 'dt-home' ); ?></span>
                                        <?php } else { ?>
                                            <a href="admin.php?page=dt_home&tab=app&action=hide/<?php echo esc_attr( $app['slug'] ); ?>">
                                                <i class="fas fa-eye-slash action-icon"></i>
                                            </a>
                                            <span
                                                class="action-tooltip-text"><?php esc_attr_e( 'Hide', 'dt-home' ); ?></span>
                                        <?php } ?>
                                    </div>
                                    &nbsp;|&nbsp;
                                    <div class="action-tooltip">
                                        <a href="admin.php?page=dt_home&tab=app&action=edit/<?php echo esc_attr( $app['slug'] ); ?>">
                                            <i class="fas fa-edit action-icon"></i>
                                        </a>
                                        <span class="action-tooltip-text"><?php esc_attr_e( 'Edit', 'dt-home' ); ?></span>
                                    </div>
                                    &nbsp;|&nbsp;
                                    <div class="action-tooltip">
                                        <a href="javascript:void(0)"
                                           onclick="copyApp('<?php echo esc_attr( $app['slug'] ); ?>', this)">
                                            <i class="fas fa-copy action-icon"></i>
                                        </a>
                                        <span class="action-tooltip-text"><?php esc_attr_e( 'Copy', 'dt-home' ); ?></span>
                                    </div>
                                    &nbsp;|&nbsp;
                                    <div class="action-tooltip">
                                        <?php if ( !isset( $app['creation_type'] ) || ( $app['creation_type'] != 'code' ) ) { ?>
                                            <a href="#" onclick="deleteApp('<?php echo esc_attr( $app['slug'] ); ?>')"
                                               class="delete-apps">
                                                <i class="fas fa-trash action-icon"></i>
                                            </a>
                                            <span
                                                class="action-tooltip-text"><?php esc_attr_e( 'Delete', 'dt-home' ); ?></span>
                                        <?php } else { ?>
                                            <a href="#" onclick="softdelete('<?php echo esc_attr( $app['slug'] ); ?>')"
                                               class="delete-apps">
                                                <i class="fas fa-trash action-icon"></i>
                                            </a>
                                            <span
                                                class="action-tooltip-text"><?php esc_attr_e( 'Soft Delete', 'dt-home' ); ?></span>
                                        <?php } ?>
                                    </div>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        function deleteApp(slug) {
            var confirmation = confirm(<?php echo json_encode( __( 'Are you sure you want to delete this app?', 'dt-home' ) ); ?>)
            if (confirmation) {
                // If the user confirms, redirect to the delete URL
                window.location.href = 'admin.php?page=dt_home&tab=app&action=delete/' + slug
            }
            // If the user cancels, do nothing
        }

        function softdelete(slug) {
            var confirmation = confirm(<?php echo json_encode( __( 'Are you sure you want to delete this app?', 'dt-home' ) ); ?>)
            if (confirmation) {
                // If the user confirms, redirect to the delete URL
                window.location.href = 'admin.php?page=dt_home&tab=app&action=softdelete/' + slug
            }
            // If the user cancels, do nothing
        }

        document.addEventListener('DOMContentLoaded', () => {
            const tabs = document.querySelectorAll('.tabs .tab')
            const contents = document.querySelectorAll('.tab-content')

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('active'))
                    contents.forEach(content => content.classList.remove('active'))

                    // Add active class to clicked tab and corresponding content
                    tab.classList.add('active')
                    const contentId = tab.getAttribute('data-tab')
                    document.getElementById(contentId).classList.add('active')
                })
            })
        })

    </script>
    <?php $this->start( 'right' ) ?>

    <!-- Add some content to the right side -->

    <?php $this->stop() ?>
