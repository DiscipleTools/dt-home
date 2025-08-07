<?php

namespace DT\Home\Services;

use DT\Home\CodeZone\WPSupport\Assets\AssetQueue;
use DT\Home\CodeZone\WPSupport\Assets\AssetQueueInterface;
use function DT\Home\config;
use function DT\Home\get_plugin_option;
use function DT\Home\Kucrut\Vite\enqueue_asset;
use function DT\Home\namespace_string;
use function DT\Home\plugin_path;
use function DT\Home\plugin_url;


class Assets
{
    /**
     * AssetQueue Service.
     *
     * @var AssetQueue $asset_queue The AssetQueue instance.
     */
    private AssetQueueInterface $asset_queue;

    /**
     * Flag indicating whether a resource has been enqueued.
     *
     * @var bool $enqueued False if the resource has not been enqueued, true otherwise.
     */
    private static bool $enqueued = false;

    public function __construct( AssetQueueInterface $asset_queue )
    {
        $this->asset_queue = $asset_queue;
    }

    /**
     * Register method to add necessary actions for enqueueing scripts and adding cloaked styles
     *
     * @return void
     */
    public function enqueue()
    {
        if ( self::$enqueued ) {
            return;
        }
        self::$enqueued = true;

        if ( is_admin() ) {
            add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ], 1000 );
            add_action( 'admin_head', [ $this, 'cloak_style' ] );
        } else {
            add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 1000 );
            add_action( "wp_head", [ $this, 'cloak_style' ] );
            add_action( 'wp_head', [ $this, 'add_open_graph_meta_tags' ] );
            add_action( 'wp_print_styles', [ $this, 'wp_print_styles' ] );
        }
    }

    /**
     * Reset asset queue
     *
     * @return void
     */
    public function wp_print_styles()
    {
        $this->asset_queue->filter(
            apply_filters( namespace_string( 'allowed_scripts' ), [] ),
            apply_filters( namespace_string( 'allowed_styles' ), [] )
        );
    }


    /**
     * Enqueues scripts and styles for the frontend.
     *
     * This method enqueues the specified asset(s) for the frontend. It uses the "enqueue_asset" function to enqueue
     * the asset(s) located in the provided plugin directory path with the given filename. The asset(s) can be JavaScript
     * or CSS files. Optional parameters can be specified to customize the enqueue behavior.
     *
     * @return void
     * @see https://github.com/kucrut/vite-for-wp
     */
    public function wp_enqueue_scripts() {
        \DT\Home\Kucrut\Vite\enqueue_asset(config( 'assets.manifest_dir' ),
            'resources/js/plugin.js',
            [
                'handle' => 'dt-home',
                'css-media' => 'all', // Optional.
                'css-only' => false, // Optional. Set to true to only load style assets in production mode.
                'in-footer' => true, // Optional. Defaults to false.
        ]);
        $javascript_globals = config( 'assets.javascript_globals' );

        // Handle callback function for translations if it exists
        if ( isset( $javascript_globals['translations'] ) && is_callable( $javascript_globals['translations'] ) ) {
            $javascript_globals['translations'] = $javascript_globals['translations']();
        }

        wp_localize_script( 'dt-home', config( 'assets.javascript_global_scope' ), apply_filters( \DT\Home\namespace_string( 'javascript_globals' ), $javascript_globals ) );
        wp_register_style( 'material-font-icons-local', '/wp-content/plugins/dt-home/resources/css/materialdesignicons.min.css' );
        wp_enqueue_style( 'material-font-icons-local' );
    }

    /**
     * Enqueues the necessary assets for the admin area.
     *
     * This method is responsible for enqueuing the necessary JavaScript and CSS
     * assets for the admin area. It should be called during the 'admin_enqueue_scripts'
     * action hook.
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        enqueue_asset(
            plugin_path( '/dist' ),
            'resources/js/admin.js',
            [
                'handle' => 'dt-home',
                'css-media' => 'all', // Optional.
                'css-only' => false, // Optional. Set to true to only load style assets in production mode.
                'in-footer' => false, // Optional. Defaults to false.
            ]
        );

        wp_enqueue_script( 'common-js', admin_url() . 'js/common.min.js', true );
        wp_enqueue_script( 'underscore-js', includes_url( 'js/underscore.min.js' ), true, '1.13.4' );
        wp_enqueue_script( 'backbone-js', includes_url( 'js/backbone.min.js' ), true, '1.5.0' );

        wp_enqueue_script( 'wp-util-js', includes_url( 'js/wp-util.min.js' ), true );
        wp_localize_script('wp-util-js', '_wpUtilSettings', [
            'ajax' => [
                'url' => admin_url( 'admin-ajax.php' )
            ]
        ]);
        wp_enqueue_script( 'wp-backbone-js', includes_url( 'js/wp-backbone.min.js' ), true );
        wp_enqueue_script( 'media-models-js', includes_url( 'js/media-models.min.js' ), true );
        wp_localize_script('media-models-js', '_wpMediaModelsL10n', [
            'settings' => [
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'post' => [
                    'id' => 0,
                ],
            ],
        ]);
        wp_enqueue_script( 'wp-plupload-js', includes_url( 'js/plupload/wp-plupload.min.js' ), true );

        wp_localize_script('wp-plupload-js', 'pluploadL10n', [
            'queue_limit_exceeded' => 'You have attempted to queue too many files.',
            'file_exceeds_size_limit' => '%s exceeds the maximum upload size for this site.',
            'zero_byte_file' => 'This file is empty. Please try another.',
            'invalid_filetype' => 'Sorry, you are not allowed to upload this file type.',
            'not_an_image' => 'This file is not an image. Please try another.',
            'image_memory_exceeded' => 'Memory exceeded. Please try another smaller file.',
            'image_dimensions_exceeded' => 'This is larger than the maximum size. Please try another.',
            'default_error' => 'An error occurred in the upload. Please try again later.',
            'missing_upload_url' => 'There was a configuration error. Please contact the server administrator.',
            'upload_limit_exceeded' => 'You may only upload 1 file.',
            'http_error' => 'Unexpected response from the server. The file may have been uploaded successfully. Check in the Media Library or reload the page.',
            'http_error_image' => 'The server cannot process the image. This can happen if the server is busy or does not have enough resources to complete the task. Uploading a smaller image may help. Suggested maximum size is 2560 pixels.',
            'upload_failed' => 'Upload failed.',
            'big_upload_failed' => 'Please try uploading this file with the %1$sbrowser uploader%2$s.',
            'big_upload_queued' => '%s exceeds the maximum upload size for the multi-file uploader when used in your browser.',
            'io_error' => 'IO error.',
            'security_error' => 'Security error.',
            'file_cancelled' => 'File canceled.',
            'upload_stopped' => 'Upload stopped.',
            'dismiss' => 'Dismiss',
            'crunching' => 'Crunching…',
            'deleted' => 'moved to the Trash.',
            'error_uploading' => '“%s” has failed to upload.',
            'unsupported_image' => 'This image cannot be displayed in a web browser. For best results convert it to JPEG before uploading.',
            'noneditable_image' => 'This image cannot be processed by the web server. Convert it to JPEG or PNG before uploading.',
            'file_url_copied' => 'The file URL has been copied to your clipboard',
        ]);
        wp_localize_script('wp-plupload-js', '_wpPluploadSettings', [
            'defaults' => [
                'file_data_name' => 'async-upload',
                'url' => admin_url() . '/async-upload.php',
                'filters' => [
                    'max_file_size' => '104857600b',
                    'mime_types' => [
                        [ 'extensions' => 'jpg,jpeg,jpe,gif,png,bmp,tiff,tif,webp,avif,ico,heic,asf,asx,wmv,wmx,wm,avi,divx,flv,mov,qt,mpeg,mpg,mpe,mp4,m4v,ogv,webm,mkv,3gp,3gpp,3g2,3gp2,txt,asc,c,cc,h,srt,csv,tsv,ics,rtx,css,htm,html,vtt,dfxp,mp3,m4a,m4b,aac,ra,ram,wav,ogg,oga,flac,mid,midi,wma,wax,mka,rtf,js,pdf,class,tar,zip,gz,gzip,rar,7z,psd,xcf,doc,pot,pps,ppt,wri,xla,xls,xlt,xlw,mdb,mpp,docx,docm,dotx,dotm,xlsx,xlsm,xlsb,xltx,xltm,xlam,pptx,pptm,ppsx,ppsm,potx,potm,ppam,sldx,sldm,onetoc,onetoc2,onetmp,onepkg,oxps,xps,odt,odp,ods,odg,odc,odb,odf,wp,wpd,key,numbers,pages,svg,svgz' ],
                    ],
                ],
                'heic_upload_error' => true,
                'multipart_params' => [
                    'action' => 'upload-attachment',
                    '_wpnonce' => wp_create_nonce( 'media-form' ),
                ],
            ],
            'browser' => [
                'mobile' => false,
                'supported' => true,
            ],
            'limitExceeded' => false,
        ]);

        wp_enqueue_script( 'wp-mediaelement-js', includes_url( 'js/mediaelement/wp-mediaelement.min.js' ), true );
        wp_enqueue_script( 'wp-api-request-js', includes_url( 'js/api-request.min.js' ), true );
        wp_enqueue_script( 'wp-dom-ready-js', includes_url( 'js/dist/dom-ready.min.js' ), true );
        wp_enqueue_script( 'wp-a11y-js', includes_url( 'js/dist/a11y.min.js' ), true );
        wp_enqueue_script( 'clipboard-js', includes_url( 'js/clipboard.min.js' ), true, '2.0.11' );

        wp_enqueue_script( 'media-views-js', includes_url( 'js/media-views.min.js' ), true );
        wp_localize_script('media-views-js', '_wpMediaViewsL10n', [
            'mediaFrameDefaultTitle' => 'Media',
            'url' => 'URL',
            'addMedia' => 'Add media',
            'search' => 'Search',
            'select' => 'Select',
            'cancel' => 'Cancel',
            'update' => 'Update',
            'replace' => 'Replace',
            'remove' => 'Remove',
            'back' => 'Back',
            'selected' => '%d selected',
            'dragInfo' => 'Drag and drop to reorder media files.',
            'uploadFilesTitle' => 'Upload files',
            'uploadImagesTitle' => 'Upload images',
            'mediaLibraryTitle' => 'Media Library',
            'insertMediaTitle' => 'Add media',
            'returnToLibrary' => '← Go to library',
            'allMediaItems' => 'All media items',
            'allDates' => 'All dates',
            'noItemsFound' => 'No items found.',
            'unattached' => 'Unattached',
            'mine' => 'Mine',
            'trash' => 'Trash',
            'uploadedToThisPost' => 'Uploaded to this post',
            'warnDelete' => "You are about to permanently delete this item from your site.\nThis action cannot be undone.\n 'Cancel' to stop, 'OK' to delete.",
            'warnBulkDelete' => "You are about to permanently delete these items from your site.\nThis action cannot be undone.\n 'Cancel' to stop, 'OK' to delete.",
            'warnBulkTrash' => "You are about to trash these items.\n  'Cancel' to stop, 'OK' to delete.",
            'bulkSelect' => 'Bulk select',
            'trashSelected' => 'Move to Trash',
            'restoreSelected' => 'Restore from Trash',
            'deletePermanently' => 'Delete permanently',
            'errorDeleting' => 'Error in deleting the attachment.',
            'apply' => 'Apply',
            'filterByDate' => 'Filter by date',
            'filterByType' => 'Filter by type',
            'searchLabel' => 'Search media',
            'searchMediaLabel' => 'Search media',
            'searchMediaPlaceholder' => 'Search media items...',
            'mediaFound' => 'Number of media items found: %d',
            'noMedia' => 'No media items found.',
            'noMediaTryNewSearch' => 'No media items found. Try a different search.',
            'attachmentDetails' => 'Attachment details',
            'insertFromUrlTitle' => 'Insert from URL',
            'setFeaturedImageTitle' => 'Featured image',
            'setFeaturedImage' => 'Set featured image',
            'reverseOrder' => 'Reverse order',
            'imageDetailsTitle' => 'Image details',
            'imageReplaceTitle' => 'Replace image',
            'imageDetailsCancel' => 'Cancel edit',
            'editImage' => 'Edit image',
            'chooseImage' => 'Choose image',
            'selectAndCrop' => 'Select and crop',
            'skipCropping' => 'Skip cropping',
            'cropImage' => 'Crop image',
            'cropYourImage' => 'Crop your image',
            'cropping' => 'Cropping…',
            'suggestedDimensions' => 'Suggested image dimensions: %1$s by %2$s pixels.',
            'cropError' => 'There has been an error cropping your image.',
            'filterAttachments' => 'Filter media',
            'attachmentsList' => 'Media list',
            'settings' => [
                'tabs' => [],
                'tabUrl' => admin_url() . '/media-upload.php?chromeless=1',
                'mimeTypes' => [
                    'image' => 'Images',
                    'audio' => 'Audio',
                    'video' => 'Video',
                    'application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-word.document.macroEnabled.12,application/vnd.ms-word.template.macroEnabled.12,application/vnd.oasis.opendocument.text,application/vnd.apple.pages,application/pdf,application/vnd.ms-xpsdocument,application/oxps,application/rtf,application/wordperfect,application/octet-stream' => 'Documents',
                    'application/vnd.apple.numbers,application/vnd.oasis.opendocument.spreadsheet,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel.sheet.macroEnabled.12,application/vnd.ms-excel.sheet.binary.macroEnabled.12' => 'Spreadsheets',
                    'application/x-gzip,application/rar,application/x-tar,application/zip,application/x-7z-compressed' => 'Archives',
                ],
                'captions' => true,
                'nonce' => [
                    'sendToEditor' => '3989e12d35',
                    'setAttachmentThumbnail' => '426b8b2f28',
                ],
                'post' => [
                    'id' => 0,
                ],
                'defaultProps' => [
                    'link' => 'none',
                    'align' => '',
                    'size' => '',
                ],
                'attachmentCounts' => [
                    'audio' => 1,
                    'video' => 1,
                ],
                'oEmbedProxyUrl' => site_url() . '/wp-json/oembed/1.0/proxy',
                'embedExts' => [ 'mp3', 'ogg', 'flac', 'm4a', 'wav', 'mp4', 'm4v', 'webm', 'ogv', 'flv' ],
                'embedMimes' => [
                    'mp3' => 'audio/mpeg',
                    'ogg' => 'audio/ogg',
                    'flac' => 'audio/flac',
                    'm4a' => 'audio/mpeg',
                    'wav' => 'audio/wav',
                    'mp4' => 'video/mp4',
                    'm4v' => 'video/mp4',
                    'webm' => 'video/webm',
                    'ogv' => 'video/ogg',
                    'flv' => 'video/x-flv',
                ],
                'contentWidth' => null,
                'months' => [],
                'mediaTrash' => 0,
                'infiniteScrolling' => 0,
            ],
        ]);


        wp_enqueue_script( 'media-editor-js', includes_url( 'js/media-editor.min.js' ), true );
        wp_enqueue_script( 'media-audiovideo-js', includes_url( 'js/media-audiovideo.min.js' ), true );
        wp_enqueue_script( 'mce-view-js', includes_url( 'js/mce-view.min.js' ), true );
        wp_enqueue_script( 'imgareaselect-js', includes_url( 'js/imgareaselect/jquery.imgareaselect.min.js' ), true );
        wp_enqueue_script( 'image-edit-js', admin_url() . 'js/image-edit.min.js', true );
        wp_enqueue_script( 'heartbeat-js', includes_url( 'js/heartbeat.min.js' ), true );
        wp_enqueue_script( 'wp-api-js', includes_url( 'js/wp-api.min.js' ), true );
        wp_enqueue_script( 'wp-auth-check-js', includes_url( 'js/wp-auth-check.min.js' ), true );

        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script('dt_utilities_scripts_script', disciple_tools()->admin_js_url . 'dt-utilities-scripts.js', [
            'jquery',
            'wp-color-picker'
        ], filemtime( disciple_tools()->admin_js_path . 'dt-utilities-scripts.js' ), true);

        wp_enqueue_script('dt_admin_shared_script', disciple_tools()->admin_js_url . 'dt-shared.js', [
            'jquery',
            'wp-color-picker'
        ], true);

        wp_localize_script('dt_options_script-js', 'dtOptionAPI', [
            'root' => site_url() . '/wp-json/',
            'nonce' => wp_create_nonce( 'wp_rest' ),
            'current_user_login' => wp_get_current_user()->user_login ?? '',
            'current_user_id' => get_current_user_id(),
            'theme_uri' => get_template_directory_uri(),
            'images_uri' => get_template_directory_uri() . '/dt-core/admin/img/',
        ]);

        wp_enqueue_script('dt_options_script', disciple_tools()->admin_js_url . 'dt-options.js', [
            'jquery',
            'wp-color-picker'
        ], true);

        wp_localize_script(
            'dt_options_script', 'dt_admin_scripts', [
                'site_url' => site_url(),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'rest_root' => esc_url_raw( rest_url() ),
                'upload' => [
                    'title' => __( 'Upload Icon', 'disciple_tools' ),
                    'button_txt' => __( 'Upload', 'disciple_tools' )
                ]
            ]
        );

        wp_enqueue_script('icon-selector', '/wp-content/plugins/dt-home/resources/js/icon-selector.js', [
            'jquery',
            'jquery-ui-core',
            'jquery-ui-sortable',
            'jquery-ui-dialog',
            'lodash',
            'jquery-ui-js',
        ], true);

        enqueue_asset(
            plugin_path( '/dist' ),
            'resources/js/admin.js',
            [
                'handle' => 'bible-plugin-admin',
                'css-media' => 'all', // Optional.
                'css-only' => false, // Optional. Set to true to only load style assets in production mode.
                'in-footer' => false, // Optional. Defaults to false.
            ]
        );

        wp_register_script( 'jquery-ui-js', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js', true, '1.12.1' );
        wp_enqueue_script( 'jquery-ui-js' );
        wp_register_style( 'jquery-ui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css' );
        wp_enqueue_style( 'jquery-ui' );

        wp_register_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css' );
        wp_enqueue_style( 'font-awesome' );

        dt_theme_enqueue_style( 'material-font-icons-local', 'dt-core/dependencies/mdi/css/materialdesignicons.min.css', [] );
        wp_enqueue_style( 'material-font-icons', 'https://cdn.jsdelivr.net/npm/@mdi/font@6.6.96/css/materialdesignicons.min.css' );

        wp_print_media_templates();
    }

    /**
     * Outputs the CSS style for cloaking elements.
     *
     * This method outputs the necessary CSS style declaration for cloaking elements
     * in the HTML markup. The style declaration hides the elements by setting the
     * "display" property to "none". This method should be called within the HTML
     * document where cloaking is required.
     *
     * @return void
     */
    public function cloak_style(): void
    {
        ?>
        <style>
            .cloak {
                visibility: hidden;
            }
        </style>
        <?php
    }

    /**
     * Outputs Open Graph meta tags.
     *
     * This method outputs the necessary Open Graph meta tags for the HTML document.
     *
     * @return void
     */
    public function add_open_graph_meta_tags(): void
    {
        $custom_logo = get_plugin_option( 'custom_ministry_logo' );
        $default_logo = plugin_url( 'resources/img/logo-color.png' );
        ?>
        <meta property="og:type" content="website" />
        <meta property="og:title" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
        <meta property="og:url" content="<?php echo esc_url( get_plugin_option( 'dt_home_plugin_url' ) ); ?>" />
        <meta property="og:image"
              content="<?php echo esc_url( !empty( $custom_logo ) ? $custom_logo : $default_logo ); ?>" />
        <meta name="color-scheme" content="light dark">
        <?php
    }
}
