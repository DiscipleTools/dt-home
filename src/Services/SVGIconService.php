<?php

namespace DT\Home\Services;

class SVGIconService
{
    private $svg_dir_path;

    public function __construct( $svg_dir_path )
    {
        $this->svg_dir_path = $svg_dir_path;
    }

    public function get_svg_icon_urls()
    {
        $svg_dir_path = get_template_directory() . '/dt-assets/images/';

        // Check if the directory exists
        if ( is_dir( $svg_dir_path ) ) {
            // Read files from the directory
            $svg_files = array_diff( scandir( $svg_dir_path ), [ '..', '.' ] );

            // Filter out only SVG files
            $svg_icon_urls = array_filter($svg_files, function ( $file ) use ( $svg_dir_path ) {
                return pathinfo( $svg_dir_path . $file, PATHINFO_EXTENSION ) === 'svg';
            });

            // Convert file paths to URLs
            $svg_icon_urls = array_map(function ( $file ) {
                // Use get_template_directory_uri() to convert the file path to a URL
                return get_template_directory_uri() . '/dt-assets/images/' . $file;
            }, $svg_icon_urls);

            // Return the SVG URLs as JSON response
            return json_encode( array_values( $svg_icon_urls ) );
        } else {
            // Directory not found, handle this case appropriately
            return json_encode( [] );
        }
    }
}
