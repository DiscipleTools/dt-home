<?php

namespace DT\Home\Controllers\MagicLink;

use DT\Home\GuzzleHttp\Psr7\ServerRequest as Request;
use DT\Home\Psr\Http\Message\ResponseInterface;
use DT\Home\Sources\Trainings;
use function DT\Home\template;

class TrainingController
{
    private Trainings $trainings;

    public function __construct( Trainings $trainings )
    {
        $this->trainings = $trainings;
    }

    /**
     * Retrieves all training data and returns it as a JSON response.
     *
     * @return ResponseInterface
     */
    public function show( Request $request, $params )
    {
        $key = $params['key'];
        $training_data = array_values( $this->trainings->all() );
        $data = json_encode( $training_data );
        $training_data_json_escaped = htmlspecialchars( $data );
        $page_title = __( 'Training', 'dt-home' );

        return template('training', compact(
            'data',
            'training_data',
            'training_data_json_escaped',
            'page_title',
            'key'
        ));
    }

    /**
     * Get all training data.
     *
     * This method retrieves the apps array from the 'trainings' option and sorts it based on the 'sort' key.
     *
     * @return array The sorted trainings array.
     */
    protected function get_all_trainings_data()
    {
        // Get the apps array from the option
        $trainings_array = get_plugin_option( 'trainings' );

        // Sort the array based on the 'sort' key
        usort($trainings_array, function ( $a, $b ) {
            return ( !empty( $a['sort'] ) && !empty( $b['sort'] ) ) ? ( $a['sort'] - $b['sort'] ) : -1;
        });

        return $trainings_array;
    }
}
