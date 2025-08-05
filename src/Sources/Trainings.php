<?php

namespace DT\Home\Sources;

use function DT\Home\get_plugin_option;
use function DT\Home\set_plugin_option;

class Trainings extends Source
{

    public function find_key()
    {
        return 'id';
    }

    /**
     * Retrieves the raw array of training videos.
     *
     * @return array The raw array of training videos.
     */
    public function raw( array $params = [] ): array
    {
        $raw_trainings = array_values( get_plugin_option( 'trainings', [] ) );

        if ( !is_array( $raw_trainings ) ) {
            $raw_trainings = [];
        }

        return $raw_trainings;
    }

    /**
     * Save training videos.
     *
     * @param array $apps The training videos to be saved.
     * @param array $options
     * @return bool Whether the saving was successful or not.
     */
    public function save( $apps, array $options = [] ): bool
    {
        return set_plugin_option( 'trainings', $apps );
    }

    /**
     * Retrieves all coded training videos.
     *
     * @return array All training videos data.
     */
    public function format_item( array $item ): array
    {
        $overrides = [];

        return array_merge([
            'id' => '',
            'name' => '',
            'embed_video' => '',
            'anchor' => '',
            'sort' => '',
        ], $item, $overrides);
    }
}
